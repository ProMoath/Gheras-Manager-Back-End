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
        Schema::create('team_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('team_id')->constrained('teams')->onDelete('restrict');
            $table->timestamps();

            // Composite primary key
            $table->primary(['user_id', 'team_id']);

            // Indexes
            $table->index('team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_team_user');
    }
};

//
