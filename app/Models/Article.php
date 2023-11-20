<?php

namespace App\Models;

use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class Article extends Model implements AuthorizableContract
{
  use Authorizable, SoftDeletes;

  protected $table = 'articles';
  protected $fillable = [
    'title', 'content', 'image_url'
  ];

  public function author(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
