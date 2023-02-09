<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamsTimeTable extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    protected $table = 'exams_time_table';
}
