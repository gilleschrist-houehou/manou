@extends('layouts.admin')


@section('content')
<div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="overview-wrap">
                                    <h2 class="title-1">Annonceurs</h2>
                                    <a href="{{ route('admin.annonceurs.create') }}" class="au-btn au-btn-icon au-btn--blue">
                                        <i class="zmdi zmdi-plus"></i>Ajouter</a>
                                </div>
                            </div>
                        </div>
                        <div class="row m-t-30">
                            <div class="col-md-12">
                                <!-- DATA TABLE-->
                                <div class="table-responsive m-b-40">
                                    <table class="table table-borderless table-data3">
                                        <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>Email</th>
                                                <th>Niveau</th>
                                                <th>Statut</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($annonceurs))
                                            @foreach($annonceurs as $an)
                                            <tr>
                                                <td>{{ $an->name }}</td>
                                                <td>{{ $an->email }}</td>
                                                <td>{{ $an->niveau }}</td>
                                                @if($an->active==1)
                                                <td>Activé</td>
                                                @else
                                                <td>Désactivé</td>
                                                @endif
                                                <td>
                                                    <div class="table-data-feature">
                                                        <a href="{{route('admin.annonceurs.edit',$an)}}" class="item" data-toggle="tooltip" data-placement="top" title="Editer">
                                                            <i class="zmdi zmdi-edit"></i>
                                                        </a>
                                                        @if($an->active==0)
                                                        <a href="javascript:void(0)" onclick="$(this).parent().find('#activer').submit()" class="item" data-toggle="tooltip" data-placement="top" title="Activer">
                                                            <i class="fas fa-eye-slash"></i>
                                                        </a>
                                                        <form id="activer" action="{{route('admin.annonceurs.active', $an->id)}}" method="post">
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        </form>
                                                        @endif
                                                        @if($an->active==1)
                                                        <a href="javascript:void(0)" onclick="$(this).parent().find('#desactiver').submit()" class="item" data-toggle="tooltip" data-placement="top" title="Désactiver">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <form id="desactiver" action="{{route('admin.annonceurs.active', $an->id)}}" method="post">
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        </form>
                                                        @endif
                                                        <a href="javascript:void(0)" onclick="$(this).parent().find('#supprimer').submit()" class="item" data-toggle="tooltip" data-placement="top" title="Supprimer">
                                                            <i class="zmdi zmdi-delete"></i>
                                                        </a>
                                                        <form id="supprimer" action="{{route('admin.annonceurs.destroy', $an->id)}}" method="post">
                                                            <input name="_method" type="hidden" value="DELETE">
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td colspan="3" style="text-align: center;">Aucune annonce disponible</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                    <div style="margin-top: 10px;"></div>
                                    {{$annonceurs->render()}}
                                </div>
                                <!-- END DATA TABLE-->
                            </div>
                        </div>
                    </div>
                </div>
@endsection