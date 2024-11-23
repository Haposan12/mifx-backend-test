<?php

// @TODO implement
if (!function_exists('usd_to_rupiah_format')) {
    function usd_to_rupiah_format($usd)
    {
        $rupiah = $usd * 14000;
        return 'Rp ' . number_format($rupiah, 0, ',', '.'). ',00';
    }
}
