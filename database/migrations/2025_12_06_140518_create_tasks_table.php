w<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description')->nullable();

            //statues & priority
            $table->enum('status', ['open', 'in_progress', 'testing', 'resolved']);
            $table->enum('priority', ['critical', 'major', 'minor']);
            $table->date('due_date')->nullable();
            $table->enum('type',['new','bug']);
            //relationships
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->foreignId('assignee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            //dates
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->decimal('work_hours', 8, 2)->nullable()->default(0);
            $table->timestamps();


            // Performance Indexes

            $table->index('status');
            $table->index('priority');
            $table->index('project_id');
            $table->index('assignee_id');
            $table->index('created_by');
            $table->index('due_date');

            //Complex Indexes
            $table->index(['assignee_id', 'status']);
            $table->index(['project_id', 'status']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
