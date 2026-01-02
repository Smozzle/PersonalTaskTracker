<?php
// app/helpers.php

if (!function_exists('getColorName')) {
    function getColorName(?string $colorCode): string
    {
        if (empty($colorCode)) {
            return 'None';
        }

        // Common mappings (you can expand this list)
        $map = [
            '#ff5733' => 'Orange Red',
            '#33ff57' => 'Lime Green',
            '#3357ff' => 'Royal Blue',
            '#ffd700' => 'Gold',
            '#06b6d4' => 'Cyan',
            '#f43f5e' => 'Rose',
            '#a78bfa' => 'Lavender',
            '#9ca3af' => 'Gray',
        ];

        $key = strtolower(trim($colorCode));
        return $map[$key] ?? $colorCode; // fallback to showing hex if unknown
    }
}
