<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\User;

class AnnonceursController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $arr['annonceurs'] = User::orderBy('id','desc')->paginate(5);
        return view('admin.annonceurs.index')->with($arr);  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $arr['mdp'] = "";
        $arr['mailexist'] = "";
        return view('admin.annonceurs.create')->with($arr);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $users)
    {
        if ($request->password!==$request->password_confirmation) {
            $arr['mailexist'] = "";
            $arr['mdp'] = "Les deux mots de passe ne sont pas identiques";
            return view('admin.annonceurs.create')->with($arr);
        }else{
            $ifEmailExist=User::where('email','=',$request->email)->pluck('email');
            if (count($ifEmailExist)) {
                $arr['mdp'] = "";
                $arr['mailexist'] = "Cet identifiant est déjà utilisé";
            return view('admin.annonceurs.create')->with($arr);
            }else{
                $users->name = $request->name;
                $users->email = $request->email;
                $users->password = Hash::make($request->password);
                $users->niveau = $request->niveau;
                /*$users->active = $request->active;*/
                $users->save();
                return redirect()->route('admin.annonceurs.index');
            }
        }
        /*
        $annonceurs->image = $fileImage;
        $annonceurs->piece = $filePiece;
        $annonceurs->title = $request->title;
        $annonceurs->description = $request->description;
        $annonceurs->created_by = Auth::id();
        $annonceurs->updated_by = Auth::id();
        $annonceurs->save();
        return redirect()->route('admin.annonceurs.index');*/
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $arr['mdp'] = "";
        $arr['mailexist'] = "";
        $arr['annonceurs'] = User::find($id);
       return view('admin.annonceurs.edit')->with($arr);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /*$annonceurs = User::find($id);
        if($request->image)
        {
            if ($annonceurs->image) {
                $pathFileDeleted ='public/annonceurs/images/'. $annonceurs->image;
                Storage::delete($pathFileDeleted);
            }
            $extImage = $request->image->getClientOriginalExtension();
            $fileImage = date('YmdHis').rand(1,99999).'_image'.'.'.$extImage;
            $request->image->storeAs('public/annonceurs/images',$fileImage);
        }else{
            if(!$annonceurs->image)
                $fileImage='';
            else
                $fileImage=$annonceurs->image;
        }

        if($request->piece)
        {
            if ($annonceurs->piece) {
                $pathFileDeleted ='public/annonceurs/pieces/'. $annonceurs->piece;
                Storage::delete($pathFileDeleted);
            }
            $extPiece = $request->piece->getClientOriginalExtension();
            $filePiece = date('YmdHis').rand(1,99999).'_piece'.'.'.$extPiece;
            $request->piece->storeAs('public/annonceurs/pieces',$filePiece);
        }else{
            if(!$annonceurs->piece)
                $filePiece='';
            else
                $filePiece=$annonceurs->piece;
        }
        $annonceurs->image = $fileImage;
        $annonceurs->piece = $filePiece;
        $annonceurs->title = $request->title;
        $annonceurs->description = $request->description;
        /*$annonceurs->created_by = 1;*/
        /*$annonceurs->updated_by = Auth::id();
        $annonceurs->save();
        return redirect()->route('admin.annonceurs.index');*/
    }

    public function active($id)
    {
        $annonceurs = User::find($id);
        if ($annonceurs->active==0) {
            $annonceurs->active = 1;
        }else{
            $annonceurs->active = 0;
        }
        
        $annonceurs->save();
       return redirect()->route('admin.annonceurs.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::destroy($id);
        return redirect()->route('admin.annonceurs.index');
    }
}
