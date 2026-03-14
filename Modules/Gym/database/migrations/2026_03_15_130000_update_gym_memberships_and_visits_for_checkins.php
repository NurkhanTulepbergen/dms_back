<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gym_visits', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('membership_id')->constrained()->cascadeOnDelete();
            $table->date('visit_date')->nullable()->after('user_id');
            $table->timestamp('check_in_at')->nullable()->after('visit_date');
            $table->timestamp('check_out_at')->nullable()->after('check_in_at');
            $table->integer('duration_minutes')->nullable()->after('check_out_at');
            $table->unsignedInteger('sessions_used')->default(1)->after('duration_minutes');
            $table->string('status')->default('completed')->after('sessions_used');
        });

        DB::table('gym_visits')
            ->whereNull('user_id')
            ->update([
                'user_id' => DB::raw('(select gym_memberships.user_id from gym_memberships where gym_memberships.id = gym_visits.membership_id)'),
            ]);

        DB::table('gym_visits')
            ->whereNull('visit_date')
            ->update(['visit_date' => DB::raw('date(used_at)')]);

        DB::table('gym_visits')
            ->whereNull('check_in_at')
            ->update(['check_in_at' => DB::raw('used_at')]);

        DB::table('gym_visits')
            ->whereNull('check_out_at')
            ->update(['check_out_at' => DB::raw('used_at')]);

        DB::table('gym_visits')
            ->whereNull('duration_minutes')
            ->update(['duration_minutes' => 0]);

        DB::table('gym_memberships')
            ->whereDate('expires_at', '>=', now()->toDateString())
            ->where('remaining_sessions', '<=', 0)
            ->update(['status' => 'exhausted']);

        DB::table('gym_memberships')
            ->whereDate('expires_at', '<', now()->toDateString())
            ->update(['status' => 'expired']);

        Schema::table('gym_visits', function (Blueprint $table) {
            $table->dropColumn('used_at');
            $table->foreignId('user_id')->nullable(false)->change();
            $table->date('visit_date')->nullable(false)->change();
            $table->timestamp('check_in_at')->nullable(false)->change();

            $table->index(['user_id', 'status']);
            $table->index(['membership_id', 'visit_date']);
        });
    }

    public function down(): void
    {
        Schema::table('gym_visits', function (Blueprint $table) {
            $table->timestamp('used_at')->nullable()->after('membership_id');
        });

        DB::table('gym_visits')
            ->whereNull('used_at')
            ->update(['used_at' => DB::raw('check_in_at')]);

        Schema::table('gym_visits', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['membership_id', 'visit_date']);
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn([
                'visit_date',
                'check_in_at',
                'check_out_at',
                'duration_minutes',
                'sessions_used',
                'status',
            ]);
            $table->timestamp('used_at')->nullable(false)->change();
        });
    }
};
