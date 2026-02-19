<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('rooms', 'room_type_id')) {
            return;
        }

        Schema::table('rooms', function (Blueprint $table) {
            $table->foreignId('room_type_id')
                ->nullable()
                ->after('floor_id')
                ->constrained('room_types')
                ->nullOnDelete();

            $table->index('room_type_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('rooms', 'room_type_id')) {
            return;
        }

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex(['room_type_id']);
            $table->dropForeign(['room_type_id']);
            $table->dropColumn('room_type_id');
        });
    }
};

