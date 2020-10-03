@extends('layouts.admin')


@section('content')
<div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="overview-wrap">
                                    <h2 class="title-1">Publicités</h2>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-1">
                            </div>
                            <div class="col-md-10">
                                <div class="card">
                                        <div class="card-header">
                                            Editer  
                                            <strong>la publicité</strong>
                                        </div>
                                        <div class="card-body card-block">
                                            <form action="{{route('admin.publicites.update',$publicites->id)}}" method="post" class="form-horizontal" enctype="multipart/form-data">
                                                 <input name="_method" type="hidden" value="PUT">
                                                 <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <div class="row form-group">
                                                    <div class="col col-sm-3">
                                                        <label for="title" class=" form-control-label">Titre</label>
                                                    </div>
                                                    <div class="col col-sm-9">
                                                        <input type="text" id="title" name="title" placeholder="" class="form-control" value="{{$publicites->title}}" required>
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col col-md-3">
                                                        <label for="description" class=" form-control-label">Description</label>
                                                    </div>
                                                    <div class="col-12 col-md-9">
                                                        <textarea name="description" id="description" rows="9" placeholder="" class="form-control" required>{{$publicites->description}}</textarea>
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col col-md-3">
                                                        <label for="image" class=" form-control-label">Image (Taille 600X500px)</label>
                                                    </div>
                                                    <div class="col-12 col-md-9">
                                                        <input type="file" id="image" name="image" class="form-control-file">
                                                    </div>
                                                    @if($publicites->image)
                                                    <div class="col-md-3"></div>
                                                    <div class="col-md-9">
                                                      <img src="{{asset('/public/storage/publicites/images/'.$publicites->image)}}" style="width: 550px;">
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    @endif
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col col-md-3">
                                                        <label for="piece" class=" form-control-label">Pièce jointe</label>
                                                    </div>
                                                    <div class="col-12 col-md-9">
                                                        <input type="file" id="piece" name="piece" class="form-control-file">
                                                    </div>
                                                    @if($publicites->piece)
                                                    <div class="col-md-3"></div>
                                                    <div class="col-md-9">
                                                      <img src="{{asset('/public/dist-admin/images/pdf.PNG')}}">
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    @endif
                                                </div>
                                                <div class="card-footer">
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="fa fa-dot-circle-o"></i> Enrgistrer
                                                    </button>
                                                    <a href="{{route('admin.publicites.index')}}" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-ban"></i> Annuler
                                                    </a>
                                                </div>
                                            </form>
                                        </div>
                                        
                                </div>
                            </div>
                            <div class="col-md-1">
                            </div>
                        </div>
                    </div>
</div>
@endsection