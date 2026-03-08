<?php

declare(strict_types=1);

namespace TrackNote\Tests\Integration\Services;

use TrackNote\Tests\TestCase;

class ReportServiceTest extends TestCase
{
    public function testPeriodDatesForDay(): void
    {
        $reflection = new \ReflectionClass(
            \TrackNote\Services\ReportService::class,
        );
        $method = $reflection->getMethod("getPeriodDates");
        $method->setAccessible(true);

        $service = new \TrackNote\Services\ReportService();
        [$start, $end] = $method->invoke($service, "day", null, null);

        $today = date("Y-m-d");
        $this->assertStringStartsWith($today, $start);
        $this->assertStringStartsWith($today, $end);
    }

    public function testPeriodDatesForWeek(): void
    {
        $reflection = new \ReflectionClass(
            \TrackNote\Services\ReportService::class,
        );
        $method = $reflection->getMethod("getPeriodDates");
        $method->setAccessible(true);

        $service = new \TrackNote\Services\ReportService();
        [$start, $end] = $method->invoke($service, "week", null, null);

        $startDate = new \DateTime($start);
        $endDate = new \DateTime($end);

        $this->assertEquals("Monday", $startDate->format("l"));
        $this->assertEquals("Sunday", $endDate->format("l"));
    }

    public function testPeriodDatesForMonth(): void
    {
        $reflection = new \ReflectionClass(
            \TrackNote\Services\ReportService::class,
        );
        $method = $reflection->getMethod("getPeriodDates");
        $method->setAccessible(true);

        $service = new \TrackNote\Services\ReportService();
        [$start, $end] = $method->invoke($service, "month", null, null);

        $startDate = new \DateTime($start);
        $endDate = new \DateTime($end);

        $this->assertEquals("01", $startDate->format("d"));
        $this->assertEquals($endDate->format("t"), $endDate->format("d"));
    }

    public function testPeriodDatesForYear(): void
    {
        $reflection = new \ReflectionClass(
            \TrackNote\Services\ReportService::class,
        );
        $method = $reflection->getMethod("getPeriodDates");
        $method->setAccessible(true);

        $service = new \TrackNote\Services\ReportService();
        [$start, $end] = $method->invoke($service, "year", null, null);

        $currentYear = date("Y");
        $this->assertStringStartsWith("$currentYear-01-01", $start);
        $this->assertStringStartsWith("$currentYear-12-31", $end);
    }

    public function testPeriodDatesWithCustomDates(): void
    {
        $reflection = new \ReflectionClass(
            \TrackNote\Services\ReportService::class,
        );
        $method = $reflection->getMethod("getPeriodDates");
        $method->setAccessible(true);

        $service = new \TrackNote\Services\ReportService();
        [$start, $end] = $method->invoke(
            $service,
            "custom",
            "2024-01-01",
            "2024-12-31",
        );

        $this->assertEquals("2024-01-01", $start);
        $this->assertEquals("2024-12-31", $end);
    }
}
