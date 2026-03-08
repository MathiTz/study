<?php

declare(strict_types=1);

namespace TrackNote\Controllers;

use TrackNote\Core\Request;
use TrackNote\Core\Response;
use TrackNote\Core\Session;
use TrackNote\Models\Organization;
use TrackNote\Services\ReportService;
use TrackNote\Services\AuditService;

class ReportController
{
    private ReportService $reportService;
    private AuditService $auditService;

    public function __construct()
    {
        $this->reportService = new ReportService();
        $this->auditService = new AuditService();
    }

    public function summary(Request $request): Response
    {
        $userId = Session::getUserId();

        $period = $request->query('period', 'week');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $summary = $this->reportService->getSummary($userId, $period, $startDate, $endDate);

        return Response::success($summary);
    }

    public function byProject(Request $request): Response
    {
        $userId = Session::getUserId();

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $organizationId = $request->query('organization_id');

        $report = $this->reportService->getByProject($userId, $startDate, $endDate, $organizationId);

        $totalSeconds = array_sum(array_column($report, 'total_seconds'));

        $reportWithPercentage = array_map(function ($item) use ($totalSeconds) {
            $item['total_seconds'] = (int) $item['total_seconds'];
            $item['total_entries'] = (int) $item['total_entries'];
            $item['total_hours'] = round($item['total_seconds'] / 3600, 2);
            $item['percentage'] = $totalSeconds > 0
                ? round(($item['total_seconds'] / $totalSeconds) * 100, 1)
                : 0;
            return $item;
        }, $report);

        return Response::success([
            'projects' => $reportWithPercentage,
            'total_seconds' => $totalSeconds,
            'total_hours' => round($totalSeconds / 3600, 2),
        ]);
    }

    public function byUser(Request $request): Response
    {
        $userId = Session::getUserId();

        $organizationId = $request->query('organization_id');

        if (empty($organizationId)) {
            return Response::error('organization_id is required', 400);
        }

        if (!Organization::isAdmin($organizationId, $userId)) {
            return Response::forbidden('Only admins can view reports by user');
        }

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $projectId = $request->query('project_id');

        $report = $this->reportService->getByUser($organizationId, $startDate, $endDate, $projectId);

        $totalSeconds = array_sum(array_column($report, 'total_seconds'));

        $reportWithPercentage = array_map(function ($item) use ($totalSeconds) {
            $item['total_seconds'] = (int) $item['total_seconds'];
            $item['total_entries'] = (int) $item['total_entries'];
            $item['total_hours'] = round($item['total_seconds'] / 3600, 2);
            $item['percentage'] = $totalSeconds > 0
                ? round(($item['total_seconds'] / $totalSeconds) * 100, 1)
                : 0;
            return $item;
        }, $report);

        return Response::success([
            'users' => $reportWithPercentage,
            'total_seconds' => $totalSeconds,
            'total_hours' => round($totalSeconds / 3600, 2),
        ]);
    }

    public function export(Request $request): Response
    {
        $userId = Session::getUserId();

        $format = $request->query('format', 'csv');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $projectId = $request->query('project_id');

        if ($format !== 'csv') {
            return Response::error('Only CSV format is currently supported', 400);
        }

        $csv = $this->reportService->exportToCsv($userId, $startDate, $endDate, $projectId);

        $response = new Response($csv);
        $response->header('Content-Type', 'text/csv');
        $response->header('Content-Disposition', 'attachment; filename="tracknote-export.csv"');

        return $response;
    }

    public function auditLogs(Request $request): Response
    {
        $userId = Session::getUserId();

        $entityType = $request->query('entity_type');
        $entityId = $request->query('entity_id');
        $limit = (int) ($request->query('limit') ?? 50);
        $offset = (int) ($request->query('offset') ?? 0);

        if ($entityType && $entityId) {
            $logs = $this->auditService->getEntityHistory($entityType, $entityId, $limit, $offset);
        } else {
            $logs = $this->auditService->getUserLogs($userId, $limit, $offset);
        }

        return Response::success($logs);
    }
}
