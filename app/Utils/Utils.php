<?php

use Carbon\Carbon;

function formatPrice(float $num)
{
    return number_format($num, 0, ',', '.');
}

function formatDate($date, $withHour = true)
{
    if (!$withHour) {
        return Carbon::parse($date)->translatedFormat('d M Y');
    }

    return Carbon::parse($date)->translatedFormat('d M Y H:i:s');
}