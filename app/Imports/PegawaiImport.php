<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;

class PegawaiImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];
        for ($i = 0; $i < 11; $i++) {
            $sheets[$i] = new PegawaiSheetImport($i);
        }
        return $sheets;
    }
}

class PegawaiSheetImport implements ToCollection
{
    private $sheetIndex;
    private $imported = 0;
    private $skipped = 0;

    public function __construct($sheetIndex)
    {
        $this->sheetIndex = $sheetIndex;
    }

    public function collection(Collection $rows)
    {
        echo "\n=== Processing Sheet {$this->sheetIndex} ===\n";
        
        if ($rows->isEmpty()) {
            echo "Sheet kosong, skip.\n";
            return;
        }

        // Cari header row
        $headerRowIndex = $this->findHeaderRow($rows);
        
        if ($headerRowIndex === null) {
            echo "Header tidak ditemukan, coba anggap baris pertama sebagai header\n";
            $headerRowIndex = 0;
        } else {
            echo "Header ditemukan di baris " . ($headerRowIndex + 1) . "\n";
        }

        // Ambil header
        $headers = $rows[$headerRowIndex];
        
        // IMPORTANT: Print header asli untuk debug
        echo "Header asli: " . json_encode($headers->toArray(), JSON_UNESCAPED_UNICODE) . "\n";
        
        $columnMap = $this->mapColumns($headers);
        
        echo "Mapping kolom:\n";
        foreach ($columnMap as $field => $index) {
            if ($index !== null) {
                echo "  - {$field} => kolom {$index} ({$headers[$index]})\n";
            }
        }

        // Process data rows
        for ($i = $headerRowIndex + 1; $i < $rows->count(); $i++) {
            $row = $rows[$i];
            
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $this->importRow($row, $columnMap, $i + 1);
        }

        echo "Sheet {$this->sheetIndex}: Imported={$this->imported}, Skipped={$this->skipped}\n";
    }

