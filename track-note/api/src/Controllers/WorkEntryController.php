<?php

declare(strict_types=1);

namespace TrackNote\Controllers;

use TrackNote\Core\Request;
use TrackNote\Core\Response;
use TrackNote\Core\Session;
use TrackNote\Models\WorkEntry;
use TrackNote\Models\Project;
use TrackNote\Models\Timer;
use TrackNote\Services\AuditService;

class WorkEntryController
{
    private AuditService $auditService;

    public function __construct()
    {
        $this->auditService = new AuditService();
    }

    public function index(Request $request): Response
    {
        $userId = Session::getUserId();

        $projectId = $request->query("project_id");
        $startDate = $request->query("start_date");
        $endDate = $request->query("end_date");
        $limit = (int) ($request->query("limit") ?? 50);
        $offset = (int) ($request->query("offset") ?? 0);

        $entries = WorkEntry::getAllForUser(
            $userId,
            $projectId,
            $startDate,
            $endDate,
            $limit,
            $offset,
        );

        foreach ($entries as &$entry) {
            $entry["duration_seconds"] = WorkEntry::calculateDuration($entry);
        }

        return Response::success($entries);
    }

    public function store(Request $request): Response
    {
        $userId = Session::getUserId();

        try {
            $data = $request->validate([
                "project_id" => "required|uuid",
                "title" => "required|string|min:1|max:255",
                "description" => "nullable|string",
                "started_at" => "required|datetime",
                "ended_at" => "nullable|datetime",
                "overlap_reason" => "nullable|string|max:500",
            ]);
        } catch (\InvalidArgumentException $e) {
            $errors = json_decode($e->getMessage(), true);
            return Response::validationError($errors);
        }

        if (!Project::isMember($data["project_id"], $userId)) {
            return Response::forbidden("You are not a member of this project");
        }

        // If ended_at is provided, create a completed entry
        if (!empty($data["ended_at"])) {
            if (
                strtotime($data["ended_at"]) <= strtotime($data["started_at"])
            ) {
                return Response::error(
                    "End time must be after start time",
                    400,
                );
            }

            $overlap = WorkEntry::checkOverlap(
                $userId,
                $data["started_at"],
                $data["ended_at"],
            );
            if ($overlap !== null && empty($data["overlap_reason"])) {
                return Response::error(
                    "This entry overlaps with another entry. Please provide an overlap_reason.",
                    409,
                    ["overlapping_entry" => $overlap],
                );
            }

            $entryId = WorkEntry::create([
                "project_id" => $data["project_id"],
                "user_id" => $userId,
                "title" => $data["title"],
                "description" => $data["description"] ?? null,
                "started_at" => $data["started_at"],
                "ended_at" => $data["ended_at"],
                "is_manual" => true,
                "overlap_reason" => $data["overlap_reason"] ?? null,
            ]);

            $entry = WorkEntry::find($entryId);
            $this->auditService->log(
                "work_entry",
                $entryId,
                "create",
                null,
                $entry,
            );

            return Response::created($entry, "Work entry created successfully");
        }

        // No ended_at means we're creating an active entry (starting a timer)
        $existingTimer = Timer::findByUser($userId);
        if ($existingTimer !== null) {
            return Response::error(
                "A timer is already running. Stop it first or use transition.",
                409,
            );
        }

        $overlap = WorkEntry::checkOverlap($userId, $data["started_at"], null);
        if ($overlap !== null && empty($data["overlap_reason"])) {
            return Response::error(
                "This entry overlaps with another entry. Please provide an overlap_reason.",
                409,
                ["overlapping_entry" => $overlap],
            );
        }

        // Create work entry and active_timer in a transaction
        $db = \TrackNote\Core\Database::getInstance();
        $result = $db->transaction(function ($db) use ($userId, $data) {
            $entryId = $db->insert("work_entries", [
                "project_id" => $data["project_id"],
                "user_id" => $userId,
                "title" => $data["title"],
                "description" => $data["description"] ?? null,
                "started_at" => $data["started_at"],
                "is_manual" => true,
                "overlap_reason" => $data["overlap_reason"] ?? null,
            ]);

            $db->insert("active_timers", [
                "user_id" => $userId,
                "work_entry_id" => $entryId,
                "status" => "running",
            ]);

            return $entryId;
        });

        $entry = WorkEntry::find($result);

        $this->auditService->log("work_entry", $result, "create", null, $entry);

        return Response::created($entry, "Work entry created successfully");
    }

