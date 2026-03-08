<?php

declare(strict_types=1);

namespace TrackNote\Controllers;

use TrackNote\Core\Request;
use TrackNote\Core\Response;
use TrackNote\Core\Session;
use TrackNote\Models\Timer;
use TrackNote\Models\Project;
use TrackNote\Services\AuditService;

class TimerController
{
    private AuditService $auditService;

    public function __construct()
    {
        $this->auditService = new AuditService();
    }

    public function current(Request $request): Response
    {
        $userId = Session::getUserId();

        $timer = Timer::findByUser($userId);

        if ($timer === null) {
            return Response::success(null);
        }

        $timer["elapsed_seconds"] = Timer::getElapsedSeconds($timer);

        return Response::success($timer);
    }

    public function start(Request $request): Response
    {
        $userId = Session::getUserId();

        try {
            $data = $request->validate([
                "project_id" => "required|uuid",
                "title" => "required|string|min:1|max:255",
            ]);
        } catch (\InvalidArgumentException $e) {
            $errors = json_decode($e->getMessage(), true);
            return Response::validationError($errors);
        }

        if (!Project::isMember($data["project_id"], $userId)) {
            return Response::forbidden("You are not a member of this project");
        }

        try {
            $result = Timer::start(
                $userId,
                $data["project_id"],
                $data["title"],
            );

            $this->auditService->log(
                "work_entry",
                $result["entry_id"],
                "timer_start",
                null,
                [
                    "project_id" => $data["project_id"],
                    "title" => $data["title"],
                ],
            );

            $timer = Timer::findByUser($userId);
            $timer["elapsed_seconds"] = Timer::getElapsedSeconds($timer);

            return Response::created($timer, "Timer started");
        } catch (\InvalidArgumentException $e) {
            return Response::error($e->getMessage(), 409);
        }
    }

    public function pause(Request $request): Response
    {
        $userId = Session::getUserId();

        try {
            Timer::pause($userId);

            $timer = Timer::findByUser($userId);

            $this->auditService->log(
                "work_entry",
                $timer["work_entry_id"],
                "timer_pause",
            );

            $timer["elapsed_seconds"] = Timer::getElapsedSeconds($timer);

            return Response::success($timer, "Timer paused");
        } catch (\InvalidArgumentException $e) {
            return Response::error($e->getMessage(), 400);
        }
    }

    public function resume(Request $request): Response
    {
        $userId = Session::getUserId();

        try {
            Timer::resume($userId);

            $timer = Timer::findByUser($userId);

            $this->auditService->log(
                "work_entry",
                $timer["work_entry_id"],
                "timer_resume",
            );

            $timer["elapsed_seconds"] = Timer::getElapsedSeconds($timer);

            return Response::success($timer, "Timer resumed");
        } catch (\InvalidArgumentException $e) {
            return Response::error($e->getMessage(), 400);
        }
    }

    public function stop(Request $request): Response
    {
        $userId = Session::getUserId();

        try {
            $entry = Timer::stop($userId);

            $this->auditService->log(
                "work_entry",
                $entry["id"],
                "timer_stop",
                null,
                [
                    "ended_at" => $entry["ended_at"],
                    "total_paused_seconds" => $entry["total_paused_seconds"],
                ],
            );

            return Response::success($entry, "Timer stopped");
        } catch (\InvalidArgumentException $e) {
            return Response::error($e->getMessage(), 400);
        }
    }

    public function transition(Request $request): Response
    {
        $userId = Session::getUserId();

        try {
            $data = $request->validate([
                "project_id" => "required|uuid",
                "title" => "required|string|min:1|max:255",
            ]);
        } catch (\InvalidArgumentException $e) {
            $errors = json_decode($e->getMessage(), true);
            return Response::validationError($errors);
        }

        if (!Project::isMember($data["project_id"], $userId)) {
            return Response::forbidden("You are not a member of this project");
        }

        try {
            $result = Timer::transition(
                $userId,
                $data["project_id"],
                $data["title"],
            );

            $this->auditService->log(
                "work_entry",
                $result["stopped_entry"]["id"],
                "timer_stop",
                null,
                [
                    "ended_at" => $result["stopped_entry"]["ended_at"],
                    "total_paused_seconds" =>
                        $result["stopped_entry"]["total_paused_seconds"],
                ],
            );

            $this->auditService->log(
                "work_entry",
                $result["new_entry_id"],
                "timer_start",
                null,
                [
                    "project_id" => $data["project_id"],
                    "title" => $data["title"],
                    "transitioned_from" => $result["stopped_entry"]["id"],
                ],
            );

            $timer = Timer::findByUser($userId);
            $timer["elapsed_seconds"] = Timer::getElapsedSeconds($timer);

            return Response::success($timer, "Timer transitioned");
        } catch (\InvalidArgumentException $e) {
            return Response::error($e->getMessage(), 400);
        }
    }
}
