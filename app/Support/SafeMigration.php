<?php

namespace App\Support;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

abstract class SafeMigration extends Migration
{
    /**
     * Buat tabel hanya jika belum ada.
     * Drop diganti dropIfExists agar down() juga aman.
     */
    protected function safeCreateTable(string $table, callable $callback): void
    {
        if (!Schema::hasTable($table)) {
            Schema::create($table, $callback);
        }
    }

    /**
     * Tambah satu kolom jika belum ada di tabel.
     */
    protected function safeAddColumn(string $table, string $column, callable $callback): void
    {
        if (Schema::hasTable($table) && !Schema::hasColumn($table, $column)) {
            Schema::table($table, $callback);
        }
    }

    /**
     * Tambah banyak kolom sekaligus, masing-masing dicek individual.
     * $columns format: ['nama_kolom' => callable]
     */
    protected function safeAddColumns(string $table, array $columns): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $existingColumns = Schema::getColumnListing($table);

        $columnsToAdd = array_filter(
            $columns,
            fn($colName) => !in_array($colName, $existingColumns),
            ARRAY_FILTER_USE_KEY
        );

        if (empty($columnsToAdd)) {
            return;
        }

        Schema::table($table, function ($table) use ($columnsToAdd) {
            foreach ($columnsToAdd as $callback) {
                $callback($table);
            }
        });
    }

    /**
     * Drop kolom hanya jika ada (untuk down() yang aman).
     */
    protected function safeDropColumns(string $table, array $columns): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $columnsToDrop = array_filter(
            $columns,
            fn($col) => Schema::hasColumn($table, $col)
        );

        if (!empty($columnsToDrop)) {
            Schema::table($table, function ($blueprint) use ($columnsToDrop) {
                $blueprint->dropColumn(array_values($columnsToDrop));
            });
        }
    }
}