<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertTestData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workdays', function (Blueprint $table) {
            DB::statement("INSERT INTO workdays (id, uuid, date, employee_id, terminal_id, t_people, t_people_test_status,
                                pressure_systolic, pressure_diastolic, pressure_test_status, type_anketa, pulse,
                                pulse_test_status, alcometer_result, alcometer_mode, alcometer_test_status,
                                narko_test_status, photo, video, admitted, is_real, created_at, updated_at, flag_pak)
VALUES (14, 'e4a0b678-fc6b-4161-a062-a5ae77b0c433', '2025-03-12 05:38:05', 3, 3, 36.6, 1, 134, 72, 1, 1, 68, 1, null,
        null, 1, null, null, null, 1, 0, '2025-03-06 17:47:26', '2025-03-06 17:47:26', 'СДПО А'),
    (15, '659de27c-54b6-4f90-a141-b6da469bdecd', '2025-03-12 05:38:05', 3, 3, 36.6, 1, 134, 72, 1, 2, 68, 1, null,
        null, 1, null, null, null, 1, 0, '2025-03-06 17:47:29', '2025-03-06 17:47:29', 'СДПО А'),
    (16, 'bbdff07e-3ab2-41e7-b4d5-ef8d3e83eb3a', '2025-03-06 08:38:05', 3, 3, 36.6, 1, 134, 72, 1, 1, 60, 1, null,
        null, null, 1, null, null, 1, 1, '2025-03-06 17:50:23', '2025-03-06 17:50:23', 'СДПО А'),
    (17, 'e5d089e1-193b-4bdd-a50b-548a8966472e', '2025-03-06 12:38:05', 3, 3, 36.6, 1, 134, 72, 1, 2, 60, 1, null,
        null, null, 1, null, null, 1, 1, '2025-03-06 17:50:33', '2025-03-06 17:50:33', 'СДПО А'),
    (18, '252e4693-352b-4083-a86d-50a84d41e8c3', '2025-03-07 12:38:05', 3, 3, 36.6, 1, 134, 72, 1, 1, 60, 1, 0.5, 1,
        0, 1, null, null, 0, 0, '2025-03-06 17:51:01', '2025-03-06 17:51:01', 'СДПО А'),
    (19, '205c5a50-c6ef-4ace-8ab0-e1fa4e2d1294', '2025-03-08 07:38:05', 3, 3, 36.6, 1, 134, 72, 1, 1, 60, 1, null,
        null, null, 1, null, null, 1, 0, '2025-03-06 17:51:34', '2025-03-06 17:51:34', 'СДПО А'),
    (20, 'df7af0df-9544-48b4-86a4-7c18d8bf94bf', '2025-03-08 15:38:05', 3, 3, 36.6, 1, 134, 72, 1, 2, 60, 1, null,
        null, null, 1, null, null, 1, 0, '2025-03-06 17:51:43', '2025-03-06 17:51:43', 'СДПО А');
");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workdays', function (Blueprint $table) {
            DB::statement("TRUNCATE TABLE workdays");
        });
    }
}
