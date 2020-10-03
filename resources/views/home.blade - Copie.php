@extends('layouts.user')


@section('content')
<div class="site-section">
      <div class="container">
        {{-- <div class="row  mb-5">
          <div class="col-md-7">
            <h2 class="heading-39291">Blog and Updates</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto quos veniam magni totam, architecto earum dolor id obcaecati!</p>
            
          </div>
        </div> --}}
        <div class="row align-items-stretch">
          @if(count($annonces))
          @foreach($annonces as $an)
          <div class="col-lg-3 col-md-6 mb-5">
            <div class="post-entry-1 h-100">
              @if($an->image)
                <a href="#">                   
                  <img src="{{asset('/public/storage/annonces/images/'.$an->image)}}" alt="Image" class="img-fluid">
                </a>
              @else
              <a href="#">
                <img src="{{ asset('/public/dist-user/images/post_3.jpg') }} " alt="Image"
                 class="img-fluid">
              </a>
              @endif
              <div class="post-entry-1-contents">
                <span class="meta">{{ $an->title }}</span>
                <h2><a href="#">{{ substr($an->description,0,100) }}...</a></h2>
                <p class="my-3"><a href="#" class="more-39291">Lire plus</a></p>
              </div>
            </div>
          </div>
          @endforeach
          @else
          <div class="col-md-12" style="background: white; text-align: center;">
            <div>Aucune annonce disponible</div>
            
          </div>
          @endif
        </div>
      </div>
    </div>
@endsection
