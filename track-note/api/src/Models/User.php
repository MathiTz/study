<?php

declare(strict_types=1);

namespace TrackNote\Models;

use TrackNote\Core\Database;

class User extends Model
{
    protected static string $table = "users";

    public static function findByEmail(string $email): ?array
    {
        return self::findBy("email", strtolower($email));
    }

    public static function create(array $data): string
    {
        $data["email"] = strtolower($data["email"]);
        $data["password_hash"] = password_hash(
            $data["password"],
            PASSWORD_ARGON2ID,
        );
        unset($data["password"]);

        return parent::create($data);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function updatePassword(
        string $userId,
        string $newPassword,
    ): bool {
        return self::update($userId, [
            "password_hash" => password_hash($newPassword, PASSWORD_ARGON2ID),
        ]);
    }
    /**
     * @param array<int,mixed> $user
     * @return array<int,mixed>
     */
    public static function toPublic(array $user): array
    {
        unset($user["password_hash"]);
        return $user;
    }

    public static function getOrganizations(string $userId): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            'SELECT o.*, om.role, om.joined_at
             FROM organizations o
             INNER JOIN organization_members om ON o.id = om.organization_id
             WHERE om.user_id = :user_id AND om.removed_at IS NULL
             ORDER BY o.name',
            ["user_id" => $userId],
        );
    }

    public static function getProjects(string $userId): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            'SELECT DISTINCT p.*, pm.role as member_role
             FROM projects p
             LEFT JOIN project_members pm ON p.id = pm.project_id AND pm.user_id = :user_id
             WHERE (p.owner_id = :owner_id OR (pm.user_id = :member_id AND pm.removed_at IS NULL))
             AND p.archived_at IS NULL
             ORDER BY p.name',
            [
                "user_id" => $userId,
                "owner_id" => $userId,
                "member_id" => $userId,
            ],
        );
    }
}
