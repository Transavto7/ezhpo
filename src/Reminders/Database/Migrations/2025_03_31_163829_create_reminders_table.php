<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Src\Reminders\Enums\ReminderType;

class CreateRemindersTable extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('title');
            $table->text('content');
            $table->string('status');
            $table->string('type')->default(ReminderType::INFO);
            $table->string('action');
            $table->jsonb('context')->nullable(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
}
