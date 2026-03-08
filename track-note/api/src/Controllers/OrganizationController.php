<?php

declare(strict_types=1);

namespace TrackNote\Controllers;

use TrackNote\Core\Request;
use TrackNote\Core\Response;
use TrackNote\Core\Session;
use TrackNote\Models\Organization;
use TrackNote\Models\User;
use TrackNote\Services\InviteService;
use TrackNote\Services\AuditService;

class OrganizationController
{
    private InviteService $inviteService;
    private AuditService $auditService;

    public function __construct()
    {
        $this->inviteService = new InviteService();
        $this->auditService = new AuditService();
    }

    public function index(Request $request): Response
    {
        $userId = Session::getUserId();
        $organizations = Organization::getAllForUser($userId);

        return Response::success($organizations);
    }

    public function store(Request $request): Response
    {
        $userId = Session::getUserId();

        try {
            $data = $request->validate([
                'name' => 'required|string|min:2|max:255',
                'slug' => 'nullable|string|max:255',
            ]);
        } catch (\InvalidArgumentException $e) {
            $errors = json_decode($e->getMessage(), true);
            return Response::validationError($errors);
        }

        if (!empty($data['slug'])) {
            $existing = Organization::findBySlug($data['slug']);
            if ($existing !== null) {
                return Response::error('This slug is already taken', 409);
            }
        }

        $orgId = Organization::create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? null,
            'owner_id' => $userId,
        ]);

        $org = Organization::find($orgId);

        $this->auditService->log('organization', $orgId, 'create', null, $org);

        return Response::created($org, 'Organization created successfully');
    }

    public function show(Request $request): Response
    {
        $userId = Session::getUserId();
        $orgId = $request->param('id');

        $org = Organization::findForUser($orgId, $userId);

        if ($org === null) {
            return Response::notFound('Organization not found');
        }

        $org['members'] = Organization::getMembers($orgId);
        $org['projects'] = Organization::getProjects($orgId);

        return Response::success($org);
    }

    public function update(Request $request): Response
    {
        $userId = Session::getUserId();
        $orgId = $request->param('id');

        $org = Organization::findForUser($orgId, $userId);

        if ($org === null) {
            return Response::notFound('Organization not found');
        }

        if (!Organization::isAdmin($orgId, $userId)) {
            return Response::forbidden('Only admins can update the organization');
        }

        try {
            $data = $request->validate([
                'name' => 'nullable|string|min:2|max:255',
                'logo_url' => 'nullable|string|max:500',
            ]);
        } catch (\InvalidArgumentException $e) {
            $errors = json_decode($e->getMessage(), true);
            return Response::validationError($errors);
        }

        $updateData = array_filter($data, fn($v) => $v !== null);

        if (empty($updateData)) {
            return Response::error('No fields to update', 400);
        }

        $oldOrg = $org;
        Organization::update($orgId, $updateData);
        $newOrg = Organization::find($orgId);

        $this->auditService->log(
            'organization',
            $orgId,
            'update',
            array_intersect_key($oldOrg, $updateData),
            $updateData
        );

        return Response::success($newOrg, 'Organization updated successfully');
    }

    public function destroy(Request $request): Response
    {
        $userId = Session::getUserId();
        $orgId = $request->param('id');

        $org = Organization::find($orgId);

        if ($org === null) {
            return Response::notFound('Organization not found');
        }

        if ($org['owner_id'] !== $userId) {
            return Response::forbidden('Only the owner can delete this organization');
        }

        $this->auditService->log('organization', $orgId, 'delete', $org, null);

        Organization::delete($orgId);

        return Response::success(null, 'Organization deleted successfully');
    }

    public function members(Request $request): Response
    {
        $userId = Session::getUserId();
        $orgId = $request->param('id');

        $org = Organization::findForUser($orgId, $userId);

        if ($org === null) {
            return Response::notFound('Organization not found');
        }

        $members = Organization::getMembers($orgId);

        return Response::success($members);
    }

    public function addMember(Request $request): Response
    {
        $userId = Session::getUserId();
        $orgId = $request->param('id');

        $org = Organization::findForUser($orgId, $userId);

        if ($org === null) {
            return Response::notFound('Organization not found');
        }

        if (!Organization::isAdmin($orgId, $userId)) {
            return Response::forbidden('Only admins can add members');
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
            Organization::addMember($orgId, $data['user_id'], $role);

            $this->auditService->log('organization', $orgId, 'add_member', null, [
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
        $orgId = $request->param('id');
        $memberId = $request->param('userId');

        $org = Organization::findForUser($orgId, $currentUserId);

        if ($org === null) {
            return Response::notFound('Organization not found');
        }

        if (!Organization::isAdmin($orgId, $currentUserId)) {
            return Response::forbidden('Only admins can update member roles');
        }

        try {
            $data = $request->validate([
                'role' => 'required|string',
            ]);
        } catch (\InvalidArgumentException $e) {
            $errors = json_decode($e->getMessage(), true);
            return Response::validationError($errors);
        }

        $updated = Organization::updateMemberRole($orgId, $memberId, $data['role']);

        if (!$updated) {
            return Response::notFound('Member not found');
        }

        $this->auditService->log('organization', $orgId, 'update_member', null, [
            'user_id' => $memberId,
            'role' => $data['role'],
        ]);

        return Response::success(null, 'Member role updated successfully');
    }

    public function removeMember(Request $request): Response
    {
        $currentUserId = Session::getUserId();
        $orgId = $request->param('id');
        $memberId = $request->param('userId');

        $org = Organization::find($orgId);

        if ($org === null) {
            return Response::notFound('Organization not found');
        }

        if (!Organization::isAdmin($orgId, $currentUserId)) {
            return Response::forbidden('Only admins can remove members');
        }

        if ($org['owner_id'] === $memberId) {
            return Response::error('Cannot remove the organization owner', 400);
        }

        $removed = Organization::removeMember($orgId, $memberId);

        if (!$removed) {
            return Response::notFound('Member not found');
        }

        $this->auditService->log('organization', $orgId, 'remove_member', [
            'user_id' => $memberId,
        ], null);

        return Response::success(null, 'Member removed successfully');
    }

    public function invites(Request $request): Response
    {
        $userId = Session::getUserId();
        $orgId = $request->param('id');

        $org = Organization::findForUser($orgId, $userId);

        if ($org === null) {
            return Response::notFound('Organization not found');
        }

        if (!Organization::isAdmin($orgId, $userId)) {
            return Response::forbidden('Only admins can view invites');
        }

        $invites = $this->inviteService->getInvitesByOrganization($orgId);

        return Response::success($invites);
    }

    public function createInvite(Request $request): Response
    {
        $userId = Session::getUserId();
        $orgId = $request->param('id');

        $org = Organization::findForUser($orgId, $userId);

        if ($org === null) {
            return Response::notFound('Organization not found');
        }

        if (!Organization::isAdmin($orgId, $userId)) {
            return Response::forbidden('Only admins can create invites');
        }

        try {
            $data = $request->validate([
                'email' => 'nullable|email',
                'expires_in_hours' => 'nullable|integer',
            ]);
        } catch (\InvalidArgumentException $e) {
            $errors = json_decode($e->getMessage(), true);
            return Response::validationError($errors);
        }

        $invite = $this->inviteService->createInvite(
            $orgId,
            $userId,
            $data['email'] ?? null,
            $data['expires_in_hours'] ?? 168
        );

        $this->auditService->log('organization', $orgId, 'create_invite', null, [
            'invite_id' => $invite['id'],
            'email' => $invite['email'],
        ]);

        return Response::created($invite, 'Invite created successfully');
    }

    public function revokeInvite(Request $request): Response
    {
        $userId = Session::getUserId();
        $orgId = $request->param('id');
        $code = $request->param('code');

        $org = Organization::findForUser($orgId, $userId);

        if ($org === null) {
            return Response::notFound('Organization not found');
        }

        if (!Organization::isAdmin($orgId, $userId)) {
            return Response::forbidden('Only admins can revoke invites');
        }

        $revoked = $this->inviteService->revokeInvite($orgId, $code);

        if (!$revoked) {
            return Response::notFound('Invite not found');
        }

        $this->auditService->log('organization', $orgId, 'revoke_invite', [
            'code' => $code,
        ], null);

        return Response::success(null, 'Invite revoked successfully');
    }

    public function acceptInvite(Request $request): Response
    {
        $userId = Session::getUserId();
        $code = $request->param('code');

        if ($userId === null) {
            $invite = $this->inviteService->findByCode($code);
            if ($invite === null) {
                return Response::notFound('Invalid invite code');
            }
            return Response::success([
                'requires_auth' => true,
                'organization_name' => $invite['organization_name'],
            ]);
        }

        try {
            $org = $this->inviteService->acceptInvite($code, $userId);

            $this->auditService->log('organization', $org['id'], 'accept_invite', null, [
                'user_id' => $userId,
            ]);

            return Response::success($org, 'Successfully joined the organization');
        } catch (\InvalidArgumentException $e) {
            return Response::error($e->getMessage(), 400);
        }
    }
}
