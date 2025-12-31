<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'slug',
        'member_count',
    ];
    protected $casts = [
        'member_count' => 'integer',
    ];
   public function users()
   {
       return $this->belongsToMany(User::class,'team_user')->withTimestamps();
   }
   public function tasks(): HasMany
   {
       return $this->hasMany(Task::class);
   }

    // Events
    protected static function booted()
    {
        static::deleting(function (Team $team) {
            // Prevent deletion if team has tasks
            if ($team->tasks()->exists()) {
                throw new \Exception('Cannot delete team with existing tasks');
            }
        });
    }

}
