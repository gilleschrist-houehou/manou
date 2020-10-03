@extends('layouts.user')


@section('content')
<div>
            <!-- Carousel -->
            <div id="templatemo-carousel" class="carousel slide" data-ride="carousel">
                <!-- Indicators -->
                <ol class="carousel-indicators">
                    <li data-target="#templatemo-carousel" data-slide-to="0" class="active"></li>
                    @if(count($publicites))
                        <?php
                        $i=0;
                        ?>
                    @foreach($publicites as $pu)
                        <?php
                        $i++;
                        ?>
                    <li data-target="#templatemo-carousel" data-slide-to="{{$i}}"></li>
                    @endforeach
                    @endif
                </ol>
                <div class="carousel-inner">
                    <div class="item active">
                            <div class="container">
                                <div class="carousel-caption">
                                  <h1>Bienvenue sur manou</h1>
                                    <p>Votre site de publication d'annonces et de recrutements</p>
                                    <h1>...</h1>
                                </div>
                            </div>
                        </div>
                        @if(count($publicites))
                        @foreach($publicites as $pu)
                        <div class="item">
                            <div class="container">
                                <div class="carousel-caption">
                                  <h1>{{$pu->title}}</h1>
                                    @if(strlen($pu->description)<=100)
                                    <p style="margin-bottom: 75px;">{{$pu->description}}</p>
                                    @else
                                    <p>{{ substr($pu->description,0,100) }}...</p>
                                    <p><a class="btn btn-lg btn-orange" href="{{route('detailPublicite',$pu)}}" role="button">Lire plus</a></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                </div>
                <a class="left carousel-control" href="#templatemo-carousel" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
                <a class="right carousel-control" href="#templatemo-carousel" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
            </div><!-- /#templatemo-carousel -->
        </div>

        {{-- <div class="templatemo-welcome" id="templatemo-welcome">
            <div class="container">
                
            </div>
        </div> --}}
        <div class="templatemo-service">
            <div class="container">
                <div class="row">
                  @if(count($recrutements))
                  @foreach($recrutements as $re)
                    <div class="col-md-3 col-sm-6">
                        <div class="card card-block">
                          @if($re->image)
                          <img class="img-responsive" src="{{asset('/public/storage/recrutements/images/'.$re->image)}}" alt="ManouPicture">
                          @else
                          <img class="img-responsive" src="{{asset('/public/dist-user/images/recrutement.png')}}" alt="ManouPicture">
                          @endif
                              <h5 class="card-title mt-3 mb-3">{{ $re->title }}</h5>
                              <p>{{ substr($re->description,0,100) }}...
                              </p>
                              <p class="text-center">
                                <a href="{{route('detailRecrutement',$re)}}" class="btn btn-orange">Lire plus</a>
                              </p>
                        </div>
                      </div>
                    @endforeach
                    @else
                    <div class="col-md-12" style="background: white; text-align: center;">
                      <div>Aucun recrutement disponible</div>
                    </div>
                    @endif
                </div>
                <div class="pull-right">
                    <div style="margin-top: 10px;"></div>
                    {{$recrutements->render()}}
                </div>
            </div>
        </div>

@endsection
