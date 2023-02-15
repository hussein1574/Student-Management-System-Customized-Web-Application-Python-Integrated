<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LecturesTimeTable extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    protected $guarded = ['id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }
    public function lecturesTime()
    {
        return $this->belongsTo(LecturesTime::class, 'lectureTime_id', 'id');
    }
    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    protected $table = 'lectures_time_table';
}
