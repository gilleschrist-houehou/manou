<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $libelle
 * @property string $adresse
 * @property string $tel
 * @property string $interphone
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 */
class Expediteur extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['code', 'libelle', 'adresse', 'tel', 'interphone', 'personnalite','created_by', 'updated_by', 'created_at', 'updated_at'];

}
