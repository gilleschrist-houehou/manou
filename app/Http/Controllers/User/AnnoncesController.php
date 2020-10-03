<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Annonces;
use App\Publicites;

class AnnoncesController extends Controller
{
    public function index()
    {
        $arr['annonces'] = Annonces::orderBy('id','desc')->where('visible','=',true)->paginate(8);
        $arr['publicites'] =Publicites::orderBy('id','desc')->where('visible','=',true)->get();
        return view('home')->with($arr);
    }

    public function show($id)
    {
        
        $annonce = Annonces::find($id);
        if($annonce){
        if ($annonce->visible==true) {
        	$arr['annonce']=$annonce;
        	return view('detailAnnonce')->with($arr);
        }
        }

        
    }

public function contact()
    {
        return view('contact');
    }
}
