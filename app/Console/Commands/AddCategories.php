<?php

namespace App\Console\Commands;

use App\Models\Category;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class AddCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add categories';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $categories = [
            'نویسندگان',
            'داخلی',
            'خارجی',
            'غیرفوتبالی',
            'تحلیل فوتبالی',
            'تحلیل غیر فوتبالی',
            'روزنامه ها',
        ];

        $user = User::where('level', 3)->firstOrFail();

        foreach ($categories as $category) {
            Category::updateOrCreate([
                'title' => $category,
            ], [
                'user_id' => $user->id,
                'status' => 1,
            ]);
        }

        $this->info(PHP_EOL.'Done');
        return Command::SUCCESS;
    }
}
