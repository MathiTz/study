<?php

declare(strict_types=1);

namespace TrackNote\Controllers;

use TrackNote\Core\Request;
use TrackNote\Core\Response;
use TrackNote\Core\Session;
use TrackNote\Models\User;
use TrackNote\Services\AuthService;
use TrackNote\Services\AuditService;

class UserController
{
    private AuthService $authService;
    private AuditService $auditService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->auditService = new AuditService();
    }

    public function profile(Request $request): Response
    {
        $userId = Session::getUserId();
        $user = User::find($userId);

        if ($user === null) {
            return Response::notFound('User not found');
        }

        $userData = User::toPublic($user);
        $userData['organizations'] = User::getOrganizations($userId);
        $userData['projects'] = User::getProjects($userId);

        return Response::success($userData);
    }

    public function updateProfile(Request $request): Response
    {
        $userId = Session::getUserId();
        $oldUser = User::find($userId);

        if ($oldUser === null) {
            return Response::notFound('User not found');
        }

        try {
            $data = $request->validate([
                'name' => 'nullable|string|min:2|max:255',
                'avatar_url' => 'nullable|string|max:500',
                'timezone' => 'nullable|string|max:50',
            ]);
        } catch (\InvalidArgumentException $e) {
            $errors = json_decode($e->getMessage(), true);
            return Response::validationError($errors);
        }

        $updateData = array_filter($data, fn($v) => $v !== null);

        if (empty($updateData)) {
            return Response::error('No fields to update', 400);
        }

        try {
            $user = $this->authService->updateProfile($userId, $updateData);

            $this->auditService->log(
                'user',
                $userId,
                'update_profile',
                array_intersect_key(User::toPublic($oldUser), $updateData),
                $updateData
            );

            return Response::success($user, 'Profile updated successfully');
        } catch (\InvalidArgumentException $e) {
            return Response::error($e->getMessage(), 400);
        }
    }

    public function updatePassword(Request $request): Response
    {
        $userId = Session::getUserId();

        try {
            $data = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|max:255',
            ]);
        } catch (\InvalidArgumentException $e) {
            $errors = json_decode($e->getMessage(), true);
            return Response::validationError($errors);
        }

        try {
            $this->authService->updatePassword(
                $userId,
                $data['current_password'],
                $data['new_password']
            );

            $this->auditService->log('user', $userId, 'update_password');

            return Response::success(null, 'Password updated successfully');
        } catch (\InvalidArgumentException $e) {
            return Response::error($e->getMessage(), 400);
        }
    }
}
