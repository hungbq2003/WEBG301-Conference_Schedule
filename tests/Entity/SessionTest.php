<?php

namespace App\Tests\Entity;

use App\Entity\Attendee;
use App\Entity\Session;
use App\Entity\Speaker;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    public function testDefaultCapacityIsOneHundred(): void
    {
        $session = new Session();
        self::assertSame(100, $session->getCapacity());
    }

    public function testNewSessionHasEmptySpeakerAndAttendeeCollections(): void
    {
        $session = new Session();
        self::assertCount(0, $session->getSpeakers());
        self::assertCount(0, $session->getAttendees());
        self::assertSame(0, $session->getAttendeeCount());
    }

    public function testAvailableSeatsEqualsCapacityWhenEmpty(): void
    {
        $session = new Session();
        $session->setCapacity(50);
        self::assertSame(50, $session->getAvailableSeats());
    }

    public function testAvailableSeatsDecreasesAsAttendeesJoin(): void
    {
        $session = new Session();
        $session->setCapacity(3);

        $session->addAttendee(new Attendee());
        self::assertSame(2, $session->getAvailableSeats());

        $session->addAttendee(new Attendee());
        self::assertSame(1, $session->getAvailableSeats());

        $session->addAttendee(new Attendee());
        self::assertSame(0, $session->getAvailableSeats());
    }

    public function testAvailableSeatsClampsAtZeroWhenOverbooked(): void
    {
        $session = new Session();
        $session->setCapacity(1);

        $session->addAttendee(new Attendee());
        $session->addAttendee(new Attendee());
        $session->addAttendee(new Attendee());

        self::assertSame(0, $session->getAvailableSeats(), 'Available seats must never be negative');
        self::assertSame(3, $session->getAttendeeCount());
    }

    public function testAddSpeakerIsIdempotent(): void
    {
        $session = new Session();
        $speaker = new Speaker();

        $session->addSpeaker($speaker);
        $session->addSpeaker($speaker);

        self::assertCount(1, $session->getSpeakers());
    }

    public function testRemoveSpeakerKeepsBothSidesInSync(): void
    {
        $session = new Session();
        $speaker = new Speaker();

        $session->addSpeaker($speaker);
        self::assertTrue($session->getSpeakers()->contains($speaker));

        $session->removeSpeaker($speaker);
        self::assertFalse($session->getSpeakers()->contains($speaker));
    }
}
