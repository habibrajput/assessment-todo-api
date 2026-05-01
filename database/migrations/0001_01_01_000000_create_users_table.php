<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the users table.
 *
 * Differences from the default Laravel users table:
 *   - Added  `verification_code`  – random token sent in the welcome e-mail
 *   - Added  `is_verified`        – flipped to true once the user clicks the link
 *   - `email_verified_at` kept for compatibility / future use
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');

            // Custom e-mail verification fields
            $table->string('verification_code')->nullable()->index();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
