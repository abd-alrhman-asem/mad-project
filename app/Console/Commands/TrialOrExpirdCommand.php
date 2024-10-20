<?php

namespace App\Console\Commands;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Console\Command;

class TrialOrExpirdCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:trial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change user status from Trial to Expired after a certain period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::where('type', UserStatus::Trial->value)
            ->where('created_at', '>', now()->subDays(7))
            ->get();


        foreach ($users as $user) {
                $user->type = UserStatus::Expired->value;
                $user->save();

        }
    }


}
