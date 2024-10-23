<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Validator;

class ImportExistingValidators extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:validators';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import existing users with role_name validator into validators table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get all users with role_name 'validator'
        $users = User::where('role_name', 'Validateur')->get();

        foreach ($users as $user) {
            // Check if the validator already exists in the validators table
            $exists = Validator::where('email', $user->email)->exists();

            if (!$exists) {
                // Create a new entry in the validators table
                Validator::create([
                    'name' => $user->name,
                    'prenom' => $user->prenom,
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'join_date' => $user->join_date,
                    'phone_number' => $user->phone_number,
                    'status' => $user->status,
                    'role_name' => $user->role_name,
                    'admin' => $user->admin,
                    'gestionnaire' => $user->gestionnaire,
                    'avatar' => $user->avatar,
                    'position' => $user->position,
                    'department' => $user->department,
                    'email_verified_at' => $user->email_verified_at,
                    'password' => $user->password,
                    'remember_token' => $user->remember_token,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]);
            }
        }

        $this->info('Existing validators have been imported successfully.');

        return 0;
    }
}

