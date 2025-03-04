<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function studentCourses()
    {
        return $this->hasMany(StudentCourse::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function scopeNonAdmin($query)
    {
        return $query->whereHas('user', function ($subQuery) {
            $subQuery->where('isAdmin', false);
        });
    }
}
