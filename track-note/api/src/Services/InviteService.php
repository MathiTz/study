<?php

declare(strict_types=1);

namespace TrackNote\Services;

use TrackNote\Core\Database;
use TrackNote\Models\Organization;
use TrackNote\Models\User;

class InviteService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function createInvite(
        string $orgId,
        string $invitedBy,
        ?string $email = null,
        int $expiresInHours = 168 // 7 days
    ): array {
        $code = $this->generateCode();
        $expiresAt = date('c', strtotime("+{$expiresInHours} hours"));

        $inviteId = $this->db->insert('invites', [
            'organization_id' => $orgId,
            'email' => $email ? strtolower($email) : null,
            'code' => $code,
            'invited_by' => $invitedBy,
            'expires_at' => $expiresAt,
        ]);

        return $this->db->fetch('SELECT * FROM invites WHERE id = :id', ['id' => $inviteId]);
    }

    public function getInvitesByOrganization(string $orgId): array
    {
        return $this->db->fetchAll(
            'SELECT i.*, u.name as invited_by_name
             FROM invites i
             INNER JOIN users u ON i.invited_by = u.id
             WHERE i.organization_id = :org_id AND i.used_at IS NULL AND i.expires_at > NOW()
             ORDER BY i.created_at DESC',
            ['org_id' => $orgId]
        );
    }

    public function findByCode(string $code): ?array
    {
        return $this->db->fetch(
            'SELECT i.*, o.name as organization_name, o.slug as organization_slug
             FROM invites i
             INNER JOIN organizations o ON i.organization_id = o.id
             WHERE i.code = :code',
            ['code' => $code]
        );
    }

    public function acceptInvite(string $code, string $userId): array
    {
        $invite = $this->findByCode($code);

        if ($invite === null) {
            throw new \InvalidArgumentException('Invalid invite code');
        }

        if ($invite['used_at'] !== null) {
            throw new \InvalidArgumentException('This invite has already been used');
        }

        if (strtotime($invite['expires_at']) < time()) {
            throw new \InvalidArgumentException('This invite has expired');
        }

        if ($invite['email'] !== null) {
            $user = User::find($userId);
            if (strtolower($user['email']) !== strtolower($invite['email'])) {
                throw new \InvalidArgumentException('This invite was sent to a different email address');
            }
        }

        if (Organization::isMember($invite['organization_id'], $userId)) {
            throw new \InvalidArgumentException('You are already a member of this organization');
        }

        return $this->db->transaction(function (Database $db) use ($invite, $userId) {
            Organization::addMember($invite['organization_id'], $userId);

            $db->update(
                'invites',
                ['used_at' => date('c')],
                'id = :id',
                ['id' => $invite['id']]
            );

            return Organization::findForUser($invite['organization_id'], $userId);
        });
    }

    public function revokeInvite(string $orgId, string $code): bool
    {
        $affected = $this->db->delete(
            'invites',
            'organization_id = :org_id AND code = :code AND used_at IS NULL',
            ['org_id' => $orgId, 'code' => $code]
        );
        return $affected > 0;
    }

    private function generateCode(): string
    {
        return bin2hex(random_bytes(16));
    }
}
