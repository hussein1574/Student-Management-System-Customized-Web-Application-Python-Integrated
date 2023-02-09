<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoursePre extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function preCourse()
    {
        return $this->belongsTo(Course::class, 'coursePre_id');
    }
}
