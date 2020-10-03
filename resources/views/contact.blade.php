@extends('layouts.user')


@section('content')
<div class="templatemo-team" id="templatemo-about">
            <div class="container">
                <div class="row">
                    <div class="templatemo-line-header head_contact">
                        <div class="text-center">
                            <hr class="team_hr team_hr_left hr_gray"/><span class="txt_darkgrey">CONTACT US</span>
                            <hr class="team_hr team_hr_right hr_gray"/>
                        </div>
                    </div>

                    {{-- <div class="col-md-8">
                        <div class="templatemo-contact-map" id="map-canvas"> </div>  
                        <div class="clearfix"></div>
                        <i>You can find us on 80 Dagon Studio, Yankin Street, <span class="txt_orange">Digital Estate</span> in Yangon.</i>
                    </div> --}}
                    <div class="col-md-12 contact_right">
                        <p>Lorem ipsum dolor sit amet, consectetu adipiscing elit pendisse as a molesti.</p>
                        {{-- <p><img src="images/location.png" alt="icon 1" /> 80 Dagon Studio, Yakin Street, Digital Estate</p> --}}
                        <p><img src="{{ asset('/public/dist-user/images/phone1.png') }}"  alt="icon 2" /> +229 96 26 11 15</p>
                        <p><img src="{{ asset('/public/dist-user/images/globe.png') }}" alt="icon 3" /><a class="link_orange" href="#"><span class="txt_orange">www.manou.cf</span></a></p>
                        <form class="form-horizontal" action="#">
                            <div class="form-group">
                                <input type="email" class="form-control" placeholder="Votre nom" maxlength="40" />
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control" placeholder="Votre adresse email" maxlength="40" />
                            </div>
                            <div class="form-group">
                                <textarea  class="form-control" style="height: 130px;" placeholder="Votre message"></textarea>
                            </div>
                            <button type="submit" class="btn btn-orange pull-right">Envoyer</button>
                        </form>
                            
                    </div>
                </div><!-- /.row -->
            </div><!-- /.container -->
</div><!-- /#templatemo-contact -->
        

@endsection
