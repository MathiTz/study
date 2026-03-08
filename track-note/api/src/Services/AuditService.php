<?php

declare(strict_types=1);

namespace TrackNote\Services;

use TrackNote\Core\Database;
use TrackNote\Core\Session;

class AuditService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function log(
        string $entityType,
        string $entityId,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $metadata = null
    ): void {
        $userId = Session::getUserId();

        $this->db->insert('audit_logs', [
            'user_id' => $userId,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'old_values' => $oldValues !== null ? json_encode($oldValues) : null,
            'new_values' => $newValues !== null ? json_encode($newValues) : null,
            'metadata' => json_encode(array_merge($metadata ?? [], [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            ])),
        ]);
    }

    public function getEntityHistory(
        string $entityType,
        string $entityId,
        ?int $limit = 50,
        ?int $offset = 0
    ): array {
        $sql = '
            SELECT al.*, u.name as user_name, u.email as user_email
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.id
            WHERE al.entity_type = :entity_type AND al.entity_id = :entity_id
            ORDER BY al.created_at DESC
            LIMIT :limit OFFSET :offset
        ';

        $logs = $this->db->fetchAll($sql, [
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'limit' => $limit,
            'offset' => $offset,
        ]);

        return array_map(function ($log) {
            $log['old_values'] = $log['old_values'] ? json_decode($log['old_values'], true) : null;
            $log['new_values'] = $log['new_values'] ? json_decode($log['new_values'], true) : null;
            $log['metadata'] = $log['metadata'] ? json_decode($log['metadata'], true) : null;
            return $log;
        }, $logs);
    }

    public function getUserLogs(
        string $userId,
        ?int $limit = 50,
        ?int $offset = 0
    ): array {
        $sql = '
            SELECT al.*, u.name as user_name, u.email as user_email
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.id
            WHERE al.user_id = :user_id
            ORDER BY al.created_at DESC
            LIMIT :limit OFFSET :offset
        ';

        $logs = $this->db->fetchAll($sql, [
            'user_id' => $userId,
            'limit' => $limit,
            'offset' => $offset,
        ]);

        return array_map(function ($log) {
            $log['old_values'] = $log['old_values'] ? json_decode($log['old_values'], true) : null;
            $log['new_values'] = $log['new_values'] ? json_decode($log['new_values'], true) : null;
            $log['metadata'] = $log['metadata'] ? json_decode($log['metadata'], true) : null;
            return $log;
        }, $logs);
    }

    public function cleanupOldLogs(int $retentionDays): int
    {
        $sql = 'DELETE FROM audit_logs WHERE created_at < NOW() - INTERVAL :days DAY';
        $stmt = $this->db->query($sql, ['days' => $retentionDays]);
        return $stmt->rowCount();
    }
}
