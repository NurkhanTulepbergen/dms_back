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
        Schema::create('gym_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('gym_plans')->cascadeOnDelete();
            $table->foreignId('charge_id')->constrained('charges')->cascadeOnDelete();

            $table->integer('total_sessions');
            $table->integer('remaining_sessions');

            $table->date('started_at');
            $table->date('expires_at');

            $table->string('status')->default('active');

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->unique('charge_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_memberships');
    }
};

