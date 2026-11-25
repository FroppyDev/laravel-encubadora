<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->constrained()->restrictOnDelete();
            $table->foreignId('category_id')->index()->constrained()->restrictOnDelete();
            $table->string('title', 180);
            $table->string('slug')->unique();
            $table->string('summary', 500);
            $table->text('description');
            $table->text('problem_statement')->nullable();
            $table->text('proposed_solution')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->text('repository_url')->nullable();
            $table->text('demo_url')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
