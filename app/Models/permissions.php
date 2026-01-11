<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class permissions extends Model
{
    use HasFactory;
    public function Role():HasMany
    {
        return $this->hasMany(Role::class); // Assuming Role table has permission_id
    }
}
