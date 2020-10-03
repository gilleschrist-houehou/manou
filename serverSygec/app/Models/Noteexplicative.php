<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $objet
 * @property string $destinataire
 * @property string $synthese
 * @property string $proposition
 * @property string $ua_signataire_code
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 */
class Noteexplicative extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['objet', 'destinataire', 'synthese', 'proposition', 'ua_signataire_code', 'courrierentrant_code','courrierinitie_code','created_by', 'updated_by', 'created_at', 'updated_at'];

     /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function signataire()
    {
        return $this->belongsTo('App\Models\Uniteadmin', 'ua_signataire_code', 'code');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courrierentrant()
    {
        return $this->belongsTo('App\Models\Courrierentrant', 'courrierentrant_code', 'code');
    }

    public function courrierinterne()
    {
        return $this->belongsTo('App\Models\Courrierinterne', 'courrierinterne_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ua_destinataire()
    {
        return $this->belongsTo('App\Models\Uniteadmin', 'destinataire', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courrierinitie()
    {
        return $this->belongsTo('App\Models\Courrierinitie', 'courrierinitie_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function signatures()
    {
        return $this->hasMany('App\Models\Signaturenoteexplicative', 'courrier_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paraphes()
    {
        return $this->hasMany('App\Models\Paraphenote', 'courrier_code', 'code');
    }



    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parcours()
    {
        return $this->hasMany('App\Models\Parcoursnoteexplicative', 'courrier_code', 'code');
    }


}
