<?php

declare(strict_types=1);

namespace TrackNote\Controllers;

use TrackNote\Core\Request;
use TrackNote\Core\Response;
use TrackNote\Core\Session;
use TrackNote\Models\Project;
use TrackNote\Models\User;
use TrackNote\Services\AuditService;

class ProjectController
{
    private AuditService $auditService;

    public function __construct()
    {
        $this->auditService = new AuditService();
    }

    public function index(Request $request): Response
    {
        $userId = Session::getUserId();
        $includeArchived = filter_var($request->query('include_archived', false), FILTER_VALIDATE_BOOLEAN);

        $projects = Project::getAllForUser($userId, $includeArchived);

        return Response::success($projects);
    }

    public function store(Request $request): Response
    {
        $userId = Session::getUserId();

        try {
            $data = $request->validate([
                'name' => 'required|string|min:2|max:255',
                'description' => 'nullable|string|max:1000',
                'color' => 'nullable|string|max:7',
                'organization_id' => 'nullable|uuid',
            ]);
        } catch (\InvalidArgumentException $e) {
            $errors = json_decode($e->getMessage(), true);
            return Response::validationError($errors);
        }

        $projectData = [
            'owner_id' => $userId,
            'name' => $data['name'],
        ];

        if (!empty($data['description'])) {
            $projectData['description'] = $data['description'];
        }

        if (!empty($data['color'])) {
            $projectData['color'] = $data['color'];
        }

        if (!empty($data['organization_id'])) {
            $projectData['organization_id'] = $data['organization_id'];
        }

        $projectId = Project::create($projectData);
        $project = Project::find($projectId);

        $this->auditService->log('project', $projectId, 'create', null, $project);

        return Response::created($project, 'Project created successfully');
    }

    public function show(Request $request): Response
    {
        $userId = Session::getUserId();
        $projectId = $request->param('id');

        $project = Project::findForUser($projectId, $userId);

        if ($project === null) {
            return Response::notFound('Project not found');
        }

        $project['members'] = Project::getMembers($projectId);

        return Response::success($project);
    }

    public function update(Request $request): Response
    {
        $userId = Session::getUserId();
        $projectId = $request->param('id');

        $project = Project::findForUser($projectId, $userId);

        if ($project === null) {
            return Response::notFound('Project not found');
        }

        if (!Project::isManager($projectId, $userId)) {
            return Response::forbidden('You do not have permission to update this project');
        }

        try {
            $data = $request->validate([
                'name' => 'nullable|string|min:2|max:255',
                'description' => 'nullable|string|max:1000',
                'color' => 'nullable|string|max:7',
            ]);
        } catch (\InvalidArgumentException $e) {
            $errors = json_decode($e->getMessage(), true);
            return Response::validationError($errors);
        }

        $updateData = array_filter($data, fn($v) => $v !== null);

        if (empty($updateData)) {
            return Response::error('No fields to update', 400);
        }

        $oldProject = $project;
        Project::update($projectId, $updateData);
        $newProject = Project::find($projectId);

        $this->auditService->log(
            'project',
            $projectId,
            'update',
            array_intersect_key($oldProject, $updateData),
            $updateData
        );

        return Response::success($newProject, 'Project updated successfully');
    }

    public function destroy(Request $request): Response
    {
        $userId = Session::getUserId();
        $projectId = $request->param('id');

        $project = Project::findForUser($projectId, $userId);

        if ($project === null) {
            return Response::notFound('Project not found');
        }

        if ($project['owner_id'] !== $userId) {
            return Response::forbidden('Only the project owner can archive this project');
        }

        Project::archive($projectId);

        $this->auditService->log('project', $projectId, 'archive', null, [
            'archived_at' => date('c'),
        ]);

        return Response::success(null, 'Project archived successfully');
    }

    public function members(Request $request): Response
    {
        $userId = Session::getUserId();
        $projectId = $request->param('id');

        $project = Project::findForUser($projectId, $userId);

        if ($project === null) {
            return Response::notFound('Project not found');
        }

        $members = Project::getMembers($projectId);

        if ($project['owner_id'] === $userId) {
            $owner = User::find($project['owner_id']);
            array_unshift($members, [
                'id' => $owner['id'],
                'name' => $owner['name'],
                'email' => $owner['email'],
                'avatar_url' => $owner['avatar_url'],
                'role' => 'owner',
                'assigned_at' => $project['created_at'],
            ]);
        }

        return Response::success($members);
    }

    public function addMember(Request $request): Response
    {
        $userId = Session::getUserId();
        $projectId = $request->param('id');

        $project = Project::findForUser($projectId, $userId);

        if ($project === null) {
            return Response::notFound('Project not found');
        }

        if (!Project::isManager($projectId, $userId)) {
            return Response::forbidden('You do not have permission to add members');
        }

        try {
            $data = $request->validate([
                'user_id' => 'required|uuid',
                'role' => 'nullable|string',
            ]);
        } catch (\InvalidArgumentException $e) {
            $errors = json_decode($e->getMessage(), true);
            return Response::validationError($errors);
        }

        $memberUser = User::find($data['user_id']);
        if ($memberUser === null) {
            return Response::notFound('User not found');
        }

        try {
            $role = $data['role'] ?? 'member';
            Project::addMember($projectId, $data['user_id'], $role);

            $this->auditService->log('project', $projectId, 'add_member', null, [
                'user_id' => $data['user_id'],
                'role' => $role,
            ]);

            return Response::created(null, 'Member added successfully');
        } catch (\InvalidArgumentException $e) {
            return Response::error($e->getMessage(), 409);
        }
    }

    public function updateMember(Request $request): Response
    {
        $currentUserId = Session::getUserId();
        $projectId = $request->param('id');
        $memberId = $request->param('userId');

        $project = Project::findForUser($projectId, $currentUserId);

        if ($project === null) {
            return Response::notFound('Project not found');
        }

        if (!Project::isManager($projectId, $currentUserId)) {
            return Response::forbidden('You do not have permission to update members');
        }

        try {
            $data = $request->validate([
                'role' => 'required|string',
            ]);
        } catch (\InvalidArgumentException $e) {
            $errors = json_decode($e->getMessage(), true);
            return Response::validationError($errors);
        }

        $updated = Project::updateMemberRole($projectId, $memberId, $data['role']);

        if (!$updated) {
            return Response::notFound('Member not found');
        }

        $this->auditService->log('project', $projectId, 'update_member', null, [
            'user_id' => $memberId,
            'role' => $data['role'],
        ]);

        return Response::success(null, 'Member role updated successfully');
    }

    public function removeMember(Request $request): Response
    {
        $currentUserId = Session::getUserId();
        $projectId = $request->param('id');
        $memberId = $request->param('userId');

        $project = Project::findForUser($projectId, $currentUserId);

        if ($project === null) {
            return Response::notFound('Project not found');
        }

        if (!Project::isManager($projectId, $currentUserId)) {
            return Response::forbidden('You do not have permission to remove members');
        }

        if ($project['owner_id'] === $memberId) {
            return Response::error('Cannot remove the project owner', 400);
        }

        $removed = Project::removeMember($projectId, $memberId);

        if (!$removed) {
            return Response::notFound('Member not found');
        }

        $this->auditService->log('project', $projectId, 'remove_member', [
            'user_id' => $memberId,
        ], null);

        return Response::success(null, 'Member removed successfully');
    }
}
