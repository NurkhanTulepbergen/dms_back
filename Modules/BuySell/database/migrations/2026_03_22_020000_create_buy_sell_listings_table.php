<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buy_sell_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 160);
            $table->string('category', 40);
            $table->string('condition', 40);
            $table->decimal('price', 12, 2);
            $table->string('pickup_location')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('status', 20)->default('active');
            $table->text('description');
            $table->json('image_paths');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'category']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buy_sell_listings');
    }
};
