<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $courrier_code
 * @property string $etapecourrier_code
 * @property string $uniteadmin_code
 * @property string $last_uniteadmin_code
 * @property boolean $sens
 * @property string $motif
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 */
class Parcourscourrierinitie extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['courrier_code', 'uniteadmin_code','texteCourrier', 'sens', 'motif', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courrierinitie()
    {
        return $this->belongsTo('App\Models\Courrierinitie', 'courrier_code', 'code');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    
    public function uniteadmin()
    {
        return $this->belongsTo('App\Models\Uniteadmin', 'uniteadmin_code', 'code');
    }

    public function ua_transmettrice()
    {
        return $this->belongsTo('App\Models\Uniteadmin', 'uniteadmindepart_code', 'code');
    }

}
