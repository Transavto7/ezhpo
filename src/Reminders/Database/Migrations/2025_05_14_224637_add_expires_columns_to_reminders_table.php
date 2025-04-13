<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExpiresColumnsToRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->integer('expires_in_minutes')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->boolean('hidden_from_initiator')->nullable();
            $table->jsonb('users_to_notify')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropColumn([
                'expires_at',
                'expires_in_minutes',
                'hidden_from_initiator',
                'users_to_notify'
            ]);
        });
    }
}
