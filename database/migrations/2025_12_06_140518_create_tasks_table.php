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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title',min(3));
            $table->longText('description')->nullable();

            //statues & priority
            $table->enum('status', ['new', 'scheduled', 'in_progress', 'issue', 'done', 'docs'])->default('new');
            $table->enum('priority', ['very_urgent', 'urgent', 'medium', 'normal'])->default('normal');
            $table->date('due_date')->nullable();

            //relationships
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->foreignId('assignee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null'); //exist when create ?

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
            $table->index(['team_id', 'status']);
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
