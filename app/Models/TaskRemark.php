<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskRemark extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['task_id', 'remark', 'status'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    
}
