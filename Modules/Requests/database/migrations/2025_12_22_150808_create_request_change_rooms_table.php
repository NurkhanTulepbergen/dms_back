<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 'student_id',
     * 'room_id',
     * 'status',
     * 'description',
     */
    public function up(): void
    {
        Schema::create('request_change_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')
                ->references('user_id')
                ->on('dorm_students')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('room_id')
                ->nullable()
                ->constrained('rooms')
                ->nullOnDelete();
            $table->string('status')->default('pending');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_change_rooms');
    }
};
