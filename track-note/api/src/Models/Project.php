<?php

declare(strict_types=1);

namespace TrackNote\Models;

use TrackNote\Core\Database;

class Project extends Model
{
    protected static string $table = 'projects';

    public static function findForUser(string $projectId, string $userId): ?array
    {
        $db = Database::getInstance();
        return $db->fetch(
            'SELECT p.*, pm.role as member_role
             FROM projects p
             LEFT JOIN project_members pm ON p.id = pm.project_id AND pm.user_id = :member_id
             WHERE p.id = :project_id
             AND (p.owner_id = :owner_id OR (pm.user_id = :pm_user_id AND pm.removed_at IS NULL))',
            [
                'project_id' => $projectId,
                'owner_id' => $userId,
                'member_id' => $userId,
                'pm_user_id' => $userId,
            ]
        );
    }

    public static function getAllForUser(string $userId, bool $includeArchived = false): array
    {
        $db = Database::getInstance();
        $archiveCondition = $includeArchived ? '' : 'AND p.archived_at IS NULL';

        return $db->fetchAll(
            "SELECT DISTINCT p.*, pm.role as member_role,
                    CASE WHEN p.owner_id = :owner_check THEN 'owner' ELSE pm.role END as user_role
             FROM projects p
             LEFT JOIN project_members pm ON p.id = pm.project_id AND pm.user_id = :member_id
             WHERE (p.owner_id = :owner_id OR (pm.user_id = :pm_user_id AND pm.removed_at IS NULL))
             $archiveCondition
             ORDER BY p.name",
            [
                'owner_check' => $userId,
                'owner_id' => $userId,
                'member_id' => $userId,
                'pm_user_id' => $userId,
            ]
        );
    }

    public static function getMembers(string $projectId): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            'SELECT u.id, u.name, u.email, u.avatar_url, pm.role, pm.assigned_at
             FROM project_members pm
             INNER JOIN users u ON pm.user_id = u.id
             WHERE pm.project_id = :project_id AND pm.removed_at IS NULL
             ORDER BY pm.assigned_at',
            ['project_id' => $projectId]
        );
    }

    public static function addMember(string $projectId, string $userId, string $role = 'member'): string
    {
        $db = Database::getInstance();

        $existing = $db->fetch(
            'SELECT id, removed_at FROM project_members WHERE project_id = :project_id AND user_id = :user_id',
            ['project_id' => $projectId, 'user_id' => $userId]
        );

        if ($existing) {
            if ($existing['removed_at'] !== null) {
                $db->update('project_members', [
                    'role' => $role,
                    'removed_at' => null,
                    'assigned_at' => date('c'),
                ], 'id = :id', ['id' => $existing['id']]);
                return $existing['id'];
            }
            throw new \InvalidArgumentException('User is already a member of this project');
        }

        return $db->insert('project_members', [
            'project_id' => $projectId,
            'user_id' => $userId,
            'role' => $role,
        ]);
    }

    public static function updateMemberRole(string $projectId, string $userId, string $role): bool
    {
        $db = Database::getInstance();
        $affected = $db->update(
            'project_members',
            ['role' => $role],
            'project_id = :project_id AND user_id = :user_id AND removed_at IS NULL',
            ['project_id' => $projectId, 'user_id' => $userId]
        );
        return $affected > 0;
    }

    public static function removeMember(string $projectId, string $userId): bool
    {
        $db = Database::getInstance();
        $affected = $db->update(
            'project_members',
            ['removed_at' => date('c')],
            'project_id = :project_id AND user_id = :user_id AND removed_at IS NULL',
            ['project_id' => $projectId, 'user_id' => $userId]
        );
        return $affected > 0;
    }

    public static function isMember(string $projectId, string $userId): bool
    {
        $db = Database::getInstance();
        $project = $db->fetch(
            'SELECT p.owner_id, pm.user_id as member_id
             FROM projects p
             LEFT JOIN project_members pm ON p.id = pm.project_id AND pm.user_id = :member_id AND pm.removed_at IS NULL
             WHERE p.id = :project_id',
            ['project_id' => $projectId, 'member_id' => $userId]
        );

        if (!$project) {
            return false;
        }

        return $project['owner_id'] === $userId || $project['member_id'] !== null;
    }

    public static function isManager(string $projectId, string $userId): bool
    {
        $db = Database::getInstance();
        $project = $db->fetch(
            'SELECT p.owner_id, pm.role
             FROM projects p
             LEFT JOIN project_members pm ON p.id = pm.project_id AND pm.user_id = :member_id AND pm.removed_at IS NULL
             WHERE p.id = :project_id',
            ['project_id' => $projectId, 'member_id' => $userId]
        );

        if (!$project) {
            return false;
        }

        return $project['owner_id'] === $userId || $project['role'] === 'manager';
    }

    public static function archive(string $projectId): bool
    {
        return self::update($projectId, ['archived_at' => date('c')]);
    }

    public static function unarchive(string $projectId): bool
    {
        $db = Database::getInstance();
        $affected = $db->update(
            self::$table,
            ['archived_at' => null],
            'id = :id',
            ['id' => $projectId]
        );
        return $affected > 0;
    }
}
