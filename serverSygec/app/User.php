<?php

namespace App\\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $profil_code
 * @property string $agent_code
 * @property int $statut
 * @property int $created_by
 * @property int $updated_by
 * @property string $remember_token
 * @property string $created_at
 * @property string $updated_at
 */
class User extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['code', 'name', 'email', 'password', 'profil_code', 'agent_code', 'statut', 'created_by', 'updated_by', 'remember_token', 'created_at', 'updated_at'];

}