    public function show(Request $request): Response
    {
        $userId = Session::getUserId();
        $entryId = $request->param("id");

        $entry = WorkEntry::findForUser($entryId, $userId);

        if ($entry === null) {
            return Response::notFound("Work entry not found");
        }

        $entry["duration_seconds"] = WorkEntry::calculateDuration($entry);
        $entry["blocks"] = WorkEntry::getBlocks($entryId);

        return Response::success($entry);
    }

    public function update(Request $request): Response
    {
        $userId = Session::getUserId();
        $entryId = $request->param("id");

        $entry = WorkEntry::findForUser($entryId, $userId);

        if ($entry === null) {
            return Response::notFound("Work entry not found");
        }

        try {
            $data = $request->validate([
                "project_id" => "nullable|uuid",
                "title" => "nullable|string|min:1|max:255",
                "description" => "nullable|string",
                "started_at" => "nullable|datetime",
                "ended_at" => "nullable|datetime",
                "overlap_reason" => "nullable|string|max:500",
            ]);
        } catch (\InvalidArgumentException $e) {
            $errors = json_decode($e->getMessage(), true);
            return Response::validationError($errors);
        }

        // Handle explicit null for ended_at (to make entry active)
        $rawBody = $request->body();
        $setEndedAtNull =
            array_key_exists("ended_at", $rawBody) &&
            $rawBody["ended_at"] === null;

        $updateData = array_filter($data, fn($v) => $v !== null);

        // Add ended_at = null if explicitly set
        if ($setEndedAtNull) {
            $updateData["ended_at"] = null;
        }

        if (empty($updateData)) {
            return Response::error("No fields to update", 400);
        }

        // Check project membership if changing project
        if (isset($updateData["project_id"])) {
            if (!Project::isMember($updateData["project_id"], $userId)) {
                return Response::forbidden(
                    "You are not a member of this project",
                );
            }
        }

        $startedAt = $updateData["started_at"] ?? $entry["started_at"];
        $endedAt = $updateData["ended_at"] ?? $entry["ended_at"];

        if ($endedAt !== null && strtotime($endedAt) <= strtotime($startedAt)) {
            return Response::error("End time must be after start time", 400);
        }

        if (
            isset($updateData["started_at"]) ||
            isset($updateData["ended_at"])
        ) {
            $overlap = WorkEntry::checkOverlap(
                $userId,
                $startedAt,
                $endedAt,
                $entryId,
            );
            if ($overlap !== null && empty($updateData["overlap_reason"])) {
                return Response::error(
                    "Updated times overlap with another entry. Please provide an overlap_reason.",
                    409,
                    ["overlapping_entry" => $overlap],
                );
            }
        }

        $oldEntry = $entry;
        WorkEntry::update($entryId, $updateData);
        $newEntry = WorkEntry::find($entryId);

        $this->auditService->log(
            "work_entry",
            $entryId,
            "update",
            array_intersect_key($oldEntry, $updateData),
            $updateData,
        );

        return Response::success($newEntry, "Work entry updated successfully");
    }

    public function destroy(Request $request): Response
    {
        $userId = Session::getUserId();
        $entryId = $request->param("id");

        $entry = WorkEntry::findForUser($entryId, $userId);

        if ($entry === null) {
            return Response::notFound("Work entry not found");
        }

        $this->auditService->log(
            "work_entry",
            $entryId,
            "delete",
            $entry,
            null,
        );

        WorkEntry::delete($entryId);

        return Response::success(null, "Work entry deleted successfully");
    }

