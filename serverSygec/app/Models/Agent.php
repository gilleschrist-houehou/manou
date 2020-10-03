<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $fonctionagent_code
 * @property string $uniteadmin_code
 * @property string $code
 * @property string $nom
 * @property boolean $activer
 * @property string $numero_matricule
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property Fonctionagent $fonctionagent
 * @property Uniteadmin $uniteadmin
 */
class Agent extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['fonctionagent_code', 'uniteadmin_code', 'code', 'nom', 'sexe', 'activer', 'numero_matricule', 'siAssistant','uniteadminprincipal_code', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fonction()
    {
        return $this->belongsTo('App\Models\Fonctionagent', 'fonctionagent_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uniteadmin()
    {
        return $this->belongsTo('App\Models\Uniteadmin', 'uniteadmin_code', 'code');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'code', 'agent_code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uniteadmin_patron()
    {
        return $this->belongsTo('App\Models\Uniteadmin', 'uniteadminprincipal_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function signature()
    {
        return $this->hasMany('App\Models\Signature', 'agent_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paraphe()
    {
        return $this->hasMany('App\Models\Paraphe', 'agent_code', 'code');
    }

}  
