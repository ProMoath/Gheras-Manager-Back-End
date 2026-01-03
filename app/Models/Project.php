<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'active'
    ];
    protected $casts = [
        'active' => 'boolean'
    ];
    public function Tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
    public function creator(): belongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected static function booted()
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
