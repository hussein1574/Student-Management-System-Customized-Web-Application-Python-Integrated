<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    protected $guarded = ['id'];
    public function students()
    {
        return $this->hasMany(Student::class);
    }
    public function DepartmentCourses()
    {
        return $this->hasMany(DepartmentCourse::class);
    }
    
}
