<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LecturesTime extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    protected $guarded = ['id'];
    public function lecturesTimeTable(){
        return $this->hasMany(LecturesTimeTable::class);
    }
}
