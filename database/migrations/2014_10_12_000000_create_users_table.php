<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('prenom')->nullable(); // Add 'prenom' column; adjust nullable as needed
            $table->string('user_id')->unique(); // Ensure user_id is unique if it is supposed to be unique
            $table->string('email')->unique();
            $table->date('join_date'); // Use 'date' type for join_date
            $table->string('phone_number')->nullable();
            $table->string('status')->nullable();
            $table->string('role_name')->nullable();
            $table->boolean('admin')->default(false); // Use boolean for admin flag
            $table->string('avatar')->nullable();
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
