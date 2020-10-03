<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $nom
 * @property string $link
 * @property string $fenetre_parent
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property Fenetre $fenetreparent
 */
class Fenetre extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['code', 'nom', 'link', 'fenetre_parent', 'created_by', 'updated_by', 'created_at', 'updated_at'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\Fenetre', 'fenetre_parent', 'code');
    }
}
