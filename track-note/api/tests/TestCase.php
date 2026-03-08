<?php

declare(strict_types=1);

namespace TrackNote\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use TrackNote\Core\Database;

abstract class TestCase extends BaseTestCase
{
    protected Database $db;

    protected function setUp(): void
    {
        parent::setUp();

        $config = [
            "driver" => "pgsql",
            "host" => getenv("DB_TEST_HOST") ?: "localhost",
            "port" => getenv("DB_TEST_PORT") ?: "5433",
            "database" => getenv("DB_TEST_DATABASE") ?: "tracknote_test",
            "username" => getenv("DB_TEST_USERNAME") ?: "postgres",
            "password" => getenv("DB_TEST_PASSWORD") ?: "postgres",
            "charset" => "utf8",
            "schema" => "public",
            "sslmode" => "prefer",
        ];

        Database::resetInstance();
        $this->db = Database::getInstance($config);
    }

    protected function tearDown(): void
    {
        Database::resetInstance();
        parent::tearDown();
    }
}
