<?php

declare(strict_types=1);

namespace Boron;

use Boron\Calendars\CalendarInterface;
use Boron\Exceptions\InvalidCalendarDateException;
use Boron\Support\Digits;
use Boron\Support\WeekDays;
use DateTimeZone;
use JsonSerializable;
use Stringable;

/**
 * An immutable year/month/day triple expressed in a specific calendar.
 *
 * This is what you get when you look at a Boron instance "through" a
 * calendar, e.g. `Boron::now()->toJalali()`.
 */
final class CalendarDate implements Stringable, JsonSerializable
{
    public static function fromJulianDayNumber(CalendarInterface $calendar, int $julianDayNumber): self
    {
        [$year, $month, $day] = $calendar->fromJulianDayNumber($julianDayNumber);

        return new self($calendar, $year, $month, $day);
    }

    public function __construct(
        public readonly CalendarInterface $calendar,
        public readonly int $year,
        public readonly int $month,
        public readonly int $day,
    ) {
        if (!$calendar->isValidDate($year, $month, $day)) {
            throw InvalidCalendarDateException::forDate($calendar->getName(), $year, $month, $day);
        }
    }

    public function __toString(): string
    {
        return sprintf('%04d-%02d-%02d', $this->year, $this->month, $this->day);
    }

    public function toJulianDayNumber(): int
    {
        return $this->calendar->toJulianDayNumber($this->year, $this->month, $this->day);
    }

    /**
     * Re-express this date in another calendar.
     */
    public function to(string|CalendarInterface $calendar): self
    {
        $calendar = CalendarRegistry::resolve($calendar);

        if ($calendar === $this->calendar) {
            return $this;
        }

        return self::fromJulianDayNumber($calendar, $this->toJulianDayNumber());
    }

    public function toBoron(DateTimeZone|string|null $timezone = null): Boron
    {
        return Boron::fromCalendar($this->calendar, $this->year, $this->month, $this->day, 0, 0, 0, $timezone);
    }

    public function toBoronImmutable(DateTimeZone|string|null $timezone = null): BoronImmutable
    {
        return BoronImmutable::fromCalendar($this->calendar, $this->year, $this->month, $this->day, 0, 0, 0, $timezone);
    }

    /**
     * Same as toBoron() but returns the Carbon-subclass flavor.
     */
    public function toCarbon(DateTimeZone|string|null $timezone = null): Carbon
    {
        return Carbon::fromCalendar($this->calendar, $this->year, $this->month, $this->day, 0, 0, 0, $timezone);
    }

    public function toCarbonImmutable(DateTimeZone|string|null $timezone = null): CarbonImmutable
    {
        return CarbonImmutable::fromCalendar($this->calendar, $this->year, $this->month, $this->day, 0, 0, 0, $timezone);
    }

    public function getMonthName(string $locale = 'en'): string
    {
        return $this->calendar->getMonthName($this->month, $locale);
    }

    public function isLeapYear(): bool
    {
        return $this->calendar->isLeapYear($this->year);
    }

    public function daysInMonth(): int
    {
        return $this->calendar->daysInMonth($this->year, $this->month);
    }

    public function daysInYear(): int
    {
        return $this->calendar->daysInYear($this->year);
    }

    public function dayOfYear(): int
    {
        return $this->calendar->dayOfYear($this->year, $this->month, $this->day);
    }

    /**
     * Day of week: 0 = Monday ... 6 = Sunday.
     */
    public function dayOfWeek(): int
    {
        return $this->toJulianDayNumber() % 7;
    }

    public function equalTo(self $other): bool
    {
        return $this->toJulianDayNumber() === $other->toJulianDayNumber();
    }

    public function addDays(int $days): self
    {
        return self::fromJulianDayNumber($this->calendar, $this->toJulianDayNumber() + $days);
    }

    /**
     * Format the date using PHP date() style tokens.
     *
     * Supported tokens: Y y m n d j t L z S F M (month names), D l N w
     * (week days). A backslash escapes the next character. When $localizeDigits
     * is true, digits are rendered using the digits of the locale.
     */
    public function format(string $format, string $locale = 'en', bool $localizeDigits = false): string
    {
        $output = '';
        $length = \strlen($format);

        for ($i = 0; $i < $length; ++$i) {
            $char = $format[$i];

            if ('\\' === $char) {
                $output .= $i + 1 < $length ? $format[++$i] : '';

                continue;
            }

            $output .= match ($char) {
                'Y' => (string) $this->year,
                'y' => str_pad((string) ($this->year % 100), 2, '0', STR_PAD_LEFT),
                'm' => str_pad((string) $this->month, 2, '0', STR_PAD_LEFT),
                'n' => (string) $this->month,
                'd' => str_pad((string) $this->day, 2, '0', STR_PAD_LEFT),
                'j' => (string) $this->day,
                't' => (string) $this->daysInMonth(),
                'L' => $this->isLeapYear() ? '1' : '0',
                'z' => (string) ($this->dayOfYear() - 1),
                'S' => $this->ordinalSuffix($this->day),
                'F' => $this->getMonthName($locale),
                'M' => $this->shortMonthName($locale),
                'l' => WeekDays::name($this->dayOfWeek(), $locale),
                'D' => WeekDays::shortName($this->dayOfWeek(), $locale),
                'N' => (string) ($this->dayOfWeek() + 1),
                'w' => (string) (($this->dayOfWeek() + 1) % 7),
                default => $char,
            };
        }

        return $localizeDigits ? Digits::localize($output, $locale) : $output;
    }

    /**
     * @return array{calendar: string, year: int, month: int, day: int}
     */
    public function toArray(): array
    {
        return [
            'calendar' => $this->calendar->getName(),
            'year' => $this->year,
            'month' => $this->month,
            'day' => $this->day,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    private function shortMonthName(string $locale): string
    {
        $name = $this->getMonthName($locale);

        return str_starts_with($locale, 'en') ? substr($name, 0, 3) : $name;
    }

    private function ordinalSuffix(int $number): string
    {
        if ($number % 100 >= 11 && $number % 100 <= 13) {
            return 'th';
        }

        return match ($number % 10) {
            1 => 'st',
            2 => 'nd',
            3 => 'rd',
            default => 'th',
        };
    }
}
