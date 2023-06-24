<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HallsDepartment extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    protected $guarded = ['id'];

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}