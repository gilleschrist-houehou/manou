<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $libelle
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property Uniteadmin[] $uniteadmins
 */
class Typeuniteadmin extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['code', 'libelle', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function uniteadmins()
    {
        return $this->hasMany('App\Models\Uniteadmin', 'typeuniteadmin_code', 'code');
    }
}
