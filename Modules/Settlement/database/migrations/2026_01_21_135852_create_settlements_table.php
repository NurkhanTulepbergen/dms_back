<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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

        // Ensure: only one active settlement per user (best-effort, DB-dependent).
        // - SQLite/Postgres support partial unique indexes.
        // - MySQL does not support partial unique indexes; application-level checks still apply.
        $driver = Schema::getConnection()->getDriverName();
        if (in_array($driver, ['sqlite', 'pgsql'], true)) {
            $sql = match ($driver) {
                'sqlite' => "CREATE UNIQUE INDEX settlements_one_active_per_user ON settlements(user_id) WHERE end_at IS NULL;",
                'pgsql' => "CREATE UNIQUE INDEX settlements_one_active_per_user ON settlements(user_id) WHERE end_at IS NULL;",
            };
            \Illuminate\Support\Facades\DB::statement($sql);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if (in_array($driver, ['sqlite', 'pgsql'], true)) {
            \Illuminate\Support\Facades\DB::statement('DROP INDEX IF EXISTS settlements_one_active_per_user');
        }
        Schema::dropIfExists('settlements');
    }
};
