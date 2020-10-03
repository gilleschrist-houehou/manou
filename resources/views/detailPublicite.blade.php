@extends('layouts.user')


@section('content')
           <div class="templatemo-team" id="templatemo-about">
            <div class="container">
                <div class="row">
                    <div class="templatemo-line-header">
                        <div class="text-center">
                            <span style="display: block; width: 100%;">{{$publicite->title}}</span>
                       </div>
                    </div>
                </div>
                <div class="clearfix"> </div>
                    <ul class="row row_team">
                        
                       
                       
                        <li class="col-lg-12 col-md-12 col-sm-12 ">
                            <div class="text-center">
                                <div class="member-thumb">
                                    @if($publicite->image)
                                    <div class="col-lg-3"></div>
                                    <div class="col-lg-9">
                                    <img class="img-responsive" src="{{asset('/public/storage/publicites/images/'.$publicite->image)}}" alt="ManouPicture">
                                    </div>
                                    @else
                                    <div class="col-lg-3"></div>
                                    <div class="col-lg-9">
                                    <img class="img-responsive" src="{{asset('/public/dist-user/images/publicite.png')}}" alt="ManouPicture">
                                    </div>
                                    @endif
                                    <div class="thumb-overlay">
                                        <a href="#"><span class="social-icon-fb"></span></a>
                                        <a href="#"><span class="social-icon-whatsapp"></span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="team-inner">
                                    <p style="font-size: 16px;" >{{$publicite->description}}</p>
                            </div>
                            @if($publicite->piece)
                            <div class="team-inner">
                                    <p style="font-size: 16px; color: black;">Télécharger le fichier joint <a target="_blank" href="{{asset('/public/storage/publicites/pieces/'.$publicite->piece)}}">ici</a></p>
                            </div>
                            @endif
                        </li>
                    </ul>
            </div>
        </div>

@endsection
