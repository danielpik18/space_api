<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'date', 'priority'];

    public function taskCategory()
    {
        return $this->belongsTo('App\TaskCategory');
    }
}
