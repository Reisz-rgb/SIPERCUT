<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupervisorSeeder extends Seeder
{
    public function run(): void
    {
        $supervisors = [
            [
                'nama'       => 'BUDI RIYANTO, S.PD, M.Pd.',
                'nip'        => '197909022006041005',
                'jabatan'    => 'Sekretaris',
                'unit_kerja' => 'Sekretariat',
            ],
            [
                'nama'       => 'Yashinta Mukti Wardhani, S.E.',
                'nip'        => '198304282010012035',
                'jabatan'    => 'Kepala Subbagian Umum dan Kepegawaian',
                'unit_kerja' => 'Sekretariat',
            ],
            [
                'nama'       => 'MOHAMAD ANDHI KURNIAWAN, S.E, M.M.',
                'nip'        => '198402192011011008',
                'jabatan'    => 'Kepala Subbagian Keuangan',
                'unit_kerja' => 'Sekretariat',
            ],
            [
                'nama'       => 'KARTI SETYANINGSIH, S.H, M.M.',
                'nip'        => '196806091990122002',
                'jabatan'    => 'Kepala Subbagian Perencanaan',
                'unit_kerja' => 'Sekretariat',
            ],
            [
                'nama'       => 'SULARSIH, S.Pd, M.M.',
                'nip'        => '197505122008012018',
                'jabatan'    => 'Kepala Bidang Pendidikan dan Tenaga Kependidikan (PTK)',
                'unit_kerja' => 'Bidang PTK',
            ],
            [
                'nama'       => 'AGUNG GALIH HARDIYANTO, ST.',
                'nip'        => '197711082010011008',
                'jabatan'    => 'Kepala Bidang Pendidikan Dasar',
                'unit_kerja' => 'Bidang Dikdas',
            ],
            [
                'nama'       => 'NURZAKA SRI WANDANSARI, S.Pd, M.Pd.',
                'nip'        => '197101061998022003',
                'jabatan'    => 'Kepala Bidang PAUD dan DIKMAS',
                'unit_kerja' => 'Bidang PAUD dan DIKMAS',
            ],
            [
                'nama'       => 'SISILIA INDUN MAWARTI, S.S',
                'nip'        => '197811082006042008',
                'jabatan'    => 'Kepala Bidang Kebudayaan',
                'unit_kerja' => 'Bidang Kebudayaan',
            ],
            [
                'nama'       => 'SISKA NUGRAHENI, SE., M.H.',
                'nip'        => '198112282002122001',
                'jabatan'    => 'Kepala Bidang Pemuda dan Olahraga',
                'unit_kerja' => 'Bidang Pemuda dan Olahraga',
            ],
        ];

        foreach ($supervisors as $supervisor) {
            DB::table('supervisors')->updateOrInsert(
                ['nip' => $supervisor['nip']],
                array_merge($supervisor, [
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}