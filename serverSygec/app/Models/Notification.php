<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Notification  extends Model
{


protected $primaryKey ='id';

public $timestamps = false;

protected $fillable = ['id', 'courrier_code','uniteadmin_code', 'object','content','email','status','typenotification'];

}

