<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $libelle
 * @property string $code
 * @property boolean $hasDelai
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property Affectation[] $affectations
 */
class Directive extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['libelle', 'code', 'hasDelai', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function affectations()
    {
        return $this->hasMany('App\Models\Affectation', 'directive_code', 'code');
    }
}
