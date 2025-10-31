<?php

if (!function_exists('terbilang')) {
    /**
     * Convert number to Indonesian words
     * 
     * @param float|int $number
     * @return string
     */
    function terbilang($number)
    {
        $number = abs($number);
        $words = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $temp = '';

        if ($number < 12) {
            $temp = ' ' . $words[$number];
        } elseif ($number < 20) {
            $temp = terbilang($number - 10) . ' Belas';
        } elseif ($number < 100) {
            $temp = terbilang(floor($number / 10)) . ' Puluh ' . terbilang($number % 10);
        } elseif ($number < 200) {
            $temp = ' Seratus ' . terbilang($number - 100);
        } elseif ($number < 1000) {
            $temp = terbilang(floor($number / 100)) . ' Ratus ' . terbilang($number % 100);
        } elseif ($number < 2000) {
            $temp = ' Seribu ' . terbilang($number - 1000);
        } elseif ($number < 1000000) {
            $temp = terbilang(floor($number / 1000)) . ' Ribu ' . terbilang($number % 1000);
        } elseif ($number < 1000000000) {
            $temp = terbilang(floor($number / 1000000)) . ' Juta ' . terbilang($number % 1000000);
        } elseif ($number < 1000000000000) {
            $temp = terbilang(floor($number / 1000000000)) . ' Milyar ' . terbilang(fmod($number, 1000000000));
        } elseif ($number < 1000000000000000) {
            $temp = terbilang(floor($number / 1000000000000)) . ' Triliun ' . terbilang(fmod($number, 1000000000000));
        }

        return trim($temp);
    }
}

if (!function_exists('format_rupiah')) {
    /**
     * Format number to Rupiah currency
     * 
     * @param float|int $number
     * @param bool $prefix
     * @return string
     */
    function format_rupiah($number, $prefix = true)
    {
        $formatted = number_format($number, 0, ',', '.');
        return $prefix ? 'Rp ' . $formatted : $formatted;
    }
}
