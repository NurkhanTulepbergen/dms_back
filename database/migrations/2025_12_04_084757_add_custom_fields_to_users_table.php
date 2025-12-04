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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('student');
            $table->string('lastname')->nullable()->after('name');
            $table->string('middlename')->nullable()->after('lastname');
            $table->string('phone_number')->nullable()->after('password');
            $table->string('uni_id')->nullable()->after('middlename');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'lastname',
                'middlename',
                'phone_number',
                'uni_id',
            ]);
        });
    }
};
