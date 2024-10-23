<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveAcheteurIdFromEstimateActions extends Migration
{
    public function up()
    {
        Schema::table('estimate_actions', function (Blueprint $table) {
            $table->dropForeign(['acheteur_id']); // Drop foreign key if exists
            $table->dropColumn('acheteur_id'); // Drop the column
        });
    }

    public function down()
    {
        Schema::table('estimate_actions', function (Blueprint $table) {
            $table->unsignedBigInteger('acheteur_id')->nullable();
            $table->foreign('acheteur_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