    /**
     * Cari baris header dengan lebih pintar
     */
    private function findHeaderRow(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $nonEmptyCells = $row->filter(fn($cell) => !empty($cell))->count();
            
            // Header biasanya punya banyak kolom terisi
            if ($nonEmptyCells >= 3) {
                $rowString = strtolower(implode('|', $row->toArray()));
                
                if (
                    str_contains($rowString, 'nip') || 
                    str_contains($rowString, 'nama') ||
                    str_contains($rowString, 'no')
                ) {
                    return $index;
                }
            }
        }
        return null;
    }

    /**
     * Map kolom Excel ke field database dengan fuzzy matching
     */
    private function mapColumns(Collection $headers): array
    {
        $map = [
            'no' => null,
            'nama' => null,
            'nip' => null,
            'gender' => null,
            'pangkat_golongan' => null,
            'bidang_unit' => null,
            'jabatan' => null,
            'usia' => null,
            'pendidikan' => null,
            'phone' => null,
        ];

        foreach ($headers as $index => $header) {
            if (empty($header)) continue;

            $h = strtolower(trim($header));
            $h = preg_replace('/[\s\/\-.]+/', '_', $h);

            if ($this->matches($h, ['no', 'nomor'])) $map['no'] = $index;
            elseif ($this->matches($h, ['nama'])) $map['nama'] = $index;
            elseif ($this->matches($h, ['nip'])) $map['nip'] = $index;
            elseif ($this->matches($h, ['gender','jk','jenis_kelamin','l_p'])) $map['gender'] = $index;
            elseif ($this->matches($h, ['pangkat','golongan','gol'])) $map['pangkat_golongan'] = $index;
            elseif ($this->matches($h, ['bidang','unit','divisi','bagian','seksi'])) $map['bidang_unit'] = $index;
            elseif ($this->matches($h, ['jabatan','posisi'])) $map['jabatan'] = $index;
            elseif ($this->matches($h, ['usia','umur'])) $map['usia'] = $index;
            elseif ($this->matches($h, ['pendidikan','pend'])) $map['pendidikan'] = $index;
            elseif ($this->matches($h, ['hp','phone','telp','wa'])) $map['phone'] = $index;
        }

        return $map;
    }

    private function cell(Collection $row, ?int $index)
    {
        return ($index !== null && isset($row[$index]))
            ? trim((string)$row[$index])
            : null;
    }


    /**
     * Helper untuk fuzzy matching
     */
    private function matches($value, array $patterns)
    {
        foreach ($patterns as $pattern) {
            if (str_contains($value, $pattern)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Cek apakah row kosong
     */
    private function isEmptyRow(Collection $row)
    {
        return $row->filter(fn($cell) => !empty($cell))->isEmpty();
    }

    /**
     * Import single row menggunakan column map
     */
   private function importRow(Collection $row, array $map, int $rowNumber)
    {
        $nip = $this->digits($this->cell($row, $map['nip']));

        if (!$nip || strlen($nip) < 5) {
            echo "Row {$rowNumber}: ✗ NIP tidak valid, skip.\n";
            $this->skipped++;
            return;
        }

        if (User::where('nip', $nip)->exists()) {
            echo "Row {$rowNumber}: ✗ NIP {$nip} sudah ada, skip.\n";
            $this->skipped++;
            return;
        }

        $nama = $this->cell($row, $map['nama']) ?? 'Tanpa Nama';

        User::create([
            'name'             => $nama,
            'nip'              => $nip,
            'phone'            => $this->generatePhone($this->cell($row, $map['phone']), $nip),
            'password'         => Hash::make($nip),
            'role'             => 'user',
            'gender'           => $this->parseGender($this->cell($row, $map['gender'])),
            'pangkat_golongan' => $this->cell($row, $map['pangkat_golongan']),
            'bidang_unit'      => $this->cell($row, $map['bidang_unit']),
            'jabatan'          => $this->cell($row, $map['jabatan']),
            'pendidikan'       => $this->cell($row, $map['pendidikan']),
            'usia'             => $this->parseUsia($this->cell($row, $map['usia'])),
            'status'           => 'aktif',
            'annual_leave_quota' => 12,
            'join_date'        => now()->subYears(rand(1, 10)),
        ]);

        echo "Row {$rowNumber}: ✓ {$nama}\n";
        $this->imported++;
    }

    private function digits(?string $value): string
    {
        if ($value === null) return '';
        return preg_replace('/[^0-9]/', '', $value);
    }

    private function generatePhone($phoneData, string $nip)
    {
        if (!empty($phoneData)) {
            $phone = preg_replace('/[^0-9]/', '', $phoneData); // FIX: was [^0-11]

            if (strlen($phone) >= 10 && strlen($phone) <= 15) {
                if (!str_starts_with($phone, '0')) {
                    $phone = '0' . $phone;
                }

                if (!User::where('phone', $phone)->exists()) {
                    return $phone;
                }
            }
        }

        $suffix = substr($nip, -8);
        $phone  = '0812' . str_pad($suffix, 8, '0', STR_PAD_LEFT);

        $counter      = 1;
        $originalPhone = $phone;
        while (User::where('phone', $phone)->exists()) {
            $phone = substr($originalPhone, 0, -1) . $counter;
            $counter++;
        }

        return $phone;
    }

    /**
     * Parse gender
     */
    private function parseGender($value)
    {
        if (empty($value)) return null;

        $value = strtoupper(trim($value));

        if (in_array($value, ['L', 'LAKI', 'LAKI-LAKI', 'LAKI LAKI', 'M', 'MALE', 'PRIA', '1'])) {
            return 'L';
        }

        if (in_array($value, ['P', 'PEREMPUAN', 'PR', 'F', 'FEMALE', 'WANITA', '2'])) {
            return 'P';
        }

        return null;
    }

    /**
     * Parse usia
     */
    private function parseUsia($value): ?int
    {
        $age = (int)$this->digits($value);
        return ($age > 0 && $age < 100) ? $age : null;
    }
}