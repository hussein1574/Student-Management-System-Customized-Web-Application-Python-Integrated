<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    public function professorDays()
    {
        return $this->hasMany(ProfessorDay::class);
    }
    public function lecturesTimeTable()
    {
        return $this->hasOne(LecturesTimeTable::class);
    }
}
