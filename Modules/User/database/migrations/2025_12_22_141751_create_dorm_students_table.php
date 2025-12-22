<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 'user_id',
     * 'live_status',
     * 'room_id',
     * 'start_live',
     * 'end_live',
     * 'warning_count'
     */
    public function up(): void
    {
        Schema::create('dorm_students', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->boolean('live_status')->default(false);
            $table->foreignId('room_id')
                ->nullable()
                ->constrained('rooms')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->date('start_live')->nullable();
            $table->date('end_live')->nullable();
            $table->integer('warning_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dorm_students');
    }
};
