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
    Schema::create('tasklink', function (Blueprint $table) {
        $table->foreignId('source_task_id')->nullable()->constrained('tasks')->onDelete('set null'); //source task
        $table->foreignId('linked_task_id')->nullable()->constrained('tasks')->onDelete('set null'); //linked task
        $table->timestamps();

        // Composite primary key
        $table->primary(['source_task_id', 'linked_task_id']);
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasklink');
    }
};
