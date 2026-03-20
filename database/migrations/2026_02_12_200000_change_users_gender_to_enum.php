<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'gender')) {
            return;
        }

        // Normalize existing values before applying enum constraint.
        DB::table('users')
            ->whereNotNull('gender')
            ->whereNotIn('gender', ['male', 'female'])
            ->update(['gender' => null]);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female'])->nullable()->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'gender')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('gender')->nullable()->change();
        });
    }
};
