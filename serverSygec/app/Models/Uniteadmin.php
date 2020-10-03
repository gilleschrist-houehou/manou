<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $typeuniteadmin_code
 * @property string $code
 * @property string $libelle
 * @property string $sigle
 * @property string $ua_parent_code
 * @property string $email
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property Typeuniteadmin $typeuniteadmin
 * @property Uniteadmin $ua_parent
 * @property Affectation[] $affectations
 * @property Agent[] $agents
 */
class Uniteadmin extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['typeuniteadmin_code', 'code', 'libelle', 'sigle', 'ua_parent_code', 'email','isAdjoint','isDecisionnel', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function typeuniteadmin()
    {
        return $this->belongsTo('App\Models\Typeuniteadmin', 'typeuniteadmin_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ua_parent()
    {
        return $this->belongsTo('App\Models\Uniteadmin', 'ua_parent_code', 'code');
    }


     /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function secretariat()
    {
        return $this->belongsTo('App\Models\Uniteadmin', 'code', 'secretaire_de');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agent()
    {
        return $this->belongsTo('App\Models\Agent', 'code', 'uniteadmin_code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function affectations()
    {
        return $this->hasMany('App\Models\Affectation', 'uniteadmin_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function agents()
    {
        return $this->hasMany('App\Models\Agent', 'uniteadmin_code', 'code');
    }
}
