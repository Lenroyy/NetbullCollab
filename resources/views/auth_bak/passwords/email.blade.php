@extends('layouts.app')

@section('content')
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" class="form-horizontal form-material" action="{{ route('password.email') }}">
        @csrf
        <a href="javascript:void(0)" class="text-center db">
            <img src="{{ asset('images/booka-logo-dark-bg.png') }}" alt="Home" /><br/>
            <img src="{{ asset('images/booka-text-dark.png') }}" alt="Home" />
        </a> 

        <div class="form-group m-t-40">
            <div class="col-xs-12">
                <input id="email" Placeholder="Email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group m-t-40">
            <div class="col-xs-12">
                <button type="submit" class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light">
                    {{ __('Send Password Reset Link') }}
                </button>
            </div>
        </div>
    </form>
@endsection
