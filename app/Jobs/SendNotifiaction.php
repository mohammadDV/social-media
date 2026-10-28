<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotifiaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param  Collection<int, User>  $users
     */
    public function __construct(
        protected Collection $users,
        protected User $admin,
        protected string $link,
        protected string $message = '',
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->users as $user) {
            Notification::create([
                'message' => $this->message !== '' ? $this->message : __('site.Someone sent a private message to you.', ['someone' => 'Admin']),
                'link' => $this->link,
                'user_id' => $user->id,
                'model_id' => $this->admin->id,
                'model_type' => User::class,
                'has_email' => 1,
            ]);
        }
    }
}
