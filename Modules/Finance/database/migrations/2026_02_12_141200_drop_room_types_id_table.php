<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('room_types_id');
    }

    public function down(): void
    {
        if (Schema::hasTable('room_types_id')) {
            return;
        }

        Schema::create('room_types_id', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('capacity');
            $table->decimal('semester_price', 12, 2);
            $table->timestamps();
        });
    }
};

