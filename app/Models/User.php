<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use  HasApiTokens, HasFactory, Notifiable, SoftDeletes, Uuids, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'created_at',
        'updated_at',
    ];
    //    protected $guard_name = 'sanctum';


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = [
        'employee',
        'roles',
        'roles.permissions'
    ];

    public function notifications(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Notification::class)
            ->withPivot('is_view', 'send');
    }

    public function employee(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Employee::class);
    }
}
