<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Publicites;

class PublicitesController extends Controller
{
    public function show($id)
    {
        $publicite = Publicites::find($id);
        if($publicite){
                if ($publicite->visible==true) {
                $arr['publicite']=$publicite;
                return view('detailPublicite')->with($arr);
            }
        }
        

        
    }
}
