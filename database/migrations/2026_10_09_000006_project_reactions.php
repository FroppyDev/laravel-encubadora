<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->index()->constrained()->restrictOnDelete();
            $table->enum('type', ['like'])->default('like');
            $table->timestamps();
            $table->unique(['project_id', 'user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_reactions');
    }
};
