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
        'created_by',
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
        return $this->belongsTo(User::class, 'created_by');
    }
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function canTransitionTo(string $newStatus): bool
    {
        $transitions=[
            'open' => ['in_progress'],
          'in_progress' => ['testing', 'open','resolved'],
            'testing' => ['resolved', 'in_progress'],
            'resolved' => ['in_progress', 'testing'],
        ];
        return in_array($newStatus, $transitions[$this->status] ?? []);
    }

    // Events
    protected static function booted(): void
    {
        static::creating(function ($project) {
            if (auth()->check()) {
                $project->created_by = Auth::id();
            }
        });
        static::updating(function ($project) {
            if (auth()->check()) {
                $project->updated_by = Auth::id();
            }
        });
    }
}
