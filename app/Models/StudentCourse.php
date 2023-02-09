<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCourse extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function courseStatus()
    {
        return $this->belongsTo(CourseStatu::class);
    }
}
