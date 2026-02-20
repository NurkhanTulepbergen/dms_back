<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penalty_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penalty_id')->constrained('penalties')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('event_type');
            $table->text('description');
            $table->string('file_path')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['penalty_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penalty_redemptions');
    }
};
