<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFkInMedicFormNormalizedPressuresTable extends Migration
{
    private static $TABLE = 'medic_form_normalized_pressures';

    private static $KEY = 'medic_form_normalized_pressures_form_id_foreign';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::$TABLE, function (Blueprint $table) {
            $foreignKeys = $this->listTableForeignKeys(self::$TABLE);
            if (in_array(self::$KEY, $foreignKeys)) {
                $table->dropForeign(self::$KEY);
            }

            DB::table(self::$TABLE)
                ->leftJoin('forms', 'forms.id', '=', self::$TABLE . '.form_id')
                ->whereNull('forms.id')
                ->delete();

            $table->foreign('form_id')
                ->references('id')
                ->on('forms')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(self::$TABLE, function (Blueprint $table) {
            $foreignKeys = $this->listTableForeignKeys(self::$TABLE);
            if (in_array(self::$KEY, $foreignKeys)) {
                $table->dropForeign(self::$KEY);
            }

            DB::table(self::$TABLE)
                ->leftJoin('anketas', 'anketas.id', '=', self::$TABLE . '.form_id')
                ->whereNull('anketas.id')
                ->delete();

            $table->foreign('form_id')
                ->references('id')
                ->on('anketas')
                ->onDelete('cascade');
        });
    }

    private function listTableForeignKeys($table)
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();

        return array_map(function($key) {
            return $key->getName();
        }, $conn->listTableForeignKeys($table));
    }
}
