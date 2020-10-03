@extends('layouts.admin')


@section('content')
<div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="overview-wrap">
                                    <h2 class="title-1">Annonceurs</h2>
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
                                            <strong>l'annonceur</strong>
                                        </div>
                                        <div class="card-body card-block">
                                            <form action="{{route('admin.annonceurs.update',$annonceurs->id)}}" method="post" class="form-horizontal" enctype="multipart/form-data">
                                                 <input name="_method" type="hidden" value="PUT">
                                                 <input type="hidden" name="_token" value="{{ csrf_token() }}">


                                                <div class="row form-group">
                                                    <div class="col col-sm-3">
                                                        <label for="name" class=" form-control-label">Nom</label>
                                                    </div>
                                                    <div class="col col-sm-9">
                                                        <input id="name" class="au-input au-input--full @error('name') is-invalid @enderror" name="name" value="{{$annonceurs->name}}" required autocomplete="name" autofocus placeholder="Nom">
                                                        @error('name')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row form-group">
                                                    <div class="col col-sm-3">
                                                        <label for="email" class=" form-control-label">Adresse email</label>
                                                    </div>
                                                    <div class="col col-sm-9">
                                                        <input id="email" class="au-input au-input--full form-control {{-- @if($mailexist) is-invalid @endif --}}" name="email" value="{{$annonceurs->email}}" required autocomplete="email" type="email" placeholder="Adresse email">
                                                        {{-- @if($mailexist) --}}
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{-- {{ $mailexist }} --}}</strong>
                                                            </span>
                                                        {{-- @endif --}}
                                                    </div>
                                                </div>

                                                <div class="row form-group">
                                                    <div class="col col-sm-3">
                                                        <label for="niveau" class=" form-control-label">Niveau</label>
                                                    </div>
                                                    <div class="col col-sm-9">
                                                        <select name="niveau" id="niveau" class="form-control">
                                                            <option value="Admin" {{ $annonceurs->niveau == 'Admin' ? selected : '' }}>Admin</option>
                                                            <option value="SuperAdmin" {{ $annonceurs->niveau == 'Superadmin' ? selected : '' }}>Superadmin</option>
                                                            {{-- <option value="3">Option #3</option> --}}
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row form-group">
                                                    <div class="col col-sm-3">
                                                        <label for="password" class=" form-control-label">Mot de passe</label>
                                                    </div>
                                                    <div class="col col-sm-9">
                                                        <input id="password" class="au-input au-input--full form-control @if($mdp) is-invalid @endif" name="password" required autocomplete="new-password" type="password" placeholder="Mot de passe">
                                                        @if($mdp)
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $mdp }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="row form-group">
                                                    <div class="col col-sm-3">
                                                        <label for="password-confirm" class=" form-control-label">Confirmer mot de passe</label>
                                                    </div>
                                                    <div class="col col-sm-9">
                                                        <input id="password-confirm" class="au-input au-input--full form-control" name="password_confirmation" required autocomplete="new-password" type="password" placeholder="Confirmer mot de passe">
                                                    </div>
                                                </div>

                                                
                                                
                                                
                                                <div class="card-footer">
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="fa fa-dot-circle-o"></i> Enrgistrer
                                                    </button>
                                                    <a href="{{route('admin.annonceurs.index')}}" class="btn btn-danger btn-sm">
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