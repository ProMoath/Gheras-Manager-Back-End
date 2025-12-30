<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->integer('age')->nullable();
            $table->string('country', 255)->nullable();
            $table->string('telegram_id', 255)->nullable();
            $table->string('job_field')->nullable();
           // $table->enum('role', ['admin', 'supervisor', 'volunteer'])->default('volunteer');
            $table->string('experience')->nullable();
            $table->integer('experience_years')->nullable();
            $table->string('job_description')->nullable();
            $table->boolean('status')->default(true);
            $table->string('password');

            $table->foreignId('role_id')->default(3)->constrained('roles')->onDelete('set null');

            $table->decimal('weekly_hours', 5, 2)->nullable();
            $table->rememberToken();
            $table->timestamps();

            // Indexes columns
            $table->index('role_id');
            $table->index('status');
            $table->index('email');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
