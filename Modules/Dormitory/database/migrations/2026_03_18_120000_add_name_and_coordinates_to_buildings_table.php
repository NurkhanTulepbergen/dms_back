<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            if (! Schema::hasColumn('buildings', 'name')) {
                $table->string('name')->nullable();
            }

            if (! Schema::hasColumn('buildings', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable();
            }

            if (! Schema::hasColumn('buildings', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            if (Schema::hasColumn('buildings', 'longitude')) {
                $table->dropColumn('longitude');
            }

            if (Schema::hasColumn('buildings', 'latitude')) {
                $table->dropColumn('latitude');
            }

            if (Schema::hasColumn('buildings', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
