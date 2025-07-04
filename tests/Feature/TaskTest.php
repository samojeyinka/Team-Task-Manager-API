<?php
// tests/Feature/TaskTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_task()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/tasks', [
                'title' => 'Test Task',
                'description' => 'Test Description',
                'assigned_to' => $member->id,
                'status' => 'pending',
                'due_date' => '2024-12-31',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', ['title' => 'Test Task']);
    }

    public function test_member_cannot_create_task()
    {
        $member = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($member, 'sanctum')
            ->postJson('/api/tasks', [
                'title' => 'Test Task',
                'description' => 'Test Description',
                'assigned_to' => $member->id,
                'status' => 'pending',
                'due_date' => '2024-12-31',
            ]);

        $response->assertStatus(403);
    }

    public function test_member_can_update_own_task_status()
    {
        $member = User::factory()->create(['role' => 'member']);
        $task = Task::factory()->create(['assigned_to' => $member->id]);

        $response = $this->actingAs($member, 'sanctum')
            ->patchJson("/api/tasks/{$task->id}/status", [
                'status' => 'completed',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('completed', $task->fresh()->status);
    }
}