<?php

declare(strict_types=1);

namespace TrackNote\Models;

use TrackNote\Core\Database;

abstract class Model
{
    protected static string $table;
    protected static string $primaryKey = "id";
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public static function getTable(): string
    {
        return static::$table;
    }

    public static function find(string $id): ?array
    {
        $db = Database::getInstance();
        return $db->fetch(
            sprintf(
                "SELECT * FROM %s WHERE %s = :id",
                static::$table,
                static::$primaryKey,
            ),
            ["id" => $id],
        );
    }

    public static function findBy(string $column, mixed $value): ?array
    {
        $db = Database::getInstance();
        return $db->fetch(
            sprintf(
                "SELECT * FROM %s WHERE %s = :value",
                static::$table,
                $column,
            ),
            ["value" => $value],
        );
    }

    public static function all(
        array $conditions = [],
        ?int $limit = null,
        ?int $offset = null,
    ): array {
        $db = Database::getInstance();
        $sql = sprintf("SELECT * FROM %s", static::$table);
        $params = [];

        if (!empty($conditions)) {
            $wheres = [];
            foreach ($conditions as $column => $value) {
                $wheres[] = "$column = :$column";
                $params[$column] = $value;
            }
            $sql .= " WHERE " . implode(" AND ", $wheres);
        }

        $sql .= " ORDER BY created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT $limit";
        }

        if ($offset !== null) {
            $sql .= " OFFSET $offset";
        }

        return $db->fetchAll($sql, $params);
    }

    public static function create(array $data): string
    {
        $db = Database::getInstance();
        return $db->insert(static::$table, $data);
    }

    public static function update(string $id, array $data): bool
    {
        $db = Database::getInstance();
        $affected = $db->update(
            static::$table,
            $data,
            sprintf("%s = :where_id", static::$primaryKey),
            ["where_id" => $id],
        );
        return $affected > 0;
    }

    public static function delete(string $id): bool
    {
        $db = Database::getInstance();
        $affected = $db->delete(
            static::$table,
            sprintf("%s = :id", static::$primaryKey),
            ["id" => $id],
        );
        return $affected > 0;
    }

    public static function exists(string $id): bool
    {
        $db = Database::getInstance();
        $count = $db->fetchColumn(
            sprintf(
                "SELECT COUNT(*) FROM %s WHERE %s = :id",
                static::$table,
                static::$primaryKey,
            ),
            ["id" => $id],
        );
        return (int) $count > 0;
    }

    public static function count(array $conditions = []): int
    {
        $db = Database::getInstance();
        $sql = sprintf("SELECT COUNT(*) FROM %s", static::$table);
        $params = [];

        if (!empty($conditions)) {
            $wheres = [];
            foreach ($conditions as $column => $value) {
                $wheres[] = "$column = :$column";
                $params[$column] = $value;
            }
            $sql .= " WHERE " . implode(" AND ", $wheres);
        }

        return (int) $db->fetchColumn($sql, $params);
    }
}