    public function blocks(Request $request): Response
    {
        $userId = Session::getUserId();
        $entryId = $request->param("id");

        $entry = WorkEntry::findForUser($entryId, $userId);

        if ($entry === null) {
            return Response::notFound("Work entry not found");
        }

        $blocks = WorkEntry::getBlocks($entryId);

        return Response::success($blocks);
    }

    public function addBlock(Request $request): Response
    {
        $userId = Session::getUserId();
        $entryId = $request->param("id");

        $entry = WorkEntry::findForUser($entryId, $userId);

        if ($entry === null) {
            return Response::notFound("Work entry not found");
        }

        try {
            $data = $request->validate([
                "title" => "required|string|min:1|max:255",
                "notes" => "nullable|string",
                "block_start" => "nullable|datetime",
                "block_end" => "nullable|datetime",
            ]);
        } catch (\InvalidArgumentException $e) {
            $errors = json_decode($e->getMessage(), true);
            return Response::validationError($errors);
        }

        $blockId = WorkEntry::addBlock($entryId, $data);
        $block = WorkEntry::getBlock($blockId);

        $this->auditService->log(
            "work_block",
            $blockId,
            "create",
            null,
            $block,
        );

        return Response::created($block, "Block added successfully");
    }

    public function updateBlock(Request $request): Response
    {
        $userId = Session::getUserId();
        $entryId = $request->param("id");
        $blockId = $request->param("blockId");

        $entry = WorkEntry::findForUser($entryId, $userId);

        if ($entry === null) {
            return Response::notFound("Work entry not found");
        }

        $block = WorkEntry::getBlock($blockId);

        if ($block === null || $block["work_entry_id"] !== $entryId) {
            return Response::notFound("Block not found");
        }

        try {
            $data = $request->validate([
                "title" => "nullable|string|min:1|max:255",
                "notes" => "nullable|string",
                "block_start" => "nullable|datetime",
                "block_end" => "nullable|datetime",
                "sort_order" => "nullable|integer",
            ]);
        } catch (\InvalidArgumentException $e) {
            $errors = json_decode($e->getMessage(), true);
            return Response::validationError($errors);
        }

        $updateData = array_filter($data, fn($v) => $v !== null);

        if (empty($updateData)) {
            return Response::error("No fields to update", 400);
        }

        $oldBlock = $block;
        WorkEntry::updateBlock($blockId, $updateData);
        $newBlock = WorkEntry::getBlock($blockId);

        $this->auditService->log(
            "work_block",
            $blockId,
            "update",
            array_intersect_key($oldBlock, $updateData),
            $updateData,
        );

        return Response::success($newBlock, "Block updated successfully");
    }

    public function removeBlock(Request $request): Response
    {
        $userId = Session::getUserId();
        $entryId = $request->param("id");
        $blockId = $request->param("blockId");

        $entry = WorkEntry::findForUser($entryId, $userId);

        if ($entry === null) {
            return Response::notFound("Work entry not found");
        }

        $block = WorkEntry::getBlock($blockId);

        if ($block === null || $block["work_entry_id"] !== $entryId) {
            return Response::notFound("Block not found");
        }

        $this->auditService->log(
            "work_block",
            $blockId,
            "delete",
            $block,
            null,
        );

        WorkEntry::deleteBlock($blockId);

        return Response::success(null, "Block deleted successfully");
    }

    public function history(Request $request): Response
    {
        $userId = Session::getUserId();
        $entryId = $request->param("id");

        $entry = WorkEntry::findForUser($entryId, $userId);

        if ($entry === null) {
            return Response::notFound("Work entry not found");
        }

        $history = $this->auditService->getEntityHistory(
            "work_entry",
            $entryId,
        );

        return Response::success($history);
    }
}
