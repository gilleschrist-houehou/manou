<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\Recrutements;

class RecrutementsController extends Controller
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
            $arr['recrutements'] = Recrutements::where('created_by','=',Auth::id())->orderBy('id','desc')->paginate(5);
        }else{
            $arr['recrutements'] = Recrutements::orderBy('id','desc')->paginate(5);
        }
        return view('admin.recrutements.index')->with($arr);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.recrutements.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Recrutements $recrutements)
    {
        if($request->image)
        {
            $extImage = $request->image->getClientOriginalExtension();
            $fileImage = date('YmdHis').rand(1,99999).'_image'.'.'.$extImage;
            $request->image->storeAs('public/recrutements/images',$fileImage);
        }else{
            $fileImage='';
        }

        if($request->piece)
        {
            $extPiece = $request->piece->getClientOriginalExtension();
            $filePiece = date('YmdHis').rand(1,99999).'_piece'.'.'.$extPiece;
            $request->piece->storeAs('public/recrutements/pieces',$filePiece);
        }else{
            $filePiece='';
        }
        $recrutements->image = $fileImage;
        $recrutements->piece = $filePiece;
        $recrutements->title = $request->title;
        $recrutements->description = $request->description;
        $recrutements->created_by = Auth::id();
        $recrutements->updated_by = Auth::id();
        $recrutements->save();
        return redirect()->route('admin.recrutements.index');
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
        $arr['recrutements'] = Recrutements::find($id);
       return view('admin.recrutements.edit')->with($arr);
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
        $recrutements = Recrutements::find($id);
        if($request->image)
        {
            if ($recrutements->image) {
                $pathFileDeleted ='public/recrutements/images/'. $recrutements->image;
                Storage::delete($pathFileDeleted);
            }
            $extImage = $request->image->getClientOriginalExtension();
            $fileImage = date('YmdHis').rand(1,99999).'_image'.'.'.$extImage;
            $request->image->storeAs('public/recrutements/images',$fileImage);
        }else{
            if(!$recrutements->image)
                $fileImage='';
            else
                $fileImage=$recrutements->image;
        }

        if($request->piece)
        {
            if ($recrutements->piece) {
                $pathFileDeleted ='public/recrutements/pieces/'. $recrutements->piece;
                Storage::delete($pathFileDeleted);
            }
            $extPiece = $request->piece->getClientOriginalExtension();
            $filePiece = date('YmdHis').rand(1,99999).'_piece'.'.'.$extPiece;
            $request->piece->storeAs('public/recrutements/pieces',$filePiece);
        }else{
            if(!$recrutements->piece)
                $filePiece='';
            else
                $filePiece=$recrutements->piece;
        }
        $recrutements->image = $fileImage;
        $recrutements->piece = $filePiece;
        $recrutements->title = $request->title;
        $recrutements->description = $request->description;
        /*$Recrutements->created_by = 1;*/
        $recrutements->updated_by = Auth::id();
        $recrutements->save();
        return redirect()->route('admin.recrutements.index');
    }

    public function visibility($id)
    {
        $recrutements = Recrutements::find($id);
        if ($recrutements->visible==0) {
            $recrutements->visible = 1;
        }else{
            $recrutements->visible = 0;
        }
        
        $recrutements->save();
       return redirect()->route('admin.recrutements.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       $recrutements=Recrutements::find($id);
       $getImage=$recrutements->image;
       $getPiece=$recrutements->piece;
       if($getImage)
       {
        $pathFileDeleted ='public/recrutements/images/'. $getImage;
        Storage::delete($pathFileDeleted);
       }
       if($getPiece)
       {
        $pathFileDeleted ='public/recrutements/pieces/'. $getPiece;
        Storage::delete($pathFileDeleted);
       }

        Recrutements::destroy($id);
        return redirect()->route('admin.recrutements.index');
    }
}
