<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('dorm_students')) {
            return;
        }

        // If already only profile (no room_id/live_status/etc), do nothing.
        $hasRoomId = Schema::hasColumn('dorm_students', 'room_id');
        $hasLiveStatus = Schema::hasColumn('dorm_students', 'live_status');
        if (!$hasRoomId && !$hasLiveStatus) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        Schema::rename('dorm_students', 'dorm_students_legacy');

        Schema::create('dorm_students', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('warning_count')->default(0);
            $table->timestamps();
        });

        // Preserve warning_count for existing profiles.
        DB::statement(
            "INSERT INTO dorm_students (user_id, warning_count, created_at, updated_at)
             SELECT user_id, warning_count, created_at, updated_at
             FROM dorm_students_legacy"
        );

        Schema::drop('dorm_students_legacy');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // No safe down (would require restoring legacy residence fields).
    }
};

