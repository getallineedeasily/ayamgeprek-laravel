<?php

function formatPrice(float $num)
{
    return number_format($num, 0, ',', '.');
}