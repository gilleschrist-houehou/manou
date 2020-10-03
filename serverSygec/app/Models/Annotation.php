<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $courrier_code
 * @property string $uniteadmin_code
 * @property string $annotation
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property Courrierentrant $courrierentrant
 * @property Uniteadmin $uniteadmin
 */
class Annotation extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['courrier_code', 'uniteadmin_code', 'annotation', 'created_by', 'updated_by', 'created_at', 'updated_at'];

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
    public function uniteadmin()
    {
        return $this->belongsTo('App\Models\Uniteadmin', 'uniteadmin_code', 'code');
    }
}
