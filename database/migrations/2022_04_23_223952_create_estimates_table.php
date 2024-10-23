<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('estimates', function (Blueprint $table) {
        $table->id();
        $table->string('estimate_number');
        $table->string('type_demande');
        $table->date('estimate_date')->nullable();
        $table->date('expiry_date')->nullable();
        $table->string('status')->nullable();
        $table->text('validators')->nullable(); // Changed to text to accommodate larger JSON strings
        $table->integer('validation_orther')->default(0); // Changed to integer and set default to 0
        $table->unsignedBigInteger('user_id');  // User ID column

        // Foreign key constraint
        $table->foreign('user_id')->references('id')->on('users')
              ->onDelete('cascade');  // Automatically delete estimates when the associated user is deleted

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
        Schema::table('estimates', function (Blueprint $table) {
            $table->dropForeign(['user_id']);  // Drop the foreign key constraint
        });

        Schema::dropIfExists('estimates');  // Drop the estimates table
    }
};
