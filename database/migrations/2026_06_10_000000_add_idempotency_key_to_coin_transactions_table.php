<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('coin_transactions', 'idempotency_key')) {
            return;
        }

        Schema::table('coin_transactions', function (Blueprint $table): void {
            $table->string('idempotency_key')->nullable()->unique()->after('reference_id');
        });

        $seenKeys = [];

        DB::table('coin_transactions')
            ->whereIn('source', [
                'assignment_turned_in',
                'assignment_created',
                'classroom_joined',
                'classroom_created',
            ])
            ->whereNotNull('reference_id')
            ->orderBy('id')
            ->get(['id', 'user_id', 'source', 'reference_id'])
            ->each(function (object $transaction) use (&$seenKeys): void {
                $key = "{$transaction->source}:{$transaction->user_id}:{$transaction->reference_id}";

                if (isset($seenKeys[$key])) {
                    return;
                }

                DB::table('coin_transactions')
                    ->where('id', $transaction->id)
                    ->update(['idempotency_key' => $key]);

                $seenKeys[$key] = true;
            });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('coin_transactions', 'idempotency_key')) {
            return;
        }

        Schema::table('coin_transactions', function (Blueprint $table): void {
            $table->dropUnique(['idempotency_key']);
            $table->dropColumn('idempotency_key');
        });
    }
};
