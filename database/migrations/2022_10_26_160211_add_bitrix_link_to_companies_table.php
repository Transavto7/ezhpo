<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Symfony\Component\Console\Output\ConsoleOutput;

class AddBitrixLinkToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->text('bitrix_link')->after('deleted_at')->nullable();
        });

        \App\FieldPrompt::create([
            'type' => 'company',
            'field' => 'bitrix_link',
            'name' => 'Ссылка на компанию в Bitrix24'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('bitrix_link');
        });

        \App\FieldPrompt::where('type', 'company')->where('field', 'bitrix_link')->forceDelete();
    }
}
