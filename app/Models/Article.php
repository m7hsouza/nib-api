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
    'title', 'content', 'path', 'is_highlighted'
  ];

  protected $hidden = [
    'deleted_at',
  ];

  protected static function boot(): void
  {
    parent::boot();
    self::creating(function (Article $article) {
      $article->author_id = auth()->id();
      return $article;
    });
  }

  public function author(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
