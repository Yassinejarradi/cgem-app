<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAcheteurIdToEstimateActionsTable extends Migration
{
    public function up()
    {
        Schema::table('estimate_actions', function (Blueprint $table) {
            $table->unsignedBigInteger('acheteur_id')->nullable()->after('user_id'); // Adding after user_id for better readability

            // Adding foreign key constraint if "users" table is used for acheteurs
            $table->foreign('acheteur_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('estimate_actions', function (Blueprint $table) {
            $table->dropColumn('acheteur_id');
        });
    }
}
