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
    Schema::create('tasks_links', function (Blueprint $table) {
        $table->id();
        $table->foreignId('source_task_id')->constrained('tasks')->onDelete('cascade'); //source task
        $table->foreignId('linked_task_id')->constrained('tasks')->onDelete('cascade'); //linked task
        $table->timestamps();

        // reject double
        $table->unique(['source_task_id', 'linked_task_id']);
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_tasks_links');
    }
};
