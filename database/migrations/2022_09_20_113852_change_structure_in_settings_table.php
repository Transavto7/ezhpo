<?php

use App\Settings;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

class ChangeStructureInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $settings = Settings::query()->first();

        Schema::table('settings', function (Blueprint $table) {
            $table->string('key')->nullable()->unique();
            $table->text('value')->nullable();
        });

        $this->createSetting('logo', null);
        $this->createSetting('sms_api_key', null);
        $this->createSetting('sms_text_driver', null);
        $this->createSetting('sms_text_car', null);
        $this->createSetting('sms_text_phone', null);
        $this->createSetting('sms_text_default', null);
        $this->createSetting('id_auto', 0);
        $this->createSetting('id_auto_required', 0);

        if($settings){
            $settings->delete();
        }

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('logo');
            $table->dropColumn('sms_api_key');
            $table->dropColumn('sms_text_driver');
            $table->dropColumn('sms_text_car');
            $table->dropColumn('sms_text_phone');
            $table->dropColumn('sms_text_default');
            $table->dropColumn('deleted_id');
            $table->dropColumn('deleted_at');
        });

        Schema::drop('system_settings');
        Permission::whereIn('name', ['system_read', 'system_delete', 'system_update', 'system_trash'])->delete();
    }

    public function createSetting($key, $value) {
        Settings::create([
            'key' => $key,
            'value' => $value,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
}
