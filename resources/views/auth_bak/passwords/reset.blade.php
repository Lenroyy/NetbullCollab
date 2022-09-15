@extends('layouts.app')

@section('content')

    <form method="POST" class="form-horizontal form-material" action="{{ route('password.update') }}">
        @csrf
        <a href="javascript:void(0)" class="text-center db">
            <img src="{{ asset('images/booka-logo-dark-bg.png') }}" alt="Home" /><br/>
            <img src="{{ asset('images/booka-text-dark.png') }}" alt="Home" />
        </a> 

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group m-t-40">
            <div class="col-xs-12">
                <input id="email" Placeholder="Email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group m-t-40">
            <div class="col-xs-12">
                <input id="password" Placeholder="Password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group m-t-40">
            <div class="col-xs-12">
                <input id="password-confirm" Placeholder="Confirm Password" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
            </div>
        </div>

        <div class="form-group m-t-40">
            <div class="col-xs-12">
                <button type="submit" class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light">
                    {{ __('Reset Password') }}
                </button>
            </div>
        </div>
    </form>

@endsection
