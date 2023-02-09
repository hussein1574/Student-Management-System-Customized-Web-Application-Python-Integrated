<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentCourse extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    protected $guarded = ['id'];
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
