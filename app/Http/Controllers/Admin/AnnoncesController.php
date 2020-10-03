<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\User;
/*use App\Http\Controllers\AuthController;
 $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);*/
use App\Annonces;

class AnnoncesController extends Controller
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
        $niveau = User::where('id','=',Auth::id())->pluck('niveau');
        if ($niveau[0]=='Admin') {
            $arr['annonces'] = Annonces::where('created_by','=',Auth::id())->orderBy('id','desc')->paginate(5);
        }else{
            $arr['annonces'] = Annonces::orderBy('id','desc')->paginate(5);
        }
     
        return view('admin.annonces.index')->with($arr);   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.annonces.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Annonces $annonces)
    {
        if($request->image)
        {
            $extImage = $request->image->getClientOriginalExtension();
            $fileImage = date('YmdHis').rand(1,99999).'_image'.'.'.$extImage;
            $request->image->storeAs('public/annonces/images',$fileImage);
        }else{
            $fileImage='';
        }

        if($request->piece)
        {
            $extPiece = $request->piece->getClientOriginalExtension();
            $filePiece = date('YmdHis').rand(1,99999).'_piece'.'.'.$extPiece;
            $request->piece->storeAs('public/annonces/pieces',$filePiece);
        }else{
            $filePiece='';
        }
        $annonces->image = $fileImage;
        $annonces->piece = $filePiece;
        $annonces->title = $request->title;
        $annonces->description = $request->description;
        $annonces->created_by = Auth::id();
        $annonces->updated_by = Auth::id();
        $annonces->save();
        return redirect()->route('admin.annonces.index');
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
        $arr['annonces'] = Annonces::find($id);
       return view('admin.annonces.edit')->with($arr);
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
        $annonces = Annonces::find($id);
        if($request->image)
        {
            if ($annonces->image) {
                $pathFileDeleted ='public/annonces/images/'. $annonces->image;
                Storage::delete($pathFileDeleted);
            }
            $extImage = $request->image->getClientOriginalExtension();
            $fileImage = date('YmdHis').rand(1,99999).'_image'.'.'.$extImage;
            $request->image->storeAs('public/annonces/images',$fileImage);
        }else{
            if(!$annonces->image)
                $fileImage='';
            else
                $fileImage=$annonces->image;
        }

        if($request->piece)
        {
            if ($annonces->piece) {
                $pathFileDeleted ='public/annonces/pieces/'. $annonces->piece;
                Storage::delete($pathFileDeleted);
            }
            $extPiece = $request->piece->getClientOriginalExtension();
            $filePiece = date('YmdHis').rand(1,99999).'_piece'.'.'.$extPiece;
            $request->piece->storeAs('public/annonces/pieces',$filePiece);
        }else{
            if(!$annonces->piece)
                $filePiece='';
            else
                $filePiece=$annonces->piece;
        }
        $annonces->image = $fileImage;
        $annonces->piece = $filePiece;
        $annonces->title = $request->title;
        $annonces->description = $request->description;
        /*$annonces->created_by = 1;*/
        $annonces->updated_by = Auth::id();
        $annonces->save();
        return redirect()->route('admin.annonces.index');
    }

    public function visibility($id)
    {
        $annonces = Annonces::find($id);
        if ($annonces->visible==0) {
            $annonces->visible = 1;
        }else{
            $annonces->visible = 0;
        }
        
        $annonces->save();
       return redirect()->route('admin.annonces.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       $annonces=Annonces::find($id);
       $getImage=$annonces->image;
       $getPiece=$annonces->piece;
       if($getImage)
       {
        $pathFileDeleted ='public/annonces/images/'. $getImage;
        Storage::delete($pathFileDeleted);
       }
       if($getPiece)
       {
        $pathFileDeleted ='public/annonces/pieces/'. $getPiece;
        Storage::delete($pathFileDeleted);
       }

        Annonces::destroy($id);
        return redirect()->route('admin.annonces.index');
    }
}
