<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function user(): HasMany
    {
        return $this->hasMany(User::class);//Eloquent will assume the foreign key column on the (User) model is (role_id)
    }
}
