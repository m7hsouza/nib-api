<?php

namespace App\Models;

use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class Article extends Model implements AuthorizableContract
{
  use Authorizable, SoftDeletes;

  protected $table = 'articles';
  protected $fillable = ['title', 'content', 'filename', 'likes'];
  protected $hidden = ['deleted_at', 'filename'];
  protected $appends = ['image_url'];

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

  public function imageUrl(): Attribute
  {
    return Attribute::get(fn () => route('article.image', ['article_id' => $this->id]));
  }
}
