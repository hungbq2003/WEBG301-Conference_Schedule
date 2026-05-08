<?php

namespace App\Tests\Entity;

use App\Entity\Session;
use App\Entity\Speaker;
use PHPUnit\Framework\TestCase;

class SpeakerTest extends TestCase
{
    public function testGetFullNameJoinsFirstAndLast(): void
    {
        $speaker = new Speaker();
        $speaker->setFirstName('Quoc');
        $speaker->setLastName('Hung');
        self::assertSame('Quoc Hung', $speaker->getFullName());
    }

    public function testCreatedAtIsSetByConstructor(): void
    {
        $speaker = new Speaker();
        self::assertInstanceOf(\DateTime::class, $speaker->getCreatedAt());
    }

    public function testNewSpeakerHasEmptySessionCollection(): void
    {
        $speaker = new Speaker();
        self::assertCount(0, $speaker->getSessions());
    }

    public function testNullableFieldsDefaultToNull(): void
    {
        $speaker = new Speaker();
        self::assertNull($speaker->getPhone());
        self::assertNull($speaker->getBio());
        self::assertNull($speaker->getAffiliation());
        self::assertNull($speaker->getProfileImage());
    }

    public function testAddSessionIsIdempotent(): void
    {
        $speaker = new Speaker();
        $session = new Session();

        $speaker->addSession($session);
        $speaker->addSession($session);

        self::assertCount(1, $speaker->getSessions());
    }
}
