<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'title', 'description', 'status'];

    public function remarks()
    {
        return $this->hasMany(TaskRemark::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

}
