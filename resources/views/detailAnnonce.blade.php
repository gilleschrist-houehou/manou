@extends('layouts.user')


@section('content')
           <div class="templatemo-team" id="templatemo-about">
            <div class="container">
                <div class="row">
                    <div class="templatemo-line-header">
                        <div class="text-center">
                            {{-- <hr class="team_hr team_hr_left "/> --}}
                            <span style="display: block; width: 100%;">{{$annonce->title}}</span>
                            {{-- <hr class="team_hr team_hr_right" /> --}}
                        </div>
                    </div>
                </div>
                <div class="clearfix"> </div>
                    <ul class="row row_team">
                        
                       
                       
                        <li class="col-lg-12 col-md-12 col-sm-12 ">
                            <div class="text-center">
                                <div class="member-thumb">
                                    @if($annonce->image)
                                    <div class="col-lg-3"></div>
                                    <div class="col-lg-9">
                                    <img class="img-responsive" src="{{asset('/public/storage/annonces/images/'.$annonce->image)}}" alt="ManouPicture">
                                    </div>
                                    @else
                                    <div class="col-lg-3"></div>
                                    <div class="col-lg-9">
                                    <img class="img-responsive" src="{{asset('/public/dist-user/images/annonce.png')}}" alt="ManouPicture">
                                    </div>
                                    @endif
                                    <div class="thumb-overlay">
                                       {{--  <a href="#"><span class="social-icon-twitter"></span></a> --}}
                                       <a class="social-icon-fb" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(route('detailAnnonce',$annonce)); ?>" target="_blank"></a>

                                        <a href="#" data-text="<?php echo $annonce->image ?>"  data-link="{{route('detailAnnonce',$annonce)}}"><span class="social-icon-whatsapp"></span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="team-inner">
                                    <p style="font-size: 16px;" >{{$annonce->description}}</p>
                            </div>
                            @if($annonce->piece)
                            <div class="team-inner">
                                    <p style="font-size: 16px; color: black;">Télécharger le fichier joint <a style="color: white; background: #ff7600; padding: 2px; !important;" target="_blank" href="{{asset('/public/storage/annonces/pieces/'.$annonce->piece)}}">ici</a></p>
                            </div>
                            @endif
                        </li>
                    </ul>
            </div>
        </div>

@endsection
