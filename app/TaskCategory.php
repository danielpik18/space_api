<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Task;

class TaskCategory extends Model
{
    protected $fillable = ['name', 'color', 'icon_class'];

    public function tasks(){
        return $this->hasMany(Task::class);
    }
}
