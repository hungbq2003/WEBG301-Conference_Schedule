<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testNewUserAlwaysHasRoleUser(): void
    {
        $user = new User();
        self::assertContains('ROLE_USER', $user->getRoles());
    }

    public function testRolesAreDeduplicatedAndAlwaysIncludeRoleUser(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER', 'ROLE_USER']);

        $roles = $user->getRoles();
        self::assertContains('ROLE_USER', $roles);
        self::assertContains('ROLE_ADMIN', $roles);
        self::assertCount(2, $roles, 'Duplicate roles should be removed');
    }

    public function testUserIdentifierIsTheEmail(): void
    {
        $user = new User();
        $user->setEmail('alice@example.com');
        self::assertSame('alice@example.com', $user->getUserIdentifier());
    }

    public function testGetFullNameJoinsFirstAndLast(): void
    {
        $user = new User();
        $user->setFirstName('Alice');
        $user->setLastName('Nguyen');
        self::assertSame('Alice Nguyen', $user->getFullName());
    }

    public function testCreatedAtIsSetByConstructor(): void
    {
        $user = new User();
        self::assertInstanceOf(\DateTime::class, $user->getCreatedAt());
        self::assertLessThanOrEqual(new \DateTime(), $user->getCreatedAt());
    }

    public function testEraseCredentialsIsNoOp(): void
    {
        $user = new User();
        $user->setPassword('secret-hash');
        $user->eraseCredentials();
        self::assertSame('secret-hash', $user->getPassword());
    }
}
