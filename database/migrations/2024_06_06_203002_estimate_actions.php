<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstimateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estimate_actions', function (Blueprint $table) {
            $table->id();
            $table->string('estimate_number');
            $table->unsignedBigInteger('validator_id');
            $table->unsignedBigInteger('user_id');
            $table->string('action');
            $table->timestamp('created_at')->useCurrent();

            // Foreign key constraints
            $table->foreign('validator_id')->references('id')->on('validators')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('estimate_actions');
    }
}

