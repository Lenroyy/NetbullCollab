@extends('components.loginHeader')

@section('content')
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="form-horizontal form-material">
        @csrf
        <a href="javascript:void(0)" class="text-center db">
            <img src="{{ asset('assets/images/logo/X.png') }}" style="height: 100px;" alt="Home" /><br/>
            <img src="{{ asset('assets/images/logo/nextrack-inverted.png') }}" style="height: 100px;" alt="Home" />
        </a> 

        <div class="form-group m-t-40">
            <div class="col-xs-12">
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Username">

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group m-t-40">
            <div class="col-xs-12">
                <input placeholder="Password" id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-12">
                <div class="checkbox checkbox-primary pull-left p-t-0">
                    <input class="checkbox checkbox-primary pull-left p-t-0" type="checkbox" name="remember" id="remember_me" {{ old('remember') ? 'checked' : '' }}>

                    <label class="form-check-label" for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>
                    
                    @if (Route::has('password.request'))
                        <div class="pull-right">
                            <a class="text-dark pull-right" href="{{ route('password.request') }}">
                                <i class="fa fa-lock m-r-5"></i>
                                {{ __('Forgot Your Password?') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="form-group text-center m-t-20">
            <div class="col-xs-12">
                <button type="submit" class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light">
                    {{ __('Login') }}
                </button>
            </div>
        </div>
        <br>&nbsp;<br>&nbsp;
        <div class="form-group m-b-0">
            <div class="col-sm-12 text-center">
                <p>Don't have an account? <a href="/register" class="text-primary m-l-5"><b>Sign Up</b></a></p>
                <br><br>
                By logging into this software you are agreeing to the license agreement <a href="/assets/files/eula.pdf" target="_blank">shown here</a>
                <br><br>
                <a href="/assets/files/T&C.pdf">Terms and Conditions</a>
                <br><br>
                <a href="/assets/files/pdpp.pdf">Personal Data Protection Policy</a>

            </div>
        </div>
    </form>

    <!-- JS DEPENDENCIES -->
    @include('components.footer') 
    <!-- END JS DEPENDENCIES -->

@endsection
