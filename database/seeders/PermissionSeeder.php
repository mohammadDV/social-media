<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $userPerm = [
            'status_show',
            'status_store',
            'status_update',
            'status_delete',
            'ticket_show',
            'ticket_store',
            'chat_store',
            'chat_show',
            'chat_delete',
            'report_store',
            'user_update',
        ];

        $authorPerm = [
            'category_show',
            'status_show',
            'status_store',
            'status_update',
            'status_delete',
            'post_show',
            'post_store',
            'post_update',
            'post_delete',
            'ticket_show',
            'ticket_store',
            'chat_store',
            'chat_show',
            'chat_delete',
            'report_store',
            'user_update',
        ];

        $operatorPerm = [
            'ticket_show',
            'ticket_replay',
            'subject_store',
            'subject_show',
            'subject_update',
            'subject_delete',
            'video_store',
            'video_show',
            'video_update',
            'video_delete',
            'report_store',
            'report_show',
            'report_update',
            'report_delete',
            'user_show',
            'user_store',
            'user_update',
            'user_delete',
            'user_confirm',
            'role_show',
        ];

        $permissions = [
            'category_show',
            'category_store',
            'category_update',
            'category_delete',
            'post_show',
            'post_store',
            'post_update',
            'post_delete',
            'post_confirm',
            'status_index',
            'status_show',
            'status_store',
            'status_update',
            'status_delete',
            'status_confirm',
            'comment_show',
            'comment_store',
            'comment_update',
            'comment_delete',
            'comment_confirm',
            'advertise_show',
            'advertise_store',
            'advertise_update',
            'advertise_delete',
            'league_show',
            'league_store',
            'league_update',
            'league_delete',
            'club_show',
            'club_store',
            'club_update',
            'club_delete',
            'player_show',
            'player_store',
            'player_update',
            'player_delete',
            'match_show',
            'match_store',
            'match_update',
            'match_delete',
            'step_show',
            'step_store',
            'step_update',
            'step_delete',
            'page_show',
            'page_store',
            'page_update',
            'page_delete',
            'user_show',
            'user_store',
            'user_update',
            'user_delete',
            'user_confirm',
            'sport_show',
            'sport_store',
            'sport_update',
            'sport_delete',
            'country_show',
            'country_store',
            'country_update',
            'country_delete',
            'permission_show',
            'permission_store',
            'permission_update',
            'permission_delete',
            'role_show',
            'role_store',
            'role_update',
            'role_delete',
            'live_show',
            'live_store',
            'live_update',
            'live_delete',
            'notification_show',
            'notification_store',
            'notification_update',
            'notification_delete',
            'notification_send',
            'like_show',
            'like_store',
            'follow_show',
            'follow_store',
            'ticket_show',
            'ticket_store',
            'ticket_replay',
            'subject_store',
            'subject_show',
            'subject_update',
            'subject_delete',
            'video_store',
            'video_show',
            'video_update',
            'video_delete',
            'chat_store',
            'chat_show',
            'chat_delete',
            'report_store',
            'report_show',
            'report_update',
            'report_delete',
        ];

        $admin = Role::updateOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $author = Role::updateOrCreate(['name' => 'author', 'guard_name' => 'web']);
        $operator = Role::updateOrCreate(['name' => 'operator', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin->syncPermissions($permissions);
        $userRole->syncPermissions($userPerm);
        $author->syncPermissions($authorPerm);
        $operator->syncPermissions($operatorPerm);

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'first_name' => 'admin',
                'last_name' => 'admin',
                'nickname' => 'admin',
                'password' => bcrypt('password'),
                'level' => 3,
                'status' => 1,
                'role_id' => 1,
                'type' => 1,
            ]
        );

        $adminUser->syncRoles(['admin']);
    }
}
