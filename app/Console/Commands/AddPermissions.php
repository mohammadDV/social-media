<?php

namespace App\Console\Commands;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class AddPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userPerm = [
            'status_show',
            'status_store',
            'status_update',
            'status_delete',
            'ticket_show',
            'ticket_store',
        ];
        $authorPerm = [
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
        ];
        $operatorPerm = [
            'ticket_show',
            'ticket_replay',
            'subject_store',
            'subject_show',
            'subject_update',
            'subject_delete',
        ];
        $permissions = [
            'post_show',
            'post_store',
            'post_update',
            'post_delete',
            'post_confirm',
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
            'category_show',
            'category_store',
            'category_update',
            'category_delete',
            'league_show',
            'league_store',
            'league_update',
            'league_delete',
            'club_show',
            'club_store',
            'club_update',
            'club_delete',
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
        ];

        // Add the lite and normal roles
        $admin = Role::updateOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $author = Role::updateOrCreate(['name' => 'author', 'guard_name' => 'web']);
        $operator = Role::updateOrCreate(['name' => 'operator', 'guard_name' => 'web']);
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Loop through the array and create permissions
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        if ($admin) {
            $admin->syncPermissions($permissions);
            $user->syncPermissions($userPerm);
            $author->syncPermissions($authorPerm);
            $operator->syncPermissions($operatorPerm);
        }

        User::find(1)->assignRole(['admin']);

        // $this->assignRoleToUser();

        $this->info(PHP_EOL.'Done');
        return Command::SUCCESS;
    }
}
