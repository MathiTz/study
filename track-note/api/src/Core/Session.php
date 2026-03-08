<?php

declare(strict_types=1);

namespace TrackNote\Core;

class Session
{
    private static bool $started = false;

    public static function start(array $config = []): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        if (empty($config)) {
            $appConfig = require dirname(__DIR__, 2) . '/config/app.php';
            $config = $appConfig['session'];
        }

        session_name($config['name'] ?? 'tracknote_session');

        session_set_cookie_params([
            'lifetime' => $config['lifetime'] ?? 7200,
            'path' => $config['path'] ?? '/',
            'domain' => $config['domain'] ?? '',
            'secure' => $config['secure'] ?? false,
            'httponly' => $config['httponly'] ?? true,
            'samesite' => $config['samesite'] ?? 'Lax',
        ]);

        session_start();
        self::$started = true;

        self::regenerateIfNeeded();
    }

    private static function regenerateIfNeeded(): void
    {
        $regenerateInterval = 1800; // 30 minutes

        if (!isset($_SESSION['_last_regenerate'])) {
            $_SESSION['_last_regenerate'] = time();
            return;
        }

        if (time() - $_SESSION['_last_regenerate'] > $regenerateInterval) {
            session_regenerate_id(true);
            $_SESSION['_last_regenerate'] = time();
        }
    }

    public static function set(string $key, mixed $value): void
    {
        self::ensureStarted();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::ensureStarted();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::ensureStarted();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::ensureStarted();
        unset($_SESSION[$key]);
    }

    public static function clear(): void
    {
        self::ensureStarted();
        $_SESSION = [];
    }

    public static function destroy(): void
    {
        self::ensureStarted();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        self::$started = false;
    }

    public static function regenerate(): void
    {
        self::ensureStarted();
        session_regenerate_id(true);
        $_SESSION['_last_regenerate'] = time();
    }

    public static function id(): string
    {
        self::ensureStarted();
        return session_id();
    }

    public static function setUserId(string $userId): void
    {
        self::set('user_id', $userId);
        self::regenerate();
    }

    public static function getUserId(): ?string
    {
        return self::get('user_id');
    }

    public static function isAuthenticated(): bool
    {
        return self::getUserId() !== null;
    }

    public static function flash(string $key, mixed $value): void
    {
        self::set("_flash.$key", $value);
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $flashKey = "_flash.$key";
        $value = self::get($flashKey, $default);
        self::remove($flashKey);
        return $value;
    }

    private static function ensureStarted(): void
    {
        if (!self::$started && session_status() !== PHP_SESSION_ACTIVE) {
            self::start();
        }
    }
}
