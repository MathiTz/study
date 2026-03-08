<?php

declare(strict_types=1);

namespace TrackNote\Services;

use TrackNote\Core\Session;
use TrackNote\Models\User;

class AuthService
{
    public function register(string $email, string $password, string $name): array
    {
        $existingUser = User::findByEmail($email);
        if ($existingUser !== null) {
            throw new \InvalidArgumentException('Email already registered');
        }

        $userId = User::create([
            'email' => $email,
            'password' => $password,
            'name' => $name,
        ]);

        $user = User::find($userId);

        Session::setUserId($userId);

        return User::toPublic($user);
    }

    public function login(string $email, string $password): array
    {
        $user = User::findByEmail($email);

        if ($user === null) {
            throw new \InvalidArgumentException('Invalid credentials');
        }

        if (!User::verifyPassword($password, $user['password_hash'])) {
            throw new \InvalidArgumentException('Invalid credentials');
        }

        Session::setUserId($user['id']);

        return User::toPublic($user);
    }

    public function logout(): void
    {
        Session::destroy();
    }

    public function getCurrentUser(): ?array
    {
        $userId = Session::getUserId();

        if ($userId === null) {
            return null;
        }

        $user = User::find($userId);

        if ($user === null) {
            Session::destroy();
            return null;
        }

        return User::toPublic($user);
    }

    public function updateProfile(string $userId, array $data): array
    {
        $allowedFields = ['name', 'avatar_url', 'timezone'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));

        if (empty($updateData)) {
            throw new \InvalidArgumentException('No valid fields to update');
        }

        User::update($userId, $updateData);

        $user = User::find($userId);
        return User::toPublic($user);
    }

    public function updatePassword(string $userId, string $currentPassword, string $newPassword): void
    {
        $user = User::find($userId);

        if ($user === null) {
            throw new \InvalidArgumentException('User not found');
        }

        if (!User::verifyPassword($currentPassword, $user['password_hash'])) {
            throw new \InvalidArgumentException('Current password is incorrect');
        }

        User::updatePassword($userId, $newPassword);

        Session::regenerate();
    }
}
