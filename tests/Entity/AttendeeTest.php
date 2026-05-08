<?php

namespace App\Tests\Entity;

use App\Entity\Attendee;
use App\Entity\Session;
use PHPUnit\Framework\TestCase;

class AttendeeTest extends TestCase
{
    public function testNewAttendeeIsNotCheckedIn(): void
    {
        $attendee = new Attendee();
        self::assertFalse($attendee->isCheckedIn());
        self::assertNull($attendee->getCheckedInAt());
    }

    public function testRegisteredAtIsSetByConstructor(): void
    {
        $attendee = new Attendee();
        self::assertInstanceOf(\DateTime::class, $attendee->getRegisteredAt());
    }

    public function testGetFullNameJoinsFirstAndLast(): void
    {
        $attendee = new Attendee();
        $attendee->setFirstName('Bob');
        $attendee->setLastName('Le');
        self::assertSame('Bob Le', $attendee->getFullName());
    }

    public function testSetCheckedInTrueAlsoStampsTheTimestamp(): void
    {
        $attendee = new Attendee();
        $before = new \DateTime();

        $attendee->setCheckedIn(true);

        self::assertTrue($attendee->isCheckedIn());
        self::assertInstanceOf(\DateTime::class, $attendee->getCheckedInAt());
        self::assertGreaterThanOrEqual($before, $attendee->getCheckedInAt());
    }

    public function testCheckedInAtIsPreservedAcrossRepeatedSetTrueCalls(): void
    {
        $attendee = new Attendee();
        $attendee->setCheckedIn(true);
        $firstStamp = $attendee->getCheckedInAt();

        // Simulate re-running the check-in action a moment later
        usleep(10_000);
        $attendee->setCheckedIn(true);

        self::assertSame($firstStamp, $attendee->getCheckedInAt(), 'Re-checking in must not overwrite the original timestamp');
    }

    public function testSettingCheckedInFalseDoesNotClearTimestamp(): void
    {
        // Documents current behaviour: setCheckedIn(false) only flips the flag,
        // the timestamp remains. If we ever want a true reset, that would be a
        // separate clearCheckedInAt() method to keep this regression-safe.
        $attendee = new Attendee();
        $attendee->setCheckedIn(true);
        $stamp = $attendee->getCheckedInAt();

        $attendee->setCheckedIn(false);
        self::assertFalse($attendee->isCheckedIn());
        self::assertSame($stamp, $attendee->getCheckedInAt());
    }

    public function testNewAttendeeHasEmptySessionCollection(): void
    {
        $attendee = new Attendee();
        self::assertCount(0, $attendee->getSessions());
    }

    public function testAddSessionIsIdempotent(): void
    {
        $attendee = new Attendee();
        $session = new Session();

        $attendee->addSession($session);
        $attendee->addSession($session);

        self::assertCount(1, $attendee->getSessions());
    }
}
