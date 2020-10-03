<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $uniteadmin_code
 * @property string $code
 * @property string $objet
 * @property string $destinataire_code
 * @property string $typecourrier_code
 * @property string $civiliteDestinataire
 * @property string $titreDestinataire
 * @property string $signataire
 * @property string $referenceCourrier
 * @property string $referenceAttribuee
 * @property string $ampliataires
 * @property string $dateCourrier
 * @property string $dateReception
 * @property string $texteCourrier
 * @property string $fichier
 * @property string $courrierentrant_code
 * @property string $courrierentrant_reference
 * @property boolean $siReponseCourrier
 * @property string $last_etape_courrier_code
 * @property string $last_agent_code
 * @property boolean $finTraitement
 * @property string $dateLastOperation
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property Uniteadmin $uniteadmin
 */
class Courrierinitie extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['uniteadmin_code', 'code', 'objet', 'destinataire_code', 'typecourrier_code', 'civiliteDestinataire', 'titreDestinataire', 'ua_signataire_code', 'referenceCourrier', 'referenceAttribuee', 'ampliataires', 'dateCourrier', 'texteCourrier', 'fichier', 'courrierentrant_code', 'courrierentrant_reference', 'siReponseCourrier', 'last_etape_courrier_code', 'last_agent_code', 'finTraitement', 'dateLastOperation', 'created_by', 'updated_by', 'created_at', 'updated_at'];

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
    public function destinataire()
    {
        return $this->belongsTo('App\Models\Expediteur', 'destinataire_code', 'code');
    }

    public function destinataireinterne()
    {
        return $this->belongsTo('App\Models\Uniteadmin', 'destinataireInterne_code', 'code');
    }

     /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function signataire()
    {
        return $this->belongsTo('App\Models\Uniteadmin', 'ua_signataire_code', 'code');
    }


    public function courrierentrant()
    {
        return $this->belongsTo('App\Models\Courrierentrant', 'courrierentrant_code', 'code');
    }

    public function courrierinterne()
    {
        return $this->belongsTo('App\Models\Courrierinterne', 'courrierentrant_code', 'code');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parcours()
    {
        return $this->hasMany('App\Models\Parcourscourrierinitie', 'courrier_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function noteexplicatives()
    {
        return $this->hasMany('App\Models\Noteexplicative', 'courrierinitie_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function signatures()
    {
        return $this->hasMany('App\Models\Signaturecourrierinitie', 'courrier_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paraphes()
    {
        return $this->hasMany('App\Models\Paraphecourrierinitie', 'courrier_code', 'code');
    }

    public function fichier()
    {
        return $this->hasMany('App\Models\Myfile', 'object_code', 'code');
    }

}
