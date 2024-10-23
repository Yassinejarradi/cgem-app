<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcheteurActionsTable extends Migration
{
    public function up()
    {
        Schema::create('acheteur_actions', function (Blueprint $table) {
            $table->id();
            $table->string('estimate_number');
            $table->unsignedBigInteger('acheteur_id');
            $table->string('action');
            $table->timestamps(); // This line will create both 'created_at' and 'updated_at' columns

            // Foreign key constraints
            $table->foreign('acheteur_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('acheteur_actions');
    }
}
