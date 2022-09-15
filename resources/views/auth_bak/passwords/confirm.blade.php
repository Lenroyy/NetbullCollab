@extends('layouts.app')

@section('content')
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <a href="javascript:void(0)" class="text-center db">
            <img src="{{ asset('images/booka-logo-dark-bg.png') }}" alt="Home" /><br/>
            <img src="{{ asset('images/booka-text-dark.png') }}" alt="Home" />
        </a> 

        <div class="form-group m-t-40">
            <div class="col-xs-12">
                {{ __('Confirm Password') }}
            </div>
        </div>

        <div class="form-group m-t-40">
            <div class="col-xs-12">
                {{ __('Please confirm your password before continuing.') }}
            </div>
        </div>

        

        <div class="form-group m-t-40">
            <div class="col-xs-12">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group m-t-40">
            <div class="col-xs-12">
                <button type="submit" class="btn btn-primary">
                    {{ __('Confirm Password') }}
                </button>

                @if (Route::has('password.request'))
                    <a class="btn btn-link" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                @endif
            </div>
        </div>
    </form>

@endsection
