<?php

declare(strict_types=1);

namespace TrackNote\Controllers;

use TrackNote\Core\Request;
use TrackNote\Core\Response;
use TrackNote\Services\AuthService;
use TrackNote\Services\AuditService;

class AuthController
{
    private AuthService $authService;
    private AuditService $auditService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->auditService = new AuditService();
    }

    public function register(Request $request): Response
    {
        try {
            $data = $request->validate([
                'email' => 'required|email|max:255',
                'password' => 'required|string|min:8|max:255',
                'name' => 'required|string|min:2|max:255',
            ]);
        } catch (\InvalidArgumentException $e) {
            $errors = json_decode($e->getMessage(), true);
            return Response::validationError($errors);
        }

        try {
            $user = $this->authService->register(
                $data['email'],
                $data['password'],
                $data['name']
            );

            $this->auditService->log('user', $user['id'], 'register', null, [
                'email' => $user['email'],
                'name' => $user['name'],
            ]);

            return Response::created($user, 'Registration successful');
        } catch (\InvalidArgumentException $e) {
            return Response::error($e->getMessage(), 409);
        }
    }

    public function login(Request $request): Response
    {
        try {
            $data = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
        } catch (\InvalidArgumentException $e) {
            $errors = json_decode($e->getMessage(), true);
            return Response::validationError($errors);
        }

        try {
            $user = $this->authService->login($data['email'], $data['password']);

            $this->auditService->log('user', $user['id'], 'login');

            return Response::success($user, 'Login successful');
        } catch (\InvalidArgumentException $e) {
            return Response::error($e->getMessage(), 401);
        }
    }

    public function logout(Request $request): Response
    {
        $user = $this->authService->getCurrentUser();

        if ($user) {
            $this->auditService->log('user', $user['id'], 'logout');
        }

        $this->authService->logout();

        return Response::success(null, 'Logged out successfully');
    }

    public function me(Request $request): Response
    {
        $user = $this->authService->getCurrentUser();

        if ($user === null) {
            return Response::unauthorized();
        }

        return Response::success($user);
    }
}
