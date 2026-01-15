<?php

if (!function_exists('format_phone')) {
    function format_phone(?string $raw): string
    {
        if (!$raw) {
            return '';
        }

        // Strip everything except digits
        $digits = preg_replace('/\D+/', '', $raw);

        // Handle leading 1 for US numbers
        if (strlen($digits) === 11 && $digits[0] === '1') {
            $digits = substr($digits, 1);
        }

        // Only format 10‑digit numbers
        if (strlen($digits) !== 10) {
            return $raw;
        }

        $area   = substr($digits, 0, 3);
        $prefix = substr($digits, 3, 3);
        $line   = substr($digits, 6, 4);

        return sprintf('(%s) %s - %s', $area, $prefix, $line);
    }
}