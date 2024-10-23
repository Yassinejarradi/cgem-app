<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\Validator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CopyValidatorUser
{
    /**
     * Handle the event.
     *
     * @param  UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $user = $event->user;

        if ($user->role_name === 'Validateur') {
            Validator::create([
                'name' => $user->name,
                'prenom' => $user->prenom,
                'user_id' => $user->id, // Ensure the correct user ID is used
                'email' => $user->email,
                'join_date' => $user->join_date,
                'phone_number' => $user->phone_number,
                'status' => $user->status,
                'role_name' => $user->role_name,
                'admin' => $user->admin,
                'avatar' => $user->avatar,
                'position' => $user->position,
                'department' => $user->department,
                'email_verified_at' => $user->email_verified_at,
                'password' => $user->password,
                'remember_token' => $user->remember_token,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
