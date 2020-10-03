<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $courrier_code
 * @property string $etapecourrier_code
 * @property string $uniteadmin_code
 * @property boolean $sens
 * @property string $created_at
 * @property string $updated_at
 * @property Courrierentrant $courrierentrant
 * @property Etapecourrierentrant $etapecourrierentrant
 */
class Parcourscourrierentrant extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['courrier_code', 'etapecourrier_code', 'uniteadmin_code', 'sens', 'created_by', 'updated_by'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courrierentrant()
    {
        return $this->belongsTo('App\Models\Courrierentrant', 'courrier_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function etapecourrierentrant()
    {
        return $this->belongsTo('App\Models\Etapecourrierentrant', 'etapecourrier_code', 'code');
    }

    

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }


}
