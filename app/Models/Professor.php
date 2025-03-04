<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    protected $guarded = ["id"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function professorCourses()
    {
        return $this->hasMany(ProfessorCourse::class);
    }
    public function professorDays()
    {
        return $this->hasMany(ProfessorDay::class);
    }
    public function lecturesTimeTable()
    {
        return $this->hasMany(LecturesTimeTable::class);
    }
}
