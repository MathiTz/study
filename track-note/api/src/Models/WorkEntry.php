<?php

declare(strict_types=1);

namespace TrackNote\Models;

use TrackNote\Core\Database;

class WorkEntry extends Model
{
    protected static string $table = "work_entries";

    public static function findForUser(string $entryId, string $userId): ?array
    {
        $db = Database::getInstance();
        return $db->fetch(
            'SELECT we.id, we.project_id, we.user_id, we.title, we.description, we.started_at, we.ended_at, we.total_paused_seconds, we.created_at, we.updated_at,
                    p.name as project_name, p.color as project_color
             FROM work_entries we
             INNER JOIN projects p ON we.project_id = p.id
             WHERE we.id = :entry_id AND we.user_id = :user_id',
            ["entry_id" => $entryId, "user_id" => $userId],
        );
    }

    public static function getAllForUser(
        string $userId,
        ?string $projectId = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $limit = 50,
        ?int $offset = 0,
    ): array {
        $db = Database::getInstance();

        $conditions = ["we.user_id = :user_id"];
        $params = ["user_id" => $userId];

        if ($projectId !== null) {
            $conditions[] = "we.project_id = :project_id";
            $params["project_id"] = $projectId;
        }

        if ($startDate !== null) {
            $conditions[] = "we.started_at >= :start_date";
            $params["start_date"] = $startDate;
        }

        if ($endDate !== null) {
            $conditions[] = "we.started_at <= :end_date";
            $params["end_date"] = $endDate;
        }

        $whereClause = implode(" AND ", $conditions);
        $params["limit"] = $limit;
        $params["offset"] = $offset;

        return $db->fetchAll(
            "SELECT we.id, we.project_id, we.user_id, we.title, we.description, we.started_at, we.ended_at, we.total_paused_seconds, we.created_at, we.updated_at,
                    p.name as project_name, p.color as project_color
             FROM work_entries we
             INNER JOIN projects p ON we.project_id = p.id
             WHERE $whereClause
             ORDER BY we.started_at DESC
             LIMIT :limit OFFSET :offset",
            $params,
        );
    }

    public static function checkOverlap(
        string $userId,
        string $startedAt,
        ?string $endedAt,
        ?string $excludeEntryId = null,
    ): ?array {
        $db = Database::getInstance();

        $params = [
            "user_id" => $userId,
            "started_at" => $startedAt,
        ];

        $excludeCondition = "";
        if ($excludeEntryId !== null) {
            $excludeCondition = "AND we.id != :exclude_id";
            $params["exclude_id"] = $excludeEntryId;
        }

        if ($endedAt === null) {
            return $db->fetch(
                "SELECT we.id, we.title, we.started_at, we.ended_at
                 FROM work_entries we
                 WHERE we.user_id = :user_id
                 AND (we.ended_at IS NULL OR we.ended_at > :started_at)
                 $excludeCondition
                 LIMIT 1",
                $params,
            );
        }

        $params["ended_at"] = $endedAt;

        return $db->fetch(
            "SELECT we.id, we.title, we.started_at, we.ended_at
             FROM work_entries we
             WHERE we.user_id = :user_id
             AND NOT (we.ended_at <= :started_at OR we.started_at >= :ended_at)
             $excludeCondition
             LIMIT 1",
            $params,
        );
    }

    public static function getBlocks(string $entryId): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT * FROM work_blocks WHERE work_entry_id = :entry_id ORDER BY sort_order, created_at",
            ["entry_id" => $entryId],
        );
    }
    /**
     * @param array<int,mixed> $data
     */
    public static function addBlock(string $entryId, array $data): string
    {
        $db = Database::getInstance();

        $maxOrder = $db->fetchColumn(
            "SELECT COALESCE(MAX(sort_order), 0) FROM work_blocks WHERE work_entry_id = :entry_id",
            ["entry_id" => $entryId],
        );

        return $db->insert("work_blocks", [
            "work_entry_id" => $entryId,
            "title" => $data["title"],
            "notes" => $data["notes"] ?? null,
            "block_start" => $data["block_start"] ?? null,
            "block_end" => $data["block_end"] ?? null,
            "sort_order" => ((int) $maxOrder) + 1,
        ]);
    }
    /**
     * @param array<int,mixed> $data
     */
    public static function updateBlock(string $blockId, array $data): bool
    {
        $db = Database::getInstance();
        $affected = $db->update("work_blocks", $data, "id = :id", [
            "id" => $blockId,
        ]);
        return $affected > 0;
    }

    public static function deleteBlock(string $blockId): bool
    {
        $db = Database::getInstance();
        $affected = $db->delete("work_blocks", "id = :id", ["id" => $blockId]);
        return $affected > 0;
    }

    public static function getBlock(string $blockId): ?array
    {
        $db = Database::getInstance();
        return $db->fetch("SELECT * FROM work_blocks WHERE id = :id", [
            "id" => $blockId,
        ]);
    }
    /**
     * @param array<int,mixed> $entry
     */
    public static function calculateDuration(array $entry): int
    {
        if ($entry["ended_at"] === null) {
            $endTime = time();
        } else {
            $endTime = strtotime($entry["ended_at"]);
        }

        $startTime = strtotime($entry["started_at"]);
        $totalSeconds = $endTime - $startTime;
        $pausedSeconds = (int) ($entry["total_paused_seconds"] ?? 0);

        return max(0, $totalSeconds - $pausedSeconds);
    }
    /**
     * @return array|array<string,int>
     */
    public static function getSummary(
        string $userId,
        ?string $startDate = null,
        ?string $endDate = null,
    ): array {
        $db = Database::getInstance();

        $conditions = ["we.user_id = :user_id", "we.ended_at IS NOT NULL"];
        $params = ["user_id" => $userId];

        if ($startDate !== null) {
            $conditions[] = "we.started_at >= :start_date";
            $params["start_date"] = $startDate;
        }

        if ($endDate !== null) {
            $conditions[] = "we.started_at <= :end_date";
            $params["end_date"] = $endDate;
        }

        $whereClause = implode(" AND ", $conditions);

        return $db->fetch(
            "SELECT
                COUNT(*) as total_entries,
                COALESCE(SUM(EXTRACT(EPOCH FROM (we.ended_at - we.started_at)) - we.total_paused_seconds), 0) as total_seconds
             FROM work_entries we
             WHERE $whereClause",
            $params,
        ) ?? ["total_entries" => 0, "total_seconds" => 0];
    }
}
