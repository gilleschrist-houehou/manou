<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Publicites;

class PublicitesController extends Controller
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
        $arr['publicites'] = Publicites::orderBy('id','desc')->paginate(5);
        return view('admin.publicites.index')->with($arr);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.publicites.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Publicites $publicites)
    {
        if($request->image)
        {
            $extImage = $request->image->getClientOriginalExtension();
            $fileImage = date('YmdHis').rand(1,99999).'_image'.'.'.$extImage;
            $request->image->storeAs('public/publicites/images',$fileImage);
        }else{
            $fileImage='';
        }

        if($request->piece)
        {
            $extPiece = $request->piece->getClientOriginalExtension();
            $filePiece = date('YmdHis').rand(1,99999).'_piece'.'.'.$extPiece;
            $request->piece->storeAs('public/publicites/pieces',$filePiece);
        }else{
            $filePiece='';
        }
        $publicites->image = $fileImage;
        $publicites->piece = $filePiece;
        $publicites->title = $request->title;
        $publicites->description = $request->description;
        $publicites->created_by = Auth::id();
        $publicites->updated_by = Auth::id();
        $publicites->save();
        return redirect()->route('admin.publicites.index');
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
        $arr['publicites'] = Publicites::find($id);
       return view('admin.publicites.edit')->with($arr);
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
        $publicites = Publicites::find($id);
        if($request->image)
        {
            if ($publicites->image) {
                $pathFileDeleted ='public/publicites/images/'. $publicites->image;
                Storage::delete($pathFileDeleted);
            }
            $extImage = $request->image->getClientOriginalExtension();
            $fileImage = date('YmdHis').rand(1,99999).'_image'.'.'.$extImage;
            $request->image->storeAs('public/publicites/images',$fileImage);
        }else{
            if(!$publicites->image)
                $fileImage='';
            else
                $fileImage=$publicites->image;
        }

        if($request->piece)
        {
            if ($publicites->piece) {
                $pathFileDeleted ='public/publicites/pieces/'. $publicites->piece;
                Storage::delete($pathFileDeleted);
            }
            $extPiece = $request->piece->getClientOriginalExtension();
            $filePiece = date('YmdHis').rand(1,99999).'_piece'.'.'.$extPiece;
            $request->piece->storeAs('public/publicites/pieces',$filePiece);
        }else{
            if(!$publicites->piece)
                $filePiece='';
            else
                $filePiece=$publicites->piece;
        }
        $publicites->image = $fileImage;
        $publicites->piece = $filePiece;
        $publicites->title = $request->title;
        $publicites->description = $request->description;
        /*$Publicites->created_by = 1;*/
        $publicites->updated_by = Auth::id();
        $publicites->save();
        return redirect()->route('admin.publicites.index');
    }

    public function visibility($id)
    {
        $publicites = Publicites::find($id);
        if ($publicites->visible==0) {
            $publicites->visible = 1;
        }else{
            $publicites->visible = 0;
        }
        
        $publicites->save();
       return redirect()->route('admin.publicites.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       $publicites=Publicites::find($id);
       $getImage=$publicites->image;
       $getPiece=$publicites->piece;
       if($getImage)
       {
        $pathFileDeleted ='public/publicites/images/'. $getImage;
        Storage::delete($pathFileDeleted);
       }
       if($getPiece)
       {
        $pathFileDeleted ='public/publicites/pieces/'. $getPiece;
        Storage::delete($pathFileDeleted);
       }

        Publicites::destroy($id);
        return redirect()->route('admin.publicites.index');
    }
}
