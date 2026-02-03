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

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'age',
        'country',
        'telegram_id',
        'experience_years',
        'experience',
        'job_field',
        'job_description',
        'status',
        'password',
        'role_id',
        'weekly_hours',
        'last_login_at',
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
        'last_login_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean',
        'weekly_hours' => 'float'

    ];
    protected $attributes=[
        'status'=>true,
        'role_id' => Role::volunteer
    ];

    // Relationships
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class,'team_user')->withTimestamps(); // Eloquent will assume the foreign keys columns on the (team_user) Table are (team_id,user_id)
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
    public function isAdmin():bool
    {
        return $this->role_id === Role::admin;
    }
    public function isSupervisor():bool
    {
        return $this->role_id === Role::supervisor;
    }

    public function isVolunteer():bool
    {
        return $this->role_id === Role::volunteer;
    }

    // Events
    protected static function booted() :void
    {
        // Prevent deletion if team has tasks
        static::deleting(function (User $user) {
            if ($user->assignedTasks()->exists()) {
                throw new \Exception('Cannot delete User with existing tasks');
            }
        });

    }

}
