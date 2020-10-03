<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $uniteadmin_code
 * @property string $code
 * @property int $ordre
 * @property string $libelle
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property Uniteadmin $uniteadmin
 */
class Etapecourrierentrant extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['uniteadmin_code', 'code', 'ordre', 'libelle', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uniteadmin()
    {
        return $this->belongsTo('App\Models\Uniteadmin', 'uniteadmin_code', 'code');
    }
    
}
