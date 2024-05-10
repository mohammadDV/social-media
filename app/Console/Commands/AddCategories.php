<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

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
            ['نویسندگان', 'Authors'],
            ['داخلی', 'Domestic'],
            ['خارجی', 'Foreign'],
            ['غیرفوتبالی', 'Non-football'],
            ['تحلیل فوتبالی', 'Football analysis'],
            ['تحلیل غیر فوتبالی', 'Non-football analysis'],
            ['روزنامه ها', 'Newspapers'],
        ];

        $user = User::where('level', 3)->firstOrFail();

        foreach ($categories as $category) {
            Category::updateOrCreate([
                'title' => $category[0],
            ], [
                'alias_title' => $category[1],
                'slug' => strtolower(str_replace(' ', '-', $category[1])),
                'menu' => 1,
                'user_id' => $user->id,
                'status' => 1,
            ]);
        }

        if (($open = fopen(public_path('excel/countries.csv'), "r")) !== FALSE) {

            $id = 1;
            while (($data = fgetcsv($open)) !== FALSE) {

                $country = explode(";", $data[0]);
                if (!empty($country[2])) {

                    Category::updateOrCreate([
                        'title' => $country[2],
                    ], [
                        'menu' => 0,
                        'user_id' => $user->id,
                        'status' => 1,
                        'alias_title' => $country[1],
                        'slug' => strtolower(str_replace(' ', '-', $country[1])),
                        'image' => 'https://varzeshtimes.ir/country/' . $id . '.png',
                        'created_at' => Carbon::now()
                    ]);
                    $id++;
                }
            }
            fclose($open);
        }

        if (($open = fopen(public_path('excel/clubs.csv'), "r")) !== FALSE) {

            $id = 1;
            while (($data = fgetcsv($open)) !== FALSE) {

                $country = explode(";", $data[0]);
                if (!empty($country[2])) {
                    Category::updateOrCreate([
                        'title' => $country[2],
                    ], [
                        'menu' => 0,
                        'user_id' => $user->id,
                        'status' => 1,
                        'alias_title' => $country[1],
                        'slug' => strtolower(str_replace(' ', '-', $country[1])),
                        'image' => 'https://varzeshtimes.ir/clubs/' . $id . '.png',
                        'created_at' => Carbon::now()
                    ]);
                    $id++;
                }
            }
            fclose($open);
        }

        $this->info(PHP_EOL.'Done');
        return Command::SUCCESS;
    }
}