<?php

declare(strict_types=1);

namespace TrackNote\Tests\Integration;

use TrackNote\Tests\TestCase;
use TrackNote\Models\Project;

class ProjectCreationTest extends TestCase
{
    private string $testUserId;
    private string $testOrgId;
    private array $createdProjectIds = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->testUserId = $this->db->insert("users", [
            "email" => "test-" . uniqid() . "@example.com",
            "name" => "Test User",
            "password_hash" => password_hash("password", PASSWORD_ARGON2ID),
        ]);

        $this->testOrgId = $this->db->insert("organizations", [
            "name" => "Test Organization " . uniqid(),
            "slug" => "test-org-" . uniqid(),
            "owner_id" => $this->testUserId,
        ]);

        $this->db->insert("organization_members", [
            "organization_id" => $this->testOrgId,
            "user_id" => $this->testUserId,
            "role" => "admin",
        ]);
    }

    protected function tearDown(): void
    {
        foreach ($this->createdProjectIds as $projectId) {
            $this->db->delete("project_members", "project_id = :project_id", [
                "project_id" => $projectId,
            ]);
            $this->db->delete("projects", "id = :id", ["id" => $projectId]);
        }

        $this->db->delete("organization_members", "user_id = :user_id", [
            "user_id" => $this->testUserId,
        ]);
        $this->db->delete("organizations", "owner_id = :owner_id", [
            "owner_id" => $this->testUserId,
        ]);
        $this->db->delete("users", "id = :id", ["id" => $this->testUserId]);

        parent::tearDown();
    }

    private function createProject(array $data): string
    {
        $projectId = Project::create($data);
        $this->createdProjectIds[] = $projectId;
        return $projectId;
    }

    public function testCreateProjectWithoutOrganization(): void
    {
        $projectId = $this->createProject([
            "owner_id" => $this->testUserId,
            "name" => "Personal Project",
            "description" => "A personal project without organization",
            "color" => "#6366f1",
        ]);

        $project = Project::find($projectId);

        $this->assertNotNull($project);
        $this->assertEquals("Personal Project", $project["name"]);
        $this->assertEquals(
            "A personal project without organization",
            $project["description"],
        );
        $this->assertEquals("#6366f1", $project["color"]);
        $this->assertNull($project["organization_id"]);
        $this->assertEquals($this->testUserId, $project["owner_id"]);
    }

    public function testCreateProjectWithOrganization(): void
    {
        $projectId = $this->createProject([
            "owner_id" => $this->testUserId,
            "name" => "Organization Project",
            "description" => "A project linked to an organization",
            "color" => "#22c55e",
            "organization_id" => $this->testOrgId,
        ]);

        $project = Project::find($projectId);

        $this->assertNotNull($project);
        $this->assertEquals("Organization Project", $project["name"]);
        $this->assertEquals(
            "A project linked to an organization",
            $project["description"],
        );
        $this->assertEquals("#22c55e", $project["color"]);
        $this->assertEquals($this->testOrgId, $project["organization_id"]);
        $this->assertEquals($this->testUserId, $project["owner_id"]);
    }

    public function testCreateProjectWithMinimalData(): void
    {
        $projectId = $this->createProject([
            "owner_id" => $this->testUserId,
            "name" => "Minimal Project",
        ]);

        $project = Project::find($projectId);

        $this->assertNotNull($project);
        $this->assertEquals("Minimal Project", $project["name"]);
        $this->assertNull($project["organization_id"]);
        $this->assertEquals("#6366f1", $project["color"]); // default color from DB
    }

    public function testCreateMultipleProjectsInSameOrganization(): void
    {
        $project1Id = $this->createProject([
            "owner_id" => $this->testUserId,
            "name" => "Project One",
            "organization_id" => $this->testOrgId,
        ]);

        $project2Id = $this->createProject([
            "owner_id" => $this->testUserId,
            "name" => "Project Two",
            "organization_id" => $this->testOrgId,
        ]);

        $project1 = Project::find($project1Id);
        $project2 = Project::find($project2Id);

        $this->assertEquals($this->testOrgId, $project1["organization_id"]);
        $this->assertEquals($this->testOrgId, $project2["organization_id"]);
        $this->assertNotEquals($project1Id, $project2Id);
    }

    public function testGetAllForUserIncludesOrganizationProjects(): void
    {
        $this->createProject([
            "owner_id" => $this->testUserId,
            "name" => "Personal Project",
        ]);

        $this->createProject([
            "owner_id" => $this->testUserId,
            "name" => "Org Project",
            "organization_id" => $this->testOrgId,
        ]);

        $projects = Project::getAllForUser($this->testUserId);

        $this->assertGreaterThanOrEqual(2, count($projects));

        $projectNames = array_column($projects, "name");
        $this->assertContains("Personal Project", $projectNames);
        $this->assertContains("Org Project", $projectNames);
    }

    public function testProjectBelongsToCorrectOrganization(): void
    {
        $anotherOrgId = $this->db->insert("organizations", [
            "name" => "Another Organization",
            "slug" => "another-org-" . uniqid(),
            "owner_id" => $this->testUserId,
        ]);

        $project1Id = $this->createProject([
            "owner_id" => $this->testUserId,
            "name" => "Project in Org 1",
            "organization_id" => $this->testOrgId,
        ]);

        $project2Id = $this->createProject([
            "owner_id" => $this->testUserId,
            "name" => "Project in Org 2",
            "organization_id" => $anotherOrgId,
        ]);

        $project1 = Project::find($project1Id);
        $project2 = Project::find($project2Id);

        $this->assertEquals($this->testOrgId, $project1["organization_id"]);
        $this->assertEquals($anotherOrgId, $project2["organization_id"]);
        $this->assertNotEquals(
            $project1["organization_id"],
            $project2["organization_id"],
        );

        // Cleanup the extra org
        $this->db->delete("organizations", "id = :id", ["id" => $anotherOrgId]);
    }

    public function testFindForUserReturnsProjectWithOrganization(): void
    {
        $projectId = $this->createProject([
            "owner_id" => $this->testUserId,
            "name" => "Org Project for FindForUser",
            "organization_id" => $this->testOrgId,
        ]);

        $project = Project::findForUser($projectId, $this->testUserId);

        $this->assertNotNull($project);
        $this->assertEquals("Org Project for FindForUser", $project["name"]);
        $this->assertEquals($this->testOrgId, $project["organization_id"]);
    }
}
