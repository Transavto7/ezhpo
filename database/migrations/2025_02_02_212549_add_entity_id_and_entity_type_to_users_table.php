<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEntityIdAndEntityTypeToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('entity_id')->nullable()->after('hash_id');
            $table->string('entity_type')->nullable()->after('entity_id');

            $table->index(['entity_id', 'entity_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['entity_id', 'entity_type']);

            $table->dropColumn('entity_type');
            $table->dropColumn('entity_id');
        });
    }
}
