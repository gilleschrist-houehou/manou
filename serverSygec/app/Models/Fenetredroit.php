<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $fenetre_code
 * @property string $profil_code
 * @property boolean $consultation
 * @property boolean $ajout
 * @property boolean $modification
 * @property boolean $suppression
 * @property boolean $transmission
 * @property Fenetre $fenetre
 * @property Profil $profil
 */
class Fenetredroit extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['fenetre_code', 'profil_code', 'consultation', 'ajout', 'modification', 'suppression', 'transmission'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fenetre()
    {
        return $this->belongsTo('App\Models\Fenetre', 'fenetre_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profil()
    {
        return $this->belongsTo('App\Models\Profil', 'profil_code', 'code');
    }
}
