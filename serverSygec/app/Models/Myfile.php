<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $mytable
 * @property string $object_code
 * @property string $datafile
 */
class Myfile extends Model
{
    /**
     * @var array
     */
	protected $primaryKey = 'id';
    
    protected $fillable = ['mytable', 'object_code', 'datafile','created_at','updated_at'];

}
