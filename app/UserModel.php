<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserModel extends Model
{
  use Notifiable;

  protected $table = 'users';
  protected $primaryKey = 'id';
  protected $fillable = [
      'name', 'email', 'password',
  ];
  protected $hidden = [
      'password', 'remember_token',
  ];
}
