<?php

declare(strict_types=1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Modele Eloquent d'un compte utilisateur (responsable).
 */
final class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'nom',
        'email',
        'password_hash',
    ];

    /**
     * Jamais serialise (protege contre une fuite accidentelle du hash).
     */
    protected $hidden = [
        'password_hash',
    ];
}
