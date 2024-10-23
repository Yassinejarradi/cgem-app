<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstimateDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('estimate_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estimate_id');
            $table->string('detail_type');
            $table->string('detail_value');
            $table->foreign('estimate_id')->references('id')->on('estimates')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('estimate_details');
    }
}
