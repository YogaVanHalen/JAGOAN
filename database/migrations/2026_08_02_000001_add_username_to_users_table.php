<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->nullable()->unique()->after('name');
            });
        }

        // Auto populate username for existing users without username
        $users = DB::table('users')->whereNull('username')->orWhere('username', '')->get();
        foreach ($users as $u) {
            $baseUsername = Str::slug(explode('@', $u->email)[0], '');
            if (empty($baseUsername)) {
                $baseUsername = Str::slug($u->name, '');
            }
            if (empty($baseUsername)) {
                $baseUsername = 'user' . $u->id;
            }

            $username = $baseUsername;
            $counter = 1;
            while (DB::table('users')->where('username', $username)->where('id', '!=', $u->id)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            DB::table('users')->where('id', $u->id)->update(['username' => strtolower($username)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('username');
            });
        }
    }
};
