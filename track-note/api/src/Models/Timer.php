<?php

declare(strict_types=1);

namespace TrackNote\Models;

use TrackNote\Core\Database;

class Timer extends Model
{
    protected static string $table = "active_timers";

    public static function findByUser(string $userId): ?array
    {
        $db = Database::getInstance();
        return $db->fetch(
            'SELECT at.*, we.title, we.description, we.started_at, we.total_paused_seconds, we.project_id,
                    p.name as project_name, p.color as project_color
             FROM active_timers at
             INNER JOIN work_entries we ON at.work_entry_id = we.id
             INNER JOIN projects p ON we.project_id = p.id
             WHERE at.user_id = :user_id',
            ["user_id" => $userId],
        );
    }

    public static function start(
        string $userId,
        string $projectId,
        string $title,
    ): array {
        $db = Database::getInstance();

        $existing = self::findByUser($userId);
        if ($existing !== null) {
            throw new \InvalidArgumentException("A timer is already running");
        }

        return $db->transaction(function (Database $db) use (
            $userId,
            $projectId,
            $title,
        ) {
            $entryId = $db->insert("work_entries", [
                "project_id" => $projectId,
                "user_id" => $userId,
                "title" => $title,
                "started_at" => date("c"),
                "is_manual" => false,
            ]);

            $timerId = $db->insert("active_timers", [
                "user_id" => $userId,
                "work_entry_id" => $entryId,
                "status" => "running",
            ]);

            return [
                "timer_id" => $timerId,
                "entry_id" => $entryId,
            ];
        });
    }

    public static function pause(string $userId): bool
    {
        $db = Database::getInstance();

        $timer = self::findByUser($userId);
        if ($timer === null) {
            throw new \InvalidArgumentException("No active timer found");
        }

        if ($timer["status"] === "paused") {
            throw new \InvalidArgumentException("Timer is already paused");
        }

        $affected = $db->update(
            "active_timers",
            [
                "status" => "paused",
                "last_pause_at" => date("c"),
            ],
            "user_id = :user_id",
            ["user_id" => $userId],
        );

        return $affected > 0;
    }

    public static function resume(string $userId): bool
    {
        $db = Database::getInstance();

        $timer = self::findByUser($userId);
        if ($timer === null) {
            throw new \InvalidArgumentException("No active timer found");
        }

        if ($timer["status"] !== "paused") {
            throw new \InvalidArgumentException("Timer is not paused");
        }

        return $db->transaction(function (Database $db) use ($userId, $timer) {
            $pausedDuration = time() - strtotime($timer["last_pause_at"]);
            $newTotalPaused =
                ((int) $timer["total_paused_seconds"]) + $pausedDuration;

            $db->update(
                "work_entries",
                ["total_paused_seconds" => $newTotalPaused],
                "id = :id",
                ["id" => $timer["work_entry_id"]],
            );

            $db->update(
                "active_timers",
                [
                    "status" => "running",
                    "last_pause_at" => null,
                ],
                "user_id = :user_id",
                ["user_id" => $userId],
            );

            return true;
        });
    }

    public static function stop(string $userId): array
    {
        $db = Database::getInstance();

        $timer = self::findByUser($userId);
        if ($timer === null) {
            throw new \InvalidArgumentException("No active timer found");
        }

        return $db->transaction(function (Database $db) use ($userId, $timer) {
            $totalPausedSeconds = (int) $timer["total_paused_seconds"];

            if (
                $timer["status"] === "paused" &&
                $timer["last_pause_at"] !== null
            ) {
                $pausedDuration = time() - strtotime($timer["last_pause_at"]);
                $totalPausedSeconds += $pausedDuration;
            }

            $db->update(
                "work_entries",
                [
                    "ended_at" => date("c"),
                    "total_paused_seconds" => $totalPausedSeconds,
                ],
                "id = :id",
                ["id" => $timer["work_entry_id"]],
            );

            $db->delete("active_timers", "user_id = :user_id", [
                "user_id" => $userId,
            ]);

            return WorkEntry::find($timer["work_entry_id"]);
        });
    }

    public static function transition(
        string $userId,
        string $projectId,
        string $title,
    ): array {
        $db = Database::getInstance();

        $timer = self::findByUser($userId);
        if ($timer === null) {
            throw new \InvalidArgumentException("No active timer found");
        }

        return $db->transaction(function (Database $db) use (
            $userId,
            $projectId,
            $title,
            $timer,
        ) {
            $now = date("c");
            $totalPausedSeconds = (int) $timer["total_paused_seconds"];

            if (
                $timer["status"] === "paused" &&
                $timer["last_pause_at"] !== null
            ) {
                $pausedDuration = time() - strtotime($timer["last_pause_at"]);
                $totalPausedSeconds += $pausedDuration;
            }

            $db->update(
                "work_entries",
                [
                    "ended_at" => $now,
                    "total_paused_seconds" => $totalPausedSeconds,
                ],
                "id = :id",
                ["id" => $timer["work_entry_id"]],
            );

            $stoppedEntry = WorkEntry::find($timer["work_entry_id"]);

            $newEntryId = $db->insert("work_entries", [
                "project_id" => $projectId,
                "user_id" => $userId,
                "title" => $title,
                "started_at" => $now,
                "is_manual" => false,
            ]);

            $db->update(
                "active_timers",
                [
                    "work_entry_id" => $newEntryId,
                    "status" => "running",
                    "last_pause_at" => null,
                ],
                "user_id = :user_id",
                ["user_id" => $userId],
            );

            return [
                "stopped_entry" => $stoppedEntry,
                "new_entry_id" => $newEntryId,
            ];
        });
    }
    /**
     * @param array<int,mixed> $timer
     */
    public static function getElapsedSeconds(array $timer): int
    {
        $startTime = strtotime($timer["started_at"]);
        $totalPaused = (int) ($timer["total_paused_seconds"] ?? 0);

        if ($timer["status"] === "paused" && $timer["last_pause_at"] !== null) {
            $currentPause = time() - strtotime($timer["last_pause_at"]);
            $totalPaused += $currentPause;
        }

        return max(0, time() - $startTime - $totalPaused);
    }
}
