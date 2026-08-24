<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Date;
use function PHPStan\Testing\assertType;

assertType('Boron\Carbon', Date::parse('2024-03-20'));
assertType('Boron\Carbon', Date::now());
assertType('Boron\Carbon', Date::today());
assertType('Boron\Carbon', Date::create(2024, 3, 20));
assertType('Boron\CalendarDate', Date::parse('2024-03-20')->toJalali());
assertType('int', Date::parse('2024-03-20')->calendarYear);

assertType('Boron\Carbon', Date::fromJalali(1403, 1, 1));
assertType('Boron\Carbon', Date::fromHijri(1445, 9, 10));
assertType('Boron\Carbon', Date::fromCalendar('jalali', 1403, 1, 1));
assertType('Boron\Carbon', Date::parseFromCalendar('jalali', '1403/01/01'));

assertType('Boron\Carbon|null', Date::make('2024-03-20'));
assertType('Boron\Carbon|null', Date::getTestNow());
assertType('Boron\Carbon|false|null', Date::createFromFormat('Y-m-d', '2024-03-20'));

assertType('Boron\Carbon', now());
assertType('Boron\Carbon', today());
assertType('Boron\CalendarDate', now()->toJalali());

function (SupportCarbon $createdAt): void {
    assertType('Boron\CalendarDate', $createdAt->toJalali());
    assertType('int', $createdAt->calendarYear);
    assertType('Boron\CalendarDate', $createdAt->calendarDate);
    assertType('string', $createdAt->calendarMonthName);
    assertType('string', $createdAt->calendarFormat('Y/m/d', 'jalali'));
};

function (CarbonImmutable $publishedAt): void {
    assertType('Boron\CalendarDate', $publishedAt->toJalali());
    assertType('int', $publishedAt->calendarYear);
};
