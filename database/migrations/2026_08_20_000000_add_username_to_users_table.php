<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'username')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 15)->nullable()->unique()->after('name');
        });

        DB::table('users')->whereNull('username')->orderBy('id')->get(['id', 'name'])->each(function ($user) {
            DB::table('users')->where('id', $user->id)->update([
                'username' => User::generateUniqueUsername($user->name),
            ]);
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'username')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
