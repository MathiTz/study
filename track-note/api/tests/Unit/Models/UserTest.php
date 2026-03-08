<?php

declare(strict_types=1);

namespace TrackNote\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use TrackNote\Models\User;

class UserTest extends TestCase
{
    public function testVerifyPasswordReturnsTrueForCorrectPassword(): void
    {
        $password = 'correct-password';
        $hash = password_hash($password, PASSWORD_ARGON2ID);

        $this->assertTrue(User::verifyPassword($password, $hash));
    }

    public function testVerifyPasswordReturnsFalseForIncorrectPassword(): void
    {
        $password = 'correct-password';
        $hash = password_hash($password, PASSWORD_ARGON2ID);

        $this->assertFalse(User::verifyPassword('wrong-password', $hash));
    }

    public function testToPublicRemovesPasswordHash(): void
    {
        $user = [
            'id' => '123',
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password_hash' => 'secret-hash',
            'created_at' => '2024-01-01 00:00:00',
        ];

        $publicUser = User::toPublic($user);

        $this->assertArrayNotHasKey('password_hash', $publicUser);
        $this->assertEquals('123', $publicUser['id']);
        $this->assertEquals('test@example.com', $publicUser['email']);
        $this->assertEquals('Test User', $publicUser['name']);
    }

    public function testGetTableReturnsCorrectTableName(): void
    {
        $this->assertEquals('users', User::getTable());
    }
}
