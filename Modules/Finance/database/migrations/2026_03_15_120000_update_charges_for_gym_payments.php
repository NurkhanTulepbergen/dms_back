<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->dropForeign(['settlement_id']);
        });

        Schema::table('charges', function (Blueprint $table) {
            $table->unsignedBigInteger('settlement_id')->nullable()->change();
            $table->foreignId('gym_plan_id')->nullable()->after('settlement_id')->constrained('gym_plans')->nullOnDelete();
            $table->foreign('settlement_id')->references('id')->on('settlements')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->dropForeign(['gym_plan_id']);
            $table->dropColumn('gym_plan_id');
            $table->dropForeign(['settlement_id']);
        });

        Schema::table('charges', function (Blueprint $table) {
            $table->unsignedBigInteger('settlement_id')->nullable(false)->change();
            $table->foreign('settlement_id')->references('id')->on('settlements')->cascadeOnDelete();
        });
    }
};
