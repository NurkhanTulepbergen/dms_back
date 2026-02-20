<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'discipline_limit')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('discipline_limit')->default(10)->after('gender');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'discipline_limit')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('discipline_limit');
            });
        }
    }
};
