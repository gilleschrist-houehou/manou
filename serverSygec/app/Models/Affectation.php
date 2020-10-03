<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $courrier_code
 * @property string $directive_code
 * @property string $uniteadmin_code
 * @property string $agent_code
 * @property string $precisionDirective
 * @property string $motifReaffectation
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property Courrierentrant $courrierentrant
 * @property Directive $directive
 * @property Uniteadmin $uniteadmin
 */
class Affectation extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['courrier_code', 'directive_code', 'uniteadmin_code', 'agent_code', 'precisionDirective', 'motifReaffectation', 'priorite', 'nbreJours', 'uniteadmin_ordonnateur_code','courrierinterne_code', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courrierentrant()
    {
        return $this->belongsTo('App\Models\Courrierentrant', 'courrier_code', 'code');
    }

    public function courrierinterne()
    {
        return $this->belongsTo('App\Models\Courrierinterne', 'courrierinterne_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function directive()
    {
        return $this->belongsTo('App\Models\Directive', 'directive_code', 'code');
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
    public function ordonnateur()
    {
        return $this->belongsTo('App\Models\Uniteadmin', 'uniteadmin_ordonnateur_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function affectationscollab()
    {
        return $this->hasMany('App\Models\Affectation', 'affectationdepart_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function jeton()
    {
        return $this->belongsTo('App\Models\Jetoncourrierentrant', 'code', 'jeton_code');
    }

    public function jetoninterne()
    {
        return $this->belongsTo('App\Models\Jetoncourrierinterne', 'code', 'jetoninterne_code');
    }

}
