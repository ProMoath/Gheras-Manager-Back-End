<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'active',
        'status',
        'creator_id',
        'updated_by',
    ];
    protected $casts = [
        'active' => 'boolean'
    ];
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
    public function creator(): belongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function canTransitionTo(string $newStatus): bool
    {
        $transitions=[
            'new' => ['scheduled', 'in_progress', 'done', 'docs', 'issue'],
            'scheduled' => ['in_progress', 'done', 'docs', 'issue'],
            'in_progress' => ['done', 'docs', 'issue'],
            'issue' => ['in_progress', 'done', 'docs'],
            'done' => ['in_progress','issue'],
            'docs' => ['in_progress','issue','done'],
        ];
        return in_array($newStatus, $transitions[$this->status] ?? []);
    }

    // Events
    protected static function booted(): void
    {
        static::creating(function ($project) {
            if (auth()->check()) {
                $project->creator_id = Auth::id();
            }
        });
        static::updating(function ($project) {
            if (auth()->check()) {
                $project->updated_by = Auth::id();
            }
        });
    }
}
