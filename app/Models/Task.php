<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
  protected $table = 'tasks';

  protected $fillable = [
    'description', 'schedule_id', 'responsible_id', 'grade', 'observation'
  ];
}
