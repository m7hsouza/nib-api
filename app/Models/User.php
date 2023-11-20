<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
  use Authenticatable, Authorizable, HasFactory;

  protected static function boot(): void
  {
    parent::boot();

    self::creating(function (User $user) {
      ['enrollment_number' => $lastEnrollmentNumber] = User::select('enrollment_number')->orderByDesc('id')->first();
      $enrollment_number = $lastEnrollmentNumber ? ++$lastEnrollmentNumber : 0;
      $user->enrollment_number = mb_str_pad($enrollment_number, 6, '0', STR_PAD_LEFT);
      return $user;
    });
  }

  protected $table = 'users';
  protected $fillable = [
    'name', 'email', 'enrollment_number'
  ];
  protected $hidden = [
    'password',
  ];

  public function getJWTIdentifier()
  {
    return $this->getKey();
  }

  public function getJWTCustomClaims()
  {
    return [];
  }
}
