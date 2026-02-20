<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penalty_rules', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('title');
            $table->integer('default_points');
            $table->boolean('redeemable')->default(true);
            $table->boolean('creates_financial_charge')->default(false);
            $table->decimal('financial_amount', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penalty_rules');
    }
};
