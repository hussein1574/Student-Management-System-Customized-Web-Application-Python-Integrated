<?php

namespace App\Models;

use App\Models\AcademicAdvisor;

use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;



class User extends Authenticatable
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'isAdmin',
        'isActivated',
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
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

    public function professor()
    {
        return $this->hasOne(Professor::class);
    }
    public function student()
    {
        return $this->hasOne(Student::class);
    }
    public function acadmicAdvisor()
    {
        return $this->hasOne(AcademicAdvisor::class);
    }
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}