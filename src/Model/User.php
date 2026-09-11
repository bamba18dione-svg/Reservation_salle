<?php

declare(strict_types=1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


final class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'nom',
        'email',
        'password_hash',
    ];

  
    protected $hidden = [
        'password_hash',
    ];
}
