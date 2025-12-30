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
        Schema::create('sub_tasks', function (Blueprint $table) {
            $table->foreignId('parent_task')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('sub_task')->constrained('tasks')->onDelete('cascade');
            $table->timestamps();

            // Composite primary key
            $table->primary(['parent_task', 'sub_task']);

            $table->index('parent_task');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_tasks');
    }
};
