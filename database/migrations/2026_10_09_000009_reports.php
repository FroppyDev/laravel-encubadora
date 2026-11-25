<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->constrained()->restrictOnDelete();
            $table->foreignId('project_id')->nullable()->index()->constrained()->cascadeOnDelete();
            $table->foreignId('comment_id')->nullable()->index()->constrained()->cascadeOnDelete();
            $table->string('reason');
            $table->text('details')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'dismissed'])->default('pending')->index();
            $table->foreignId('reviewed_by')->nullable()->index()->constrained('users')->restrictOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'project_id']);
            $table->unique(['user_id', 'comment_id']);
        });
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE reports ADD CONSTRAINT reports_exactly_one_target CHECK ((project_id IS NOT NULL) <> (comment_id IS NOT NULL))');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
