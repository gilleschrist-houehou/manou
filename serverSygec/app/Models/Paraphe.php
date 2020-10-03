<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $agent_code
 * @property string $uniteadmin_code
 * @property string $datafile
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 */
class Paraphe extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['agent_code', 'uniteadmin_code', 'datafile', 'created_by', 'updated_by', 'created_at', 'updated_at'];

}
