<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('request_lives', 'preferred_room_id')) {
            Schema::table('request_lives', function (Blueprint $table) {
                $table->unsignedBigInteger('preferred_room_id')->nullable();
                $table->index('preferred_room_id');
            });
        }

        // Best-effort backfill from legacy column room_id if present.
        if (Schema::hasColumn('request_lives', 'room_id')) {
            DB::statement('UPDATE request_lives SET preferred_room_id = room_id WHERE preferred_room_id IS NULL AND room_id IS NOT NULL');
        }
    }

    public function down(): void
    {
        // NOTE: SQLite cannot drop columns easily; keep it as-is.
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            if (Schema::hasColumn('request_lives', 'preferred_room_id')) {
                Schema::table('request_lives', function (Blueprint $table) {
                    $table->dropIndex(['preferred_room_id']);
                    $table->dropColumn('preferred_room_id');
                });
            }
        }
    }
};

