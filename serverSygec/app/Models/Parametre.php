<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $adresseServeur
 * @property string $adresseServeurFichier
 * @property string $logo
 * @property string $adresse
 * @property string $piedPage
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 */
class Parametre extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['adresseServeur', 'adresseServeurFichier', 'logo', 'adresse','emailSuggestion', 'piedPage', 'ua_finale_courrier_initie', 'marge', 'tailletexte', 'created_by', 'updated_by', 'created_at', 'updated_at'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function secretariatadmin()
    {
        return $this->belongsTo('App\Models\Uniteadmin', 'ua_finale_courrier_initie', 'code');
    }


}
