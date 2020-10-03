<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Suggestion  extends Model
{

protected $table ='suggestion';

protected $primaryKey ='id';

public $timestamps = true;

protected $fillable = ['id','message','nomEmetteur','emailEmetteur','emailRecepteur','created_at','updated_at'];

}

