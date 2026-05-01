<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the todos table.
 *
 * Each row belongs to a single user (foreign key with cascade delete –
 * removing a user automatically removes all their todos).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->id();

            // Scoped to a user; cascade keeps data consistent
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('description');

            $table->timestamps();

            // Composite index: most queries filter by user_id first
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
