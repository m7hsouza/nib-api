<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Door extends Model
{
  protected $table = 'doors';

  protected $fillable = ['name'];
}
