<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $courrier_code
 * @property string $uniteadmin_code
 * @property string $uniteadmindepart_code
 * @property boolean $sens
 * @property string $synthese
 * @property string $proposition
 * @property string $motif
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 */
class Parcoursnoteexplicative extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['courrier_code', 'uniteadmin_code', 'uniteadmindepart_code', 'sens', 'synthese', 'proposition', 'motif', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uniteadmin()
    {
        return $this->belongsTo('App\Models\Uniteadmin', 'uniteadmin_code', 'code');
    }
}
