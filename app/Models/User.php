<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'telegram_id',
        'job_field',
        'experience_years',
        'age',
        'country',
        'weekly_hours',
    ];

    /**
     * The attributes that should be hidden for serialization.
      *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean',
        'weekly_hours' => 'float'
    ];

    // Relationships
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class,'team_user')->withTimestamps();
    }
    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }
    public function assignedTasks(): hasMany
    {
        return $this->hasMany(Task::class,'assignee_id');
    }
    public function updatedTasks(): HasMany
    {
        return $this->hasMany(Task::class,'updated_by');
    }

    // Accessors
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }
    public function scopeSupervisor($query)
    {
        return $query->where('role','supervisor');
    }

    public function scopeVolunteer($query)
    {
        return $query->where('role','volunteer');
    }


    // Events
    protected static function booted()
    {
        static::deleting(function (User $user) {
            // Prevent deletion if team has tasks
            if ($user->assignedTasks()->exists()) {
                throw new \Exception('Cannot delete User with existing tasks');
            }
        });

        /*

        */
    }
    public function getJWTIdentifier()
    {
        return $this->getKey(); // return id
    }

    public function getJWTCustomClaims()
    {
        return []; // if we want additional data in the token
    }

}
