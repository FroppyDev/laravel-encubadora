<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['student', 'moderator', 'admin'])->default('student');
            $table->text('bio')->nullable();
            $table->string('avatar_path')->nullable();
        });
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE UNIQUE INDEX users_email_normalized_unique ON users (lower(email))');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS users_email_normalized_unique');
        }
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['role', 'bio', 'avatar_path']));
    }
};
