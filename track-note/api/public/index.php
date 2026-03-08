<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/vendor/autoload.php";

use TrackNote\Core\Request;
use TrackNote\Core\Response;
use TrackNote\Core\Router;
use TrackNote\Core\Session;
use TrackNote\Middleware\CorsMiddleware;
use TrackNote\Middleware\AuthMiddleware;
use TrackNote\Controllers\AuthController;
use TrackNote\Controllers\UserController;
use TrackNote\Controllers\ProjectController;
use TrackNote\Controllers\TimerController;
use TrackNote\Controllers\WorkEntryController;
use TrackNote\Controllers\OrganizationController;
use TrackNote\Controllers\ReportController;

// Load environment variables from .env if exists
$envFile = dirname(__DIR__) . "/.env";
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), "#")) {
            continue;
        }
        if (str_contains($line, "=")) {
            putenv(trim($line));
        }
    }
}

// Initialize session
Session::start();

// Set error handling
$appConfig = require dirname(__DIR__) . "/config/app.php";

if ($appConfig["debug"]) {
    error_reporting(E_ALL);
    ini_set("display_errors", "1");
} else {
    error_reporting(0);
    ini_set("display_errors", "0");
}

set_exception_handler(function (Throwable $e) use ($appConfig) {
    $message = $appConfig["debug"] ? $e->getMessage() : "Internal server error";
    $response = Response::serverError($message);

    if ($appConfig["debug"]) {
        $response = Response::error($e->getMessage(), 500, [
            "file" => $e->getFile(),
            "line" => $e->getLine(),
            "trace" => explode("\n", $e->getTraceAsString()),
        ]);
    }

    $response->send();
});

// Create router
$router = new Router();

// CORS middleware for all routes
$corsMiddleware = new CorsMiddleware();

// Public routes
$router->group(["prefix" => "/api"], function (Router $router) {
    // Health check
    $router->get("/health", fn() => Response::success(["status" => "ok"]));

    // Auth routes (public)
    $router->group(["prefix" => "/auth"], function (Router $router) {
        $router->post("/register", [AuthController::class, "register"]);
        $router->post("/login", [AuthController::class, "login"]);
        $router->post("/logout", [AuthController::class, "logout"]);
        $router->get("/me", [AuthController::class, "me"]);
    });

    // Accept invite (public, but needs the code)
    $router->post("/invites/:code/accept", [
        OrganizationController::class,
        "acceptInvite",
    ]);

    // Protected routes
    $router->group(
        ["prefix" => "", "middleware" => AuthMiddleware::class],
        function (Router $router) {
            // User profile
            $router->get("/users/profile", [UserController::class, "profile"]);
            $router->put("/users/profile", [
                UserController::class,
                "updateProfile",
            ]);
            $router->put("/users/password", [
                UserController::class,
                "updatePassword",
            ]);

            // Projects
            $router->get("/projects", [ProjectController::class, "index"]);
            $router->post("/projects", [ProjectController::class, "store"]);
            $router->get("/projects/:id", [ProjectController::class, "show"]);
            $router->put("/projects/:id", [ProjectController::class, "update"]);
            $router->delete("/projects/:id", [
                ProjectController::class,
                "destroy",
            ]);

            // Project members
            $router->get("/projects/:id/members", [
                ProjectController::class,
                "members",
            ]);
            $router->post("/projects/:id/members", [
                ProjectController::class,
                "addMember",
            ]);
            $router->put("/projects/:id/members/:userId", [
                ProjectController::class,
                "updateMember",
            ]);
            $router->delete("/projects/:id/members/:userId", [
                ProjectController::class,
                "removeMember",
            ]);

            // Timer
            $router->get("/timer/current", [TimerController::class, "current"]);
            $router->post("/timer/start", [TimerController::class, "start"]);
            $router->post("/timer/pause", [TimerController::class, "pause"]);
            $router->post("/timer/resume", [TimerController::class, "resume"]);
            $router->post("/timer/stop", [TimerController::class, "stop"]);
            $router->post("/timer/transition", [
                TimerController::class,
                "transition",
            ]);

            // Work entries
            $router->get("/work-entries", [
                WorkEntryController::class,
                "index",
            ]);
            $router->post("/work-entries", [
                WorkEntryController::class,
                "store",
            ]);
            $router->get("/work-entries/:id", [
                WorkEntryController::class,
                "show",
            ]);
            $router->put("/work-entries/:id", [
                WorkEntryController::class,
                "update",
            ]);
            $router->delete("/work-entries/:id", [
                WorkEntryController::class,
                "destroy",
            ]);

            // Work entry blocks
            $router->get("/work-entries/:id/blocks", [
                WorkEntryController::class,
                "blocks",
            ]);
            $router->post("/work-entries/:id/blocks", [
                WorkEntryController::class,
                "addBlock",
            ]);
            $router->put("/work-entries/:id/blocks/:blockId", [
                WorkEntryController::class,
                "updateBlock",
            ]);
            $router->delete("/work-entries/:id/blocks/:blockId", [
                WorkEntryController::class,
                "removeBlock",
            ]);

            // Work entry history
            $router->get("/work-entries/:id/history", [
                WorkEntryController::class,
                "history",
            ]);

            // Organizations
            $router->get("/organizations", [
                OrganizationController::class,
                "index",
            ]);
            $router->post("/organizations", [
                OrganizationController::class,
                "store",
            ]);
            $router->get("/organizations/:id", [
                OrganizationController::class,
                "show",
            ]);
            $router->put("/organizations/:id", [
                OrganizationController::class,
                "update",
            ]);
            $router->delete("/organizations/:id", [
                OrganizationController::class,
                "destroy",
            ]);

            // Organization members
            $router->get("/organizations/:id/members", [
                OrganizationController::class,
                "members",
            ]);
            $router->post("/organizations/:id/members", [
                OrganizationController::class,
                "addMember",
            ]);
            $router->put("/organizations/:id/members/:userId", [
                OrganizationController::class,
                "updateMember",
            ]);
            $router->delete("/organizations/:id/members/:userId", [
                OrganizationController::class,
                "removeMember",
            ]);

            // Organization invites
            $router->get("/organizations/:id/invites", [
                OrganizationController::class,
                "invites",
            ]);
            $router->post("/organizations/:id/invites", [
                OrganizationController::class,
                "createInvite",
            ]);
            $router->delete("/organizations/:id/invites/:code", [
                OrganizationController::class,
                "revokeInvite",
            ]);

            // Reports
            $router->get("/reports/summary", [
                ReportController::class,
                "summary",
            ]);
            $router->get("/reports/by-project", [
                ReportController::class,
                "byProject",
            ]);
            $router->get("/reports/by-user", [
                ReportController::class,
                "byUser",
            ]);
            $router->get("/reports/export", [
                ReportController::class,
                "export",
            ]);

            // Audit logs
            $router->get("/audit-logs", [ReportController::class, "auditLogs"]);
        },
    );
});

// Handle request
$request = new Request();

// Apply CORS middleware manually to all requests
$response = $corsMiddleware->handle(
    $request,
    fn($req) => $router->dispatch($req),
);

$response->send();
