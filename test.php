<?php

require_once './vendor/autoload.php';

use Boron\Carbon;

echo Carbon::parse('2024-03-20')->toJalali().PHP_EOL;          // 1403-01-01 (Nowruz!)
echo Carbon::parse('2024-03-20')->toHijri().PHP_EOL;           // 1445-09-10 (Ramadan 10)
echo Carbon::fromJalali(1403, 5, 19)->toDateString().PHP_EOL;  // 2024-08-09

echo Carbon::now()->calendarFormat('l j F Y', 'jalali', 'fa', true).PHP_EOL;
