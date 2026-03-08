<?php

declare(strict_types=1);

namespace TrackNote\Services;

use TrackNote\Core\Database;

class ReportService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getSummary(
        string $userId,
        string $period = 'week',
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        [$start, $end] = $this->getPeriodDates($period, $startDate, $endDate);

        $summary = $this->db->fetch(
            "SELECT
                COUNT(*) as total_entries,
                COUNT(DISTINCT project_id) as total_projects,
                COALESCE(SUM(
                    CASE WHEN ended_at IS NOT NULL
                    THEN EXTRACT(EPOCH FROM (ended_at - started_at)) - total_paused_seconds
                    ELSE 0 END
                ), 0) as total_seconds
             FROM work_entries
             WHERE user_id = :user_id
             AND started_at >= :start_date
             AND started_at <= :end_date",
            [
                'user_id' => $userId,
                'start_date' => $start,
                'end_date' => $end,
            ]
        );

        $dailyBreakdown = $this->db->fetchAll(
            "SELECT
                DATE(started_at) as date,
                COUNT(*) as entries,
                COALESCE(SUM(
                    CASE WHEN ended_at IS NOT NULL
                    THEN EXTRACT(EPOCH FROM (ended_at - started_at)) - total_paused_seconds
                    ELSE 0 END
                ), 0) as seconds
             FROM work_entries
             WHERE user_id = :user_id
             AND started_at >= :start_date
             AND started_at <= :end_date
             GROUP BY DATE(started_at)
             ORDER BY date",
            [
                'user_id' => $userId,
                'start_date' => $start,
                'end_date' => $end,
            ]
        );

        return [
            'period' => $period,
            'start_date' => $start,
            'end_date' => $end,
            'total_entries' => (int) $summary['total_entries'],
            'total_projects' => (int) $summary['total_projects'],
            'total_seconds' => (int) $summary['total_seconds'],
            'total_hours' => round(((int) $summary['total_seconds']) / 3600, 2),
            'daily_breakdown' => array_map(fn($day) => [
                'date' => $day['date'],
                'entries' => (int) $day['entries'],
                'seconds' => (int) $day['seconds'],
                'hours' => round(((int) $day['seconds']) / 3600, 2),
            ], $dailyBreakdown),
        ];
    }

    public function getByProject(
        string $userId,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $organizationId = null
    ): array {
        [$start, $end] = $this->getPeriodDates('custom', $startDate, $endDate);

        $orgCondition = '';
        $params = [
            'user_id' => $userId,
            'start_date' => $start,
            'end_date' => $end,
        ];

        if ($organizationId !== null) {
            $orgCondition = 'AND p.organization_id = :org_id';
            $params['org_id'] = $organizationId;
        }

        return $this->db->fetchAll(
            "SELECT
                p.id as project_id,
                p.name as project_name,
                p.color as project_color,
                COUNT(we.id) as total_entries,
                COALESCE(SUM(
                    CASE WHEN we.ended_at IS NOT NULL
                    THEN EXTRACT(EPOCH FROM (we.ended_at - we.started_at)) - we.total_paused_seconds
                    ELSE 0 END
                ), 0) as total_seconds
             FROM work_entries we
             INNER JOIN projects p ON we.project_id = p.id
             WHERE we.user_id = :user_id
             AND we.started_at >= :start_date
             AND we.started_at <= :end_date
             $orgCondition
             GROUP BY p.id, p.name, p.color
             ORDER BY total_seconds DESC",
            $params
        );
    }

    public function getByUser(
        string $organizationId,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $projectId = null
    ): array {
        [$start, $end] = $this->getPeriodDates('custom', $startDate, $endDate);

        $projectCondition = '';
        $params = [
            'org_id' => $organizationId,
            'start_date' => $start,
            'end_date' => $end,
        ];

        if ($projectId !== null) {
            $projectCondition = 'AND we.project_id = :project_id';
            $params['project_id'] = $projectId;
        }

        return $this->db->fetchAll(
            "SELECT
                u.id as user_id,
                u.name as user_name,
                u.email as user_email,
                u.avatar_url,
                COUNT(we.id) as total_entries,
                COALESCE(SUM(
                    CASE WHEN we.ended_at IS NOT NULL
                    THEN EXTRACT(EPOCH FROM (we.ended_at - we.started_at)) - we.total_paused_seconds
                    ELSE 0 END
                ), 0) as total_seconds
             FROM work_entries we
             INNER JOIN projects p ON we.project_id = p.id
             INNER JOIN users u ON we.user_id = u.id
             WHERE p.organization_id = :org_id
             AND we.started_at >= :start_date
             AND we.started_at <= :end_date
             $projectCondition
             GROUP BY u.id, u.name, u.email, u.avatar_url
             ORDER BY total_seconds DESC",
            $params
        );
    }

    public function exportToCsv(
        string $userId,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $projectId = null
    ): string {
        [$start, $end] = $this->getPeriodDates('custom', $startDate, $endDate);

        $projectCondition = '';
        $params = [
            'user_id' => $userId,
            'start_date' => $start,
            'end_date' => $end,
        ];

        if ($projectId !== null) {
            $projectCondition = 'AND we.project_id = :project_id';
            $params['project_id'] = $projectId;
        }

        $entries = $this->db->fetchAll(
            "SELECT
                we.id,
                we.title,
                we.started_at,
                we.ended_at,
                we.total_paused_seconds,
                we.is_manual,
                p.name as project_name,
                CASE WHEN we.ended_at IS NOT NULL
                    THEN EXTRACT(EPOCH FROM (we.ended_at - we.started_at)) - we.total_paused_seconds
                    ELSE 0 END as duration_seconds
             FROM work_entries we
             INNER JOIN projects p ON we.project_id = p.id
             WHERE we.user_id = :user_id
             AND we.started_at >= :start_date
             AND we.started_at <= :end_date
             $projectCondition
             ORDER BY we.started_at",
            $params
        );

        $output = fopen('php://temp', 'r+');

        fputcsv($output, [
            'ID',
            'Project',
            'Title',
            'Started At',
            'Ended At',
            'Duration (seconds)',
            'Duration (hours)',
            'Paused (seconds)',
            'Manual Entry',
        ]);

        foreach ($entries as $entry) {
            fputcsv($output, [
                $entry['id'],
                $entry['project_name'],
                $entry['title'],
                $entry['started_at'],
                $entry['ended_at'] ?? '',
                (int) $entry['duration_seconds'],
                round(((int) $entry['duration_seconds']) / 3600, 2),
                (int) $entry['total_paused_seconds'],
                $entry['is_manual'] ? 'Yes' : 'No',
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    private function getPeriodDates(string $period, ?string $startDate, ?string $endDate): array
    {
        if ($startDate !== null && $endDate !== null) {
            return [$startDate, $endDate];
        }

        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        return match ($period) {
            'day' => [
                $now->format('Y-m-d 00:00:00'),
                $now->format('Y-m-d 23:59:59'),
            ],
            'week' => [
                $now->modify('monday this week')->format('Y-m-d 00:00:00'),
                $now->modify('sunday this week')->format('Y-m-d 23:59:59'),
            ],
            'month' => [
                $now->modify('first day of this month')->format('Y-m-d 00:00:00'),
                $now->modify('last day of this month')->format('Y-m-d 23:59:59'),
            ],
            'year' => [
                $now->format('Y-01-01 00:00:00'),
                $now->format('Y-12-31 23:59:59'),
            ],
            default => [
                $now->modify('-30 days')->format('Y-m-d 00:00:00'),
                $now->format('Y-m-d 23:59:59'),
            ],
        };
    }
}
