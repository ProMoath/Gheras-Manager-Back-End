<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

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
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->integer('age')->nullable();
            $table->string('country')->nullable();
            $table->string('telegram_id')->nullable();
           // $table->enum('role', ['admin', 'supervisor', 'volunteer'])->default('volunteer');
            $table->integer('experience_years')->nullable();
            $table->text('experience')->nullable();
            $table->string('job_title')->nullable();
            $table->longText('job_description')->nullable();
            $table->boolean('status')->default(true);
            $table->string('password');

            $table->foreignId('role_id')->default(Role::volunteer)->constrained('roles')->onDelete('restrict');

            $table->decimal('weekly_hours', 5, 2)->unsigned()->nullable();
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
