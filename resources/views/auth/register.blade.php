@extends('layouts.app')
@section('title', __('Inscription'))
@section('content')
<div class="login-form">
                            <form action="" method="POST" action="{{ route('register') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="name">Nom</label>
                                    <input id="name" class="au-input au-input--full @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Nom">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="email">Adresse email</label>
                                    <input id="email" class="au-input au-input--full form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" type="email" placeholder="Adresse email">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="password">Mot de passe</label>
                                    <input id="password" class="au-input au-input--full form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" type="password" placeholder="Mot de passe">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="password-confirm">Confirmer mot de passe</label>
                                    <input id="password-confirm" class="au-input au-input--full form-control" name="password_confirmation" required autocomplete="new-password" type="password" placeholder="Confirmer mot de passe">
                                </div>
                                {{-- <div class="login-checkbox">
                                    <label>
                                        <input type="checkbox" name="aggree">Agree the terms and policy
                                    </label>
                                </div> --}}
                                <button class="au-btn au-btn--block au-btn--green m-b-20" type="submit">Inscription</button>
                            </form>
                            <div class="register-link">
                                <p>
                                    Vous avez déjà un compte?
                                    <a href="{{ route('login') }}">{{ __('Connectez-vous') }}</a>
                                </p>
                            </div>
</div>
@endsection
