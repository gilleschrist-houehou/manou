@extends('layouts.user')


@section('content')
           <div class="templatemo-team" id="templatemo-about">
            <div class="container">
                <div class="row">
                    <div class="templatemo-line-header">
                        <div class="text-center">
                            {{-- <hr class="team_hr team_hr_left"/> --}}
                            <span style="display: block; width: 100%;">{{$recrutement->title}}</span>
                            {{-- <hr class="team_hr team_hr_right" /> --}}
                        </div>
                    </div>
                </div>
                <div class="clearfix"> </div>
                    <ul class="row row_team">
                        
                       
                       
                        <li class="col-lg-12 col-md-12 col-sm-12 ">
                            <div class="text-center">
                                <div class="member-thumb">
                                    @if($recrutement->image)
                                    <div class="col-lg-3"></div>
                                    <div class="col-lg-9">
                                    <img class="img-responsive" src="{{asset('/public/storage/recrutements/images/'.$recrutement->image)}}" alt="ManouPicture">
                                    </div>
                                    @else
                                    <div class="col-lg-3"></div>
                                    <div class="col-lg-9">
                                    <img class="img-responsive" src="{{asset('/public/dist-user/images/recrutement.png')}}" alt="ManouPicture">
                                    </div>
                                    @endif
                                    <div class="thumb-overlay">
                                        <a href="#"><span class="social-icon-fb"></span></a>
                                        <a href="#"><span class="social-icon-whatsapp"></span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="team-inner">
                                    <p style="font-size: 16px;" >{{$recrutement->description}}</p>
                            </div>
                            @if($recrutement->piece)
                            <div class="team-inner">
                                    <p style="font-size: 16px; color: black;">Télécharger le fichier joint <a target="_blank" href="{{asset('/public/storage/recrutements/pieces/'.$recrutement->piece)}}">ici</a></p>
                            </div>
                            @endif
                        </li>
                    </ul>
            </div>
        </div>

@endsection
