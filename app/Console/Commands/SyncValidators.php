<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Validator;

class SyncValidators extends Command
{
    protected $signature = 'sync:validators';
    protected $description = 'Imports validators from users table to validators table';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $validators = User::where('role_name', 'validator')->get();
        foreach ($validators as $user) {
            Validator::updateOrCreate(
                ['email' => $user->email],
                ['name' => $user->name, 'user_id' => $user->id] // Assuming you have a 'user_id' field in the validators table to track back to the users table
            );
        }
        $this->info('Validators synced successfully!');
    }
}

