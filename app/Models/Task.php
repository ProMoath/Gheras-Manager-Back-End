<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'description', 'status', 'priority', 'due_date',
        'team_id', 'project_id', 'assignee_id',
        'started_at', 'completed_at', 'work_hours'
    ];

    // casting
    protected $casts = [
        'due_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'work_hours' => 'decimal:2',
    ];

    // Relationships
    public function creator(): belongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function editor(): belongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function assignee(): belongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
    public function linkedTask(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'linked_task_id','source_task_id');
    }
    public function sourceTask(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'source_task_id','linked_task_id');
    }
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    public function canTransitionTo(string $newStatus): bool
    {
        $transitions = [
            'new' => ['scheduled', 'in_progress', 'done', 'docs', 'issue'],
            'scheduled' => ['in_progress', 'done', 'docs', 'issue'],
            'in_progress' => ['done', 'docs', 'issue'],
            'issue' => ['in_progress', 'done', 'docs'],
            'done' => [],
            'docs' => [],
        ];

        return in_array($newStatus, $transitions[$this->status] ?? []);
    }

    // Events
    protected static function booted()
    {
        static::creating(function ($task) {
            if(auth()->check()) {
                $task->created_by = Auth::id();
            }
        });
        static::updating(function ($task) {
            if(auth()->check()) {
                $task->updated_by = Auth::id();

            }
        });         // automatically add creator or updater of the task
        static::updating(function (Task $task) {
            if ($task->isDirty('status')) {
                $newStatus = $task->status;

                if ($newStatus === 'in_progress' && !$task->started_at) {
                    $task->started_at = now();
                }

                if ($newStatus === 'done' && !$task->completed_at) {
                    $task->completed_at = now();
                }
            }

        });

    }
    // Status transition logic

}

