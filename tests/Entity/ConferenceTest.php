<?php

namespace App\Tests\Entity;

use App\Entity\Attendee;
use App\Entity\Conference;
use App\Entity\Session;
use App\Entity\Speaker;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class ConferenceTest extends TestCase
{
    public function testNewConferenceHasEmptyChildCollections(): void
    {
        $conference = new Conference();
        self::assertInstanceOf(Collection::class, $conference->getSessions());
        self::assertInstanceOf(Collection::class, $conference->getSpeakers());
        self::assertInstanceOf(Collection::class, $conference->getAttendees());
        self::assertCount(0, $conference->getSessions());
        self::assertCount(0, $conference->getSpeakers());
        self::assertCount(0, $conference->getAttendees());
    }

    public function testCreatedAtIsSetByConstructor(): void
    {
        $conference = new Conference();
        self::assertInstanceOf(\DateTime::class, $conference->getCreatedAt());
    }

    public function testAddSessionSyncsReverseSide(): void
    {
        $conference = new Conference();
        $session = new Session();

        $conference->addSession($session);

        self::assertCount(1, $conference->getSessions());
        self::assertSame($conference, $session->getConference(), 'Session.conference should point back at the parent');
    }

    public function testAddSessionIsIdempotent(): void
    {
        $conference = new Conference();
        $session = new Session();

        $conference->addSession($session);
        $conference->addSession($session);

        self::assertCount(1, $conference->getSessions());
    }

    public function testAddSpeakerSyncsReverseSide(): void
    {
        $conference = new Conference();
        $speaker = new Speaker();

        $conference->addSpeaker($speaker);

        self::assertCount(1, $conference->getSpeakers());
        self::assertSame($conference, $speaker->getConference());
    }

    public function testAddAttendeeSyncsReverseSide(): void
    {
        $conference = new Conference();
        $attendee = new Attendee();

        $conference->addAttendee($attendee);

        self::assertCount(1, $conference->getAttendees());
        self::assertSame($conference, $attendee->getConference());
    }

    public function testFluentSettersReturnSelf(): void
    {
        $conference = new Conference();
        self::assertSame($conference, $conference->setName('PHP Forum'));
        self::assertSame($conference, $conference->setLocation('Hanoi'));
        self::assertSame($conference, $conference->setCapacity(500));
    }
}
