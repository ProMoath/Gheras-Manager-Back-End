<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;
    public const admin = 1;
    public const supervisor = 2;
    public const volunteer = 3;
    protected $fillable = ['name'];

        //Relationships


    public function user(): HasMany
    {
        return $this->hasMany(User::class);//Eloquent will assume the foreign key column on the (User) model is (role_id)

    }
    public function permissions(): BelongsTo
    {
        return $this->belongsTo(Permission::class); // Assuming permission table has role_id
    }
}
