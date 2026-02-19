<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('request_lives')) {
            return;
        }

        $hasLegacyRoomId = Schema::hasColumn('request_lives', 'room_id');
        $hasLegacyDocuments = Schema::hasColumn('request_lives', 'documents');
        if (!$hasLegacyRoomId && !$hasLegacyDocuments) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        Schema::rename('request_lives', 'request_lives_legacy');

        Schema::create('request_lives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('preferred_room_id')
                ->nullable()
                ->constrained('rooms')
                ->nullOnDelete();
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        // Copy data, mapping room_id -> preferred_room_id.
        DB::statement(
            "INSERT INTO request_lives (id, user_id, preferred_room_id, status, created_at, updated_at)
             SELECT id, user_id, room_id, status, created_at, updated_at
             FROM request_lives_legacy"
        );

        // Migrate legacy documents JSON -> documents table when possible.
        if (Schema::hasTable('documents') && $hasLegacyDocuments) {
            $rows = DB::table('request_lives_legacy')
                ->select(['id', 'documents'])
                ->whereNotNull('documents')
                ->get();

            foreach ($rows as $row) {
                $parsed = json_decode((string) $row->documents, true);
                if (!is_array($parsed)) {
                    continue;
                }

                // Accept either array of objects or array of strings.
                foreach ($parsed as $doc) {
                    if (is_array($doc)) {
                        $type = $doc['type'] ?? 'unknown';
                        $path = $doc['path'] ?? null;
                        if (!is_string($path) || $path === '') {
                            continue;
                        }
                        DB::table('documents')->insert([
                            'request_id' => $row->id,
                            'type' => is_string($type) ? $type : 'unknown',
                            'path' => $path,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        continue;
                    }

                    if (is_string($doc) && $doc !== '') {
                        DB::table('documents')->insert([
                            'request_id' => $row->id,
                            'type' => 'unknown',
                            'path' => $doc,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        Schema::drop('request_lives_legacy');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // No safe down.
    }
};

