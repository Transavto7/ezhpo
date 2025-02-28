<?php

use App\Enums\TripTicket\TripTicketType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTypeToTripTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trip_tickets', function (Blueprint $table) {
            $table->string('type')->default(TripTicketType::COMMON);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trip_tickets', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
