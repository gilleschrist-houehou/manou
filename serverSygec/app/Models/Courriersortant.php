<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $expediteur_code
 * @property string $typecourrier_code
 * @property string $code
 * @property string $objet
 * @property string $referenceCourrier
 * @property string $referenceAttribuee
 * @property boolean $necessiteReponse
 * @property string $ampliataires
 * @property string $dateCourrier
 * @property string $dateReception
 * @property string $resumeCourrier
 * @property string $lienCourrier
 * @property string $codeCourrierReference
 * @property boolean $siReponseCourrier
 * @property string $last_etape_courrier_code
 * @property boolean $finAffectation
 * @property boolean $finTraitement
 * @property string $dateLastOperation
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property Expediteur $expediteur
 * @property Typecourrier $typecourrier
 * @property Affectation[] $affectations
 */
class Courriersortant extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['expediteur_code', 'typecourrier_code', 'code', 'objet', 'referenceCourrier', 'referenceAttribuee', 'necessiteReponse', 'ampliataires', 'dateCourrier', 'dateReception', 'resumeCourrier', 'lienCourrier', 'codeCourrierReference', 'siReponseCourrier', 'last_etape_courrier_code', 'finAffectation', 'finTraitement', 'dateLastOperation', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function destinataire()
    {
        return $this->belongsTo('App\Models\Expediteur', 'expediteur_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function typecourrier()
    {
        return $this->belongsTo('App\Models\Typecourrier', 'typecourrier_code', 'code');
    }

    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fichier()
    {
        return $this->hasMany('App\Models\Myfile', 'object_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function piecesjointes()
    {
        return $this->hasMany('App\Models\Myfilejoint', 'object_code', 'code');
    }

}
