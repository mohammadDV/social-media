<?php

namespace Tests\Feature\Api\Profile;

use App\Models\Report;
use App\Models\Status;
use App\Models\User;

class ReportEndpointsTest extends ProfileTestCase
{
    public function test_report_index_requires_auth(): void
    {
        $this->getJson('/api/profile/reports')->assertUnauthorized();
    }

    public function test_report_index_forbidden_without_permission(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/profile/reports')->assertForbidden();
    }

    public function test_report_index_returns_paginated_list(): void
    {
        $admin = $this->actingAsAdmin();
        $statusOwner = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $status = Status::factory()->create(['user_id' => $statusOwner->id]);

        Report::create([
            'model_id' => $status->id,
            'model_type' => Status::class,
            'message' => 'This content violates rules',
            'user_id' => $admin->id,
            'status' => Report::STATUS_PENDING,
            'count' => 1,
        ]);

        $this->getJson('/api/profile/reports')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.message', 'This content violates rules');
    }

    public function test_report_show_requires_auth(): void
    {
        $status = Status::factory()->create(['user_id' => 1]);
        $report = Report::create([
            'model_id' => $status->id,
            'model_type' => Status::class,
            'message' => 'Report message long enough',
            'user_id' => 1,
            'status' => Report::STATUS_PENDING,
            'count' => 1,
        ]);

        $this->getJson('/api/profile/reports/'.$report->id)->assertUnauthorized();
    }

    public function test_report_show_forbidden_without_permission(): void
    {
        $admin = $this->actingAsAdmin();
        $status = Status::factory()->create(['user_id' => $admin->id]);
        $report = Report::create([
            'model_id' => $status->id,
            'model_type' => Status::class,
            'message' => 'Report message long enough',
            'user_id' => $admin->id,
            'status' => Report::STATUS_PENDING,
            'count' => 1,
        ]);

        $this->actingAsUser();

        $this->getJson('/api/profile/reports/'.$report->id)->assertForbidden();
    }

    public function test_report_show_returns_report(): void
    {
        $admin = $this->actingAsAdmin();
        $status = Status::factory()->create(['user_id' => $admin->id]);
        $report = Report::create([
            'model_id' => $status->id,
            'model_type' => Status::class,
            'message' => 'Detailed report message',
            'user_id' => $admin->id,
            'status' => Report::STATUS_PENDING,
            'count' => 1,
        ]);

        $this->getJson('/api/profile/reports/'.$report->id)
            ->assertOk()
            ->assertJsonPath('id', $report->id)
            ->assertJsonPath('message', 'Detailed report message');
    }

    public function test_report_store_requires_auth(): void
    {
        $this->postJson('/api/profile/reports', [])->assertUnauthorized();
    }

    public function test_report_store_forbidden_without_permission(): void
    {
        $this->actingAsUserWithoutPermissions();

        $this->postJson('/api/profile/reports', [
            'id' => 1,
            'type' => 'status',
            'message' => 'This is a report message',
        ])->assertForbidden();
    }

    public function test_report_store_validation_fails(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/profile/reports', [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_report_store_creates_record(): void
    {
        $user = $this->actingAsUser();
        $statusOwner = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $status = Status::factory()->create(['user_id' => $statusOwner->id]);

        $this->postJson('/api/profile/reports', [
            'id' => $status->id,
            'type' => 'status',
            'message' => 'Spam content reported here',
        ])
            ->assertCreated()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('reports', [
            'model_id' => $status->id,
            'model_type' => Status::class,
            'message' => 'Spam content reported here',
            'user_id' => $user->id,
            'status' => Report::STATUS_PENDING,
        ]);
    }

    public function test_report_close_requires_auth(): void
    {
        $status = Status::factory()->create(['user_id' => 1]);
        $report = Report::create([
            'model_id' => $status->id,
            'model_type' => Status::class,
            'message' => 'Close me please now',
            'user_id' => 1,
            'status' => Report::STATUS_PENDING,
            'count' => 1,
        ]);

        $this->postJson('/api/profile/reports/'.$report->id, ['is_delete' => 1])->assertUnauthorized();
    }

    public function test_report_close_forbidden_without_permission(): void
    {
        $admin = $this->actingAsAdmin();
        $status = Status::factory()->create(['user_id' => $admin->id]);
        $report = Report::create([
            'model_id' => $status->id,
            'model_type' => Status::class,
            'message' => 'Close me please now',
            'user_id' => $admin->id,
            'status' => Report::STATUS_PENDING,
            'count' => 1,
        ]);

        $this->actingAsUser();

        $this->postJson('/api/profile/reports/'.$report->id, ['is_delete' => 1])->assertForbidden();
    }

    public function test_report_close_validation_fails(): void
    {
        $admin = $this->actingAsAdmin();
        $status = Status::factory()->create(['user_id' => $admin->id]);
        $report = Report::create([
            'model_id' => $status->id,
            'model_type' => Status::class,
            'message' => 'Close me please now',
            'user_id' => $admin->id,
            'status' => Report::STATUS_PENDING,
            'count' => 1,
        ]);

        $this->postJson('/api/profile/reports/'.$report->id, [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_report_close_closes_and_flags_model(): void
    {
        $admin = $this->actingAsAdmin();
        $statusOwner = User::factory()->create(['status' => 1, 'role_id' => 4]);
        $status = Status::factory()->create([
            'user_id' => $statusOwner->id,
            'is_report' => false,
        ]);
        $report = Report::create([
            'model_id' => $status->id,
            'model_type' => Status::class,
            'message' => 'Close me please now',
            'user_id' => $admin->id,
            'status' => Report::STATUS_PENDING,
            'count' => 1,
        ]);

        $this->postJson('/api/profile/reports/'.$report->id, ['is_delete' => 1])
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => Report::STATUS_CLOSED,
            'is_delete' => 1,
            'operator_id' => $admin->id,
        ]);

        $this->assertDatabaseHas('statuses', [
            'id' => $status->id,
            'is_report' => 1,
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $statusOwner->id,
            'model_id' => $status->id,
            'model_type' => Status::class,
        ]);
    }

    public function test_report_destroy_requires_auth(): void
    {
        $status = Status::factory()->create(['user_id' => 1]);
        $report = Report::create([
            'model_id' => $status->id,
            'model_type' => Status::class,
            'message' => 'Delete this report msg',
            'user_id' => 1,
            'status' => Report::STATUS_PENDING,
            'count' => 1,
        ]);

        $this->deleteJson('/api/profile/reports/'.$report->id)->assertUnauthorized();
    }

    public function test_report_destroy_forbidden_without_permission(): void
    {
        $admin = $this->actingAsAdmin();
        $status = Status::factory()->create(['user_id' => $admin->id]);
        $report = Report::create([
            'model_id' => $status->id,
            'model_type' => Status::class,
            'message' => 'Delete this report msg',
            'user_id' => $admin->id,
            'status' => Report::STATUS_PENDING,
            'count' => 1,
        ]);

        $this->actingAsUser();

        $this->deleteJson('/api/profile/reports/'.$report->id)->assertForbidden();
    }

    public function test_report_destroy_deletes_record(): void
    {
        $admin = $this->actingAsAdmin();
        $status = Status::factory()->create(['user_id' => $admin->id]);
        $report = Report::create([
            'model_id' => $status->id,
            'model_type' => Status::class,
            'message' => 'Delete this report msg',
            'user_id' => $admin->id,
            'status' => Report::STATUS_PENDING,
            'count' => 1,
        ]);

        $this->deleteJson('/api/profile/reports/'.$report->id)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseMissing('reports', ['id' => $report->id]);
    }
}
