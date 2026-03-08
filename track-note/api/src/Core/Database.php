<?php

declare(strict_types=1);

namespace TrackNote\Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;
    /**
     * @param array<int,mixed> $config
     */
    private function __construct(array $config)
    {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s;options=\'--client_encoding=%s\'',
            $config["host"],
            $config["port"],
            $config["database"],
            $config["charset"],
        );

        if (!empty($config["sslmode"])) {
            $dsn .= ";sslmode=" . $config["sslmode"];
        }

        try {
            $this->pdo = new PDO(
                $dsn,
                $config["username"],
                $config["password"],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ],
            );

            if (!empty($config["schema"])) {
                $this->pdo->exec("SET search_path TO {$config["schema"]}");
            }

            $this->pdo->exec("SET timezone TO 'UTC'");
        } catch (PDOException $e) {
            throw new PDOException(
                "Database connection failed: " . $e->getMessage(),
            );
        }
    }

    public static function getInstance(?array $config = null): self
    {
        if (self::$instance === null) {
            if ($config === null) {
                $config = require dirname(__DIR__, 2) . "/config/database.php";
            }
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public static function resetInstance(): void
    {
        self::$instance = null;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
    /**
     * @param array<int,mixed> $params
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    /**
     * @param array<int,mixed> $params
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }
    /**
     * @param array<int,mixed> $params
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    /**
     * @param array<int,mixed> $params
     */
    public function fetchColumn(
        string $sql,
        array $params = [],
        int $column = 0,
    ): mixed {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn($column);
    }
    /**
     * @param array<int,mixed> $data
     */
    public function insert(string $table, array $data): string
    {
        $data = $this->convertBooleans($data);
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":" . $col, $columns);

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s) RETURNING id",
            $table,
            implode(", ", $columns),
            implode(", ", $placeholders),
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        return $stmt->fetchColumn();
    }
    /**
     * @param array<int,mixed> $data
     * @param array<int,mixed> $whereParams
     */
    public function update(
        string $table,
        array $data,
        string $where,
        array $whereParams = [],
    ): int {
        $data = $this->convertBooleans($data);
        $sets = array_map(fn($col) => "$col = :$col", array_keys($data));

        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s",
            $table,
            implode(", ", $sets),
            $where,
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_merge($data, $whereParams));
        return $stmt->rowCount();
    }
    /**
     * @param array<int,mixed> $params
     */
    public function delete(
        string $table,
        string $where,
        array $params = [],
    ): int {
        $sql = sprintf("DELETE FROM %s WHERE %s", $table, $where);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }
    /**
     * @param callable(): mixed $callback
     */
    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Convert PHP booleans to PostgreSQL compatible values
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    private function convertBooleans(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $data[$key] = (int) $value;
            }
        }
        return $data;
    }
}
