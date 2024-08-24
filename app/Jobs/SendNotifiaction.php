<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SendNotifiaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $chunkSize = 200;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Collection $users,
        protected User $admin,
        protected string $link)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        DB::beginTransaction();

        try {
            // Chunk the users and process them
            User::chunk($this->chunkSize, function ($users) {
                $dataToInsert = [];

                // Prepare data for bulk insertion
                foreach ($users as $user) {
                    $dataToInsert[] = [
                        'column1' => $user->attribute1,
                        'column2' => $user->attribute2,
                        // Add more columns as needed
                    ];
                }

                // Insert data in bulk using Eloquent model
                // YourModel::insert($dataToInsert);
            });

            // Commit the transaction if everything went fine
            DB::commit();

            echo "Bulk insert completed successfully!";
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurred
            DB::rollBack();

            // Handle the exception gracefully
            echo "Error occurred: " . $e->getMessage();
        }


        foreach ($this->users ?? [] as $user) {
            Notification::create([
                'message' => __('site.Someone sent a private message to you.', ['someone' => 'Admin']),
                'link' => $this->link,
                'user_id' => $user->id,
                'model_id' => $this->admin->id,
                'model_type' => User::class,
                'has_email' => 1,
            ]);
        }
    }
}
