<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('settlements')) {
            return;
        }

        // If already in the new format, do nothing.
        if (Schema::hasColumn('settlements', 'user_id')) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        // Rebuild the table (safe for SQLite; table is expected to be empty in dev,
        // but this also preserves existing rows if any).
        Schema::rename('settlements', 'settlements_legacy');

        Schema::create('settlements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('room_id')
                ->constrained('rooms')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('start_at');
            $table->date('end_at')->nullable();

            $table->string('status'); // active | finished | cancelled
            $table->string('source'); // request_live | admin_manual | relocation
            $table->string('end_reason')->nullable(); // graduation | eviction | relocation | personal

            $table->timestamps();

            $table->index(['room_id', 'end_at']);
            $table->index(['user_id', 'end_at']);
        });

        // No legacy data copy: in this project legacy settlements were just id/timestamps.

        // Ensure: only one active settlement per user (SQLite supports partial unique indexes).
        DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS settlements_one_active_per_user ON settlements(user_id) WHERE end_at IS NULL;");

        Schema::drop('settlements_legacy');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // No safe down (would require rebuilding legacy shape).
    }
};
