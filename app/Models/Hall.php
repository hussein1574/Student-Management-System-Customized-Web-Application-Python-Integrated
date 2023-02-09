<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    protected $guarded = ['id'];
    public function examsTimeTable()
    {
        return $this->hasOne(ExamsTimeTable::class);
    }
    public function lecturesTimeTable()
    {
        return $this->hasOne(LecturesTimeTable::class);
    }
}
