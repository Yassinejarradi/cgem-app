<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsToArticlesTable extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->decimal('price', 8, 2)->nullable()->after('description');
            $table->integer('stock')->nullable()->after('price');
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('price');
            $table->dropColumn('stock');
        });
    }
}

