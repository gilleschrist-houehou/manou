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
class Jetoncourrierentrant extends Model
{
    /**
     * @var array
     */
    public $timestamps = true;

    protected $fillable = ['code','courrierentrant_code', 'affectationdepart_id', 'uniteadmin_code', 'created_at', 'updated_at'];
  

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function affectations()
    {
        return $this->hasMany('App\Models\Affectation', 'jeton_code', 'code');
    }
}
