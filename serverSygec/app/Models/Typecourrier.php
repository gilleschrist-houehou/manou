<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $libelle
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property Courriersortant[] $courriersortants
 * @property Courrierentrant[] $courrierentrants

 */
class Typecourrier extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['code', 'libelle', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courrierentrants()
    {
        return $this->hasMany('App\Models\Courrierentrant', 'typecourrier_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courriersortants()
    {
        return $this->hasMany('App\Models\Courriersortant', 'typecourrier_code', 'code');
    }
}
