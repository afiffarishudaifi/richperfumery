<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class MasterUserModel extends Model
{
  use Notifiable;

  protected $table = 'users';
  protected $primaryKey = 'id';
  protected $fillable = [
      'group_id', 'name', 'email', 'password', 'users_email','id_profil'
  ];
  protected $hidden = [
      'password', 'remember_token',
  ];
}
