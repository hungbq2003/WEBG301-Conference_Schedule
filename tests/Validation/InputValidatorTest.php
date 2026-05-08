<?php

namespace App\Tests\Validation;

use App\Validation\InputValidator;
use PHPUnit\Framework\TestCase;

class InputValidatorTest extends TestCase
{
    // ---------- validateConferenceDates ----------

    public function testValidConferenceDateRangePasses(): void
    {
        self::assertSame([], InputValidator::validateConferenceDates('2026-06-01', '2026-06-03'));
    }

    public function testSameStartAndEndDateIsAllowed(): void
    {
        self::assertSame([], InputValidator::validateConferenceDates('2026-06-01', '2026-06-01'));
    }

    public function testEndDateBeforeStartDateFails(): void
    {
        $errors = InputValidator::validateConferenceDates('2026-06-10', '2026-06-01');
        self::assertNotEmpty($errors);
        self::assertStringContainsString('End date must be on or after', $errors[0]);
    }

    public function testMissingDatesFail(): void
    {
        self::assertNotEmpty(InputValidator::validateConferenceDates(null, '2026-06-01'));
        self::assertNotEmpty(InputValidator::validateConferenceDates('2026-06-01', null));
        self::assertNotEmpty(InputValidator::validateConferenceDates('', ''));
        self::assertNotEmpty(InputValidator::validateConferenceDates('   ', '2026-06-01'));
    }

    public function testMalformedDateFails(): void
    {
        $errors = InputValidator::validateConferenceDates('not-a-date', '2026-06-01');
        self::assertNotEmpty($errors);
        self::assertStringContainsString('valid dates', $errors[0]);
    }

    // ---------- validateSessionTimes ----------

    public function testValidSessionTimeRangePasses(): void
    {
        self::assertSame([], InputValidator::validateSessionTimes('2026-06-01 09:00', '2026-06-01 10:30'));
    }

    public function testSameStartAndEndTimeFails(): void
    {
        $errors = InputValidator::validateSessionTimes('2026-06-01 09:00', '2026-06-01 09:00');
        self::assertNotEmpty($errors);
        self::assertStringContainsString('strictly after', $errors[0]);
    }

    public function testEndTimeBeforeStartTimeFails(): void
    {
        $errors = InputValidator::validateSessionTimes('2026-06-01 10:00', '2026-06-01 09:00');
        self::assertNotEmpty($errors);
    }

    public function testMissingTimesFail(): void
    {
        self::assertNotEmpty(InputValidator::validateSessionTimes(null, '2026-06-01 10:00'));
        self::assertNotEmpty(InputValidator::validateSessionTimes('', ''));
    }

    public function testMalformedTimeFails(): void
    {
        $errors = InputValidator::validateSessionTimes('garbage', '2026-06-01 10:00');
        self::assertNotEmpty($errors);
    }

    // ---------- validateCapacity ----------

    public function testPositiveCapacityPasses(): void
    {
        self::assertSame([], InputValidator::validateCapacity(1));
        self::assertSame([], InputValidator::validateCapacity('100'));
        self::assertSame([], InputValidator::validateCapacity(500));
    }

    public function testZeroCapacityFails(): void
    {
        $errors = InputValidator::validateCapacity(0);
        self::assertNotEmpty($errors);
        self::assertStringContainsString('positive integer', $errors[0]);
    }

    public function testNegativeCapacityFails(): void
    {
        self::assertNotEmpty(InputValidator::validateCapacity(-50));
        self::assertNotEmpty(InputValidator::validateCapacity('-10'));
    }

    public function testNonNumericCapacityFails(): void
    {
        self::assertNotEmpty(InputValidator::validateCapacity('abc'));
        self::assertNotEmpty(InputValidator::validateCapacity('100abc'));
    }

    public function testMissingCapacityFails(): void
    {
        self::assertNotEmpty(InputValidator::validateCapacity(null));
        self::assertNotEmpty(InputValidator::validateCapacity(''));
    }
}
