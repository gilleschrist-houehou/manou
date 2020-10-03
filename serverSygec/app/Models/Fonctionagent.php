<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $libelle
 * @property boolean $signataire
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property Agent[] $agents
 * @property Etapecourrierentrant[] $etapecourrierentrants
 */
class Fonctionagent extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['code', 'libelle', 'signataire', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function agents()
    {
        return $this->hasMany('App\Models\Agent', 'fonctionagent_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function etapecourrierentrants()
    {
        return $this->hasMany('App\Models\Etapecourrierentrant', 'fonctionagent_code', 'code');
    }
}
