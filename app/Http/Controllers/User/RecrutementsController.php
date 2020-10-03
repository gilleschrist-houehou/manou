<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Recrutements;
use App\Publicites;

class RecrutementsController extends Controller
{
    public function index()
    {
        $arr['recrutements'] = Recrutements::orderBy('id','desc')->where('visible','=',true)->paginate(8);
        $arr['publicites'] =Publicites::orderBy('id','desc')->where('visible','=',true)->get();
        return view('recrutement')->with($arr);
    }

    public function show($id)
    {
        $recrutement = Recrutements::find($id);
        if($recrutement){
        if ($recrutement->visible==true) {
        	$arr['recrutement']=$recrutement;
        	return view('detailRecrutement')->with($arr);
        }
        }

        
    }
}
