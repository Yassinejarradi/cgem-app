<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManagedByAndManagedAtToEstimatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estimates', function (Blueprint $table) {
            $table->unsignedBigInteger('managed_by')->nullable()->after('status');
            $table->timestamp('managed_at')->nullable()->after('managed_by');

            // Optionally, if you have a users table, you can add a foreign key constraint
            // $table->foreign('managed_by')->references('id')->on('users')->onDelete('set null');
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
            // Drop the columns
            $table->dropColumn(['managed_by', 'managed_at']);
        });
    }
}
