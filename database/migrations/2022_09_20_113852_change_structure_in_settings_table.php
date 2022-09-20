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

        $this->createSetting('logo', $settings->logo);
        $this->createSetting('sms_api_key', $settings->sms_api_key);
        $this->createSetting('sms_text_driver', $settings->sms_text_driver);
        $this->createSetting('sms_text_car', $settings->sms_text_car);
        $this->createSetting('sms_text_phone', $settings->sms_text_phone);
        $this->createSetting('sms_text_default', $settings->sms_text_default);
        $this->createSetting('id_auto', 0);
        $this->createSetting('id_auto_required', 0);
        $settings->delete();

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
