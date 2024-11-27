<?php

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CheckFkFromFormEventsToForms extends Migration
{
    private static $TABLE = 'form_events';

    private static $FOREIGN_TABLE = 'forms';

    private static $KEY = 'form_events_form_uuid_foreign';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::$TABLE, function (Blueprint $table) {
            $conn = Schema::getConnection()->getDoctrineSchemaManager();

            $wrongReferencedTable = false;

            /** @var ForeignKeyConstraint $key */
            foreach ($conn->listTableForeignKeys(self::$TABLE) as $key) {
                if ($key->getName() !== self::$KEY) {
                    continue;
                }

                if ($key->getForeignTableName() === self::$FOREIGN_TABLE) {
                    continue;
                }

                $wrongReferencedTable = true;
            }

            if (!$wrongReferencedTable) {
                return;
            }

            DB::table(self::$TABLE)
                ->leftJoin(self::$FOREIGN_TABLE, 'forms.uuid', '=', self::$TABLE . '.form_uuid')
                ->whereNull('forms.uuid')
                ->delete();

            $table->dropForeign(self::$KEY);

            $table->foreign('form_uuid')
                ->references('uuid')
                ->on(self::$FOREIGN_TABLE)
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

    }
}
