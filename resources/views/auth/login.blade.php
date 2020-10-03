@extends('layouts.app')
@section('title', __('Connexion'))
@section('content')
<div class="login-form">
                            <form action="" method="post" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <label>Adresse email</label>
                                    <input class="au-input au-input--full form-control @error('email') is-invalid @enderror" type="email" name="email" placeholder="Adresse email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Mot de passe</label>
                                    <input class="au-input au-input--full form-control @error('password') is-invalid @enderror" required autocomplete="current-password" type="password" name="password" placeholder="Mot de passe">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="login-checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>Se souvenir de moi
                                    </label>
                                    <label>
                                        @if (Route::has('password.request'))
                                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                                {{ __('Mot de passe oublié?') }}
                                            </a>
                                        @endif
                                        {{-- <a href="#">Forgotten Password?</a> --}}
                                    </label>
                                </div>
                                <button class="au-btn au-btn--block au-btn--green m-b-20" type="submit">Connexion</button>
                                {{-- <div class="social-login-content">
                                    <div class="social-button">
                                        <button class="au-btn au-btn--block au-btn--blue m-b-20">sign in with facebook</button>
                                        <button class="au-btn au-btn--block au-btn--blue2">sign in with twitter</button>
                                    </div>
                                </div> --}}
                            </form>
                            <div class="register-link">
                                <p>
                                    Vous n'avez pas un compte?
                                    <a href="{{ route('register') }}">{{ __('Créer un ici') }}</a>
                                    {{-- <a href="#">Créer un ici</a> --}}
                                </p>
                            </div>
</div>
@endsection