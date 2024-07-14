<?php

if (!function_exists('maskString')) {
    function maskString($string, $start = 1, $end = 1) {
        $length = strlen($string);
        if ($length <= ($start + $end)) {
            return str_repeat('*', $length);
        }

        return substr($string, 0, $start) . str_repeat('*', $length - $start - $end) . substr($string, -$end);
    }
}
