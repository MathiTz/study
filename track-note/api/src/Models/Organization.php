<?php

declare(strict_types=1);

namespace TrackNote\Models;

use TrackNote\Core\Database;

class Organization extends Model
{
    protected static string $table = 'organizations';

    public static function findBySlug(string $slug): ?array
    {
        return self::findBy('slug', $slug);
    }

    public static function create(array $data): string
    {
        if (!isset($data['slug'])) {
            $data['slug'] = self::generateSlug($data['name']);
        }

        $orgId = parent::create($data);

        $db = Database::getInstance();
        $db->insert('organization_members', [
            'organization_id' => $orgId,
            'user_id' => $data['owner_id'],
            'role' => 'admin',
        ]);

        return $orgId;
    }

    public static function generateSlug(string $name): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        $baseSlug = $slug;
        $counter = 1;

        while (self::findBySlug($slug) !== null) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public static function getAllForUser(string $userId): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            'SELECT o.*, om.role, om.joined_at
             FROM organizations o
             INNER JOIN organization_members om ON o.id = om.organization_id
             WHERE om.user_id = :user_id AND om.removed_at IS NULL
             ORDER BY o.name',
            ['user_id' => $userId]
        );
    }

    public static function findForUser(string $orgId, string $userId): ?array
    {
        $db = Database::getInstance();
        return $db->fetch(
            'SELECT o.*, om.role as user_role
             FROM organizations o
             INNER JOIN organization_members om ON o.id = om.organization_id
             WHERE o.id = :org_id AND om.user_id = :user_id AND om.removed_at IS NULL',
            ['org_id' => $orgId, 'user_id' => $userId]
        );
    }

    public static function getMembers(string $orgId): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            'SELECT u.id, u.name, u.email, u.avatar_url, om.role, om.joined_at
             FROM organization_members om
             INNER JOIN users u ON om.user_id = u.id
             WHERE om.organization_id = :org_id AND om.removed_at IS NULL
             ORDER BY om.joined_at',
            ['org_id' => $orgId]
        );
    }

    public static function addMember(string $orgId, string $userId, string $role = 'member'): string
    {
        $db = Database::getInstance();

        $existing = $db->fetch(
            'SELECT id, removed_at FROM organization_members WHERE organization_id = :org_id AND user_id = :user_id',
            ['org_id' => $orgId, 'user_id' => $userId]
        );

        if ($existing) {
            if ($existing['removed_at'] !== null) {
                $db->update('organization_members', [
                    'role' => $role,
                    'removed_at' => null,
                    'joined_at' => date('c'),
                ], 'id = :id', ['id' => $existing['id']]);
                return $existing['id'];
            }
            throw new \InvalidArgumentException('User is already a member of this organization');
        }

        return $db->insert('organization_members', [
            'organization_id' => $orgId,
            'user_id' => $userId,
            'role' => $role,
        ]);
    }

    public static function updateMemberRole(string $orgId, string $userId, string $role): bool
    {
        $db = Database::getInstance();
        $affected = $db->update(
            'organization_members',
            ['role' => $role],
            'organization_id = :org_id AND user_id = :user_id AND removed_at IS NULL',
            ['org_id' => $orgId, 'user_id' => $userId]
        );
        return $affected > 0;
    }

    public static function removeMember(string $orgId, string $userId): bool
    {
        $db = Database::getInstance();
        $affected = $db->update(
            'organization_members',
            ['removed_at' => date('c')],
            'organization_id = :org_id AND user_id = :user_id AND removed_at IS NULL',
            ['org_id' => $orgId, 'user_id' => $userId]
        );
        return $affected > 0;
    }

    public static function isMember(string $orgId, string $userId): bool
    {
        $db = Database::getInstance();
        $count = $db->fetchColumn(
            'SELECT COUNT(*) FROM organization_members
             WHERE organization_id = :org_id AND user_id = :user_id AND removed_at IS NULL',
            ['org_id' => $orgId, 'user_id' => $userId]
        );
        return (int) $count > 0;
    }

    public static function isAdmin(string $orgId, string $userId): bool
    {
        $db = Database::getInstance();
        $member = $db->fetch(
            'SELECT role FROM organization_members
             WHERE organization_id = :org_id AND user_id = :user_id AND removed_at IS NULL',
            ['org_id' => $orgId, 'user_id' => $userId]
        );
        return $member !== null && $member['role'] === 'admin';
    }

    public static function getProjects(string $orgId): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            'SELECT * FROM projects WHERE organization_id = :org_id AND archived_at IS NULL ORDER BY name',
            ['org_id' => $orgId]
        );
    }
}
