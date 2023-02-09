<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessorCourse extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    protected $guarded = ['id'];
    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
