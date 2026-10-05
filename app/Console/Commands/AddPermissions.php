<?php

namespace App\Console\Commands;

use Database\Seeders\PermissionSeeder;
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
    protected $description = 'Seed roles and permissions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->call('db:seed', ['--class' => PermissionSeeder::class]);

        $this->info(PHP_EOL.'Done');

        return Command::SUCCESS;
    }
}
