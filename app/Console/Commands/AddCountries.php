<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\User;
use Illuminate\Console\Command;

class AddCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add countries';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $user = User::where('level', 3)->firstOrFail();

        if (($open = fopen(public_path('excel/countries.csv'), "r")) !== FALSE) {

            $id = 1;
            while (($data = fgetcsv($open)) !== FALSE) {

                $country = explode(";", $data[0]);
                if (!empty($country[2])) {

                    Country::create([
                        'title' => $country[2],
                        'user_id' => $user->id,
                        'alias_title' => $country[1],
                        'image' => 'https://prod-data-sport.s3.eu-central-1.amazonaws.com/country/' . $id . '.png',
                        'status' => 1,
                        'created_at' => '2023-11-25 07:39:56'
                    ]);
                    $id++;
                }
            }
            fclose($open);
        }

        $this->info(PHP_EOL.'Done' . $id);
        return Command::SUCCESS;
    }
}