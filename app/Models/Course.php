<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    protected $guarded = ['id'];

    public function studentCourses()
    {
        return $this->hasMany(StudentCourse::class);
    }
    public function professorCourses()
    {
        return $this->hasMany(ProfessorCourse::class);
    }
    public function departmentCourses()
    {
        return $this->hasMany(DepartmentCourse::class);
    }
    public function examsTimeTable()
    {
        return $this->hasMany(ExamsTimeTable::class);
    }
    public function lecturesTimeTable()
    {
        return $this->hasMany(LecturesTimeTable::class);
    }
    public function coursePre()
    {
        return $this->hasMany(CoursePre::class);
    }
}