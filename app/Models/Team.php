<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'slug',
        'members_count',
    ];
    protected $casts = [
        'member_count' => 'integer',
    ];
    // Relationships
    public function users(): belongsToMany
    {
        return $this->belongsToMany(User::class,'team_user')->withTimestamps();
    }
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
    protected static function booted(): void
    {
        static::deleting(function (Team $team) {
            // Prevent deletion if team has tasks
            if ($team->tasks()->exists()) {
                throw new \Exception('Cannot delete team with existing tasks');
            }
        });
    }
}
