<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCampaignIdToGoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->integer('campaign_goal_id')->index()->after('id');
            $table->dropColumn('event_id');
            $table->dropColumn('campaign_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->integer('event_id')->index()->after('id');
            $table->integer('campaign_id')->index()->after('event_id');
            $table->dropColumn('campaign_goal_id');
        });
    }
}
