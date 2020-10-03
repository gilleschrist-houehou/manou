<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $mytable
 * @property string $object_code
 * @property string $datafile
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 */
class Myfilejoint extends Model
{
    /**
     * @var array
     */
	protected $primaryKey = 'id';
    
    protected $fillable = ['mytable', 'object_code', 'datafile', 'created_by', 'updated_by', 'created_at', 'updated_at'];

}
