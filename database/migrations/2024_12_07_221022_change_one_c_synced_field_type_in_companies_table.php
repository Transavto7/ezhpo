<?php

use App\Company;
use App\Enums\OneCSyncStatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOneCSyncedFieldTypeInCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->integer('one_c_synced_tmp')->default(OneCSyncStatusEnum::NON_CREATED);
        });

        Company::withTrashed()
            ->where('one_c_synced', true)
            ->update([
                'one_c_synced_tmp' => OneCSyncStatusEnum::NEED_UPDATE
            ]);

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('one_c_synced');
            $table->renameColumn('one_c_synced_tmp', 'one_c_synced');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('one_c_synced_tmp')->default(false);
        });

        Company::withTrashed()
            ->where('one_c_synced', OneCSyncStatusEnum::SYNCED)
            ->update([
                'one_c_synced_tmp' => true
            ]);

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('one_c_synced');
            $table->renameColumn('one_c_synced_tmp', 'one_c_synced');
        });
    }
}
