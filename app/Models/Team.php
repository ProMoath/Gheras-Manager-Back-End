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

}
