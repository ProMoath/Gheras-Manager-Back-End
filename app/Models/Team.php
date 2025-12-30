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
       return $this->belongsToMany(User::class,'user')->withTimestamps();
   }
   public function tasks(): HasMany
   {
       return $this->hasMany(Task::class, 'task_id');
   }


}
