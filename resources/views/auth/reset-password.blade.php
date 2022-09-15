@extends('components.loginHeader')

@section('content')

    <form method="POST" class="form-horizontal form-material" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <a href="javascript:void(0)" class="text-center db">
            <img src="{{ asset('assets/images/logo/X.png') }}" style="height: 100px;" alt="Home" /><br/>
            <img src="{{ asset('assets/images/logo/nextrack-inverted.png') }}" style="height: 100px;" alt="Home" />
        </a> 

        

        <div class="form-group m-t-40">
            <div class="col-xs-12">
                
                <x-jet-input id="email" class="form-control form-control-line" type="email" name="email" :value="old('email', $request->email)" required autofocus />

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group m-t-40">
            <div class="col-xs-12">
                <x-jet-input id="password" placeholder="New Password" class="form-control form-control-line" type="password" name="password" required autocomplete="new-password" />

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group m-t-40">
            <div class="col-xs-12">
                <x-jet-input id="password_confirmation" placeholder="Confirm New Password" class="form-control form-control-line" type="password" name="password_confirmation" required autocomplete="new-password" />
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
