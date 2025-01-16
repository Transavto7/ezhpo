<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStampIdToReqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reqs', function (Blueprint $table) {
            $table->unsignedBigInteger('stamp_id')->nullable()->after('hash_id');
            $table->foreign('stamp_id')->references('id')->on('stamps');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reqs', function (Blueprint $table) {
            $table->dropForeign(['stamp_id']);
            $table->dropColumn('stamp_id');
        });
    }
}
