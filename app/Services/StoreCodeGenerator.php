<?php

namespace App\Services;

use App\Models\Store;

class StoreCodeGenerator
{
    public static function generate(string $name): string
    {
        // Prefix from name
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 3));

        if (strlen($prefix) < 3) {
            $prefix = str_pad($prefix, 3, 'X');
        }

        // Get last store ID (global serial)
        $lastId = Store::withTrashed()->max('id') ?? 0;
        $nextNumber = str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);

        return "{$prefix}-{$nextNumber}";
    }
}
