@extends('layouts.app')

@section('content')

    <form method="POST" class="form-horizontal form-material" action="{{ route('register') }}">
        @csrf
        <a href="javascript:void(0)" class="text-center db">
            <img src="{{ asset('images/booka-logo-dark-bg.png') }}" alt="Home" /><br/>
            <img src="{{ asset('images/booka-text-dark.png') }}" alt="Home" />
        </a> 

        <div class="form-group m-t-40">
            <div class="col-xs-12">
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Name" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group m-t-40">
            <div class="col-xs-12">

                <input id="email" type="email" placeholder="Email address" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group m-t-40">
            <div class="col-xs-12">
                <input id="password" type="password" placeholder="Password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

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
                    {{ __('Register') }}
                </button>
            </div>
        </div>
    </form>
@endsection
