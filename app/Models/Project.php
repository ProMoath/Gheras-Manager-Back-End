<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
