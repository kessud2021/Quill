<?php

namespace App\Models;

use Framework\Database\Model;

class User extends Model {
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
    protected $timestamps = true;
    protected $softDelete = true;
}
