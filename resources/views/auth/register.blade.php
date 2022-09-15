@extends('components.loginHeader')

@section('content')

    <form method="POST" class="form-horizontal form-material" action="{{ route('register') }}">
        @csrf
        <a href="javascript:void(0)" class="text-center db">
            <img src="{{ asset('assets/images/logo/X.png') }}" style="height: 100px;" alt="Home" /><br/>
            <img src="{{ asset('assets/images/logo/nextrack-inverted.png') }}" style="height: 100px;" alt="Home" />
        </a> 

        <div class="form-group m-t-40" style="overflow: auto;">
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
                <label>What best describes you?</label>
                <select class="form-control form-control-line" name="founder" onChange="checkEntity(this.value)">
                    <option value="worker">Worker</option>
                    <option value="contractor">Start New Contractor</option>
                    <option value="builder">Start New Builder</option>
                    <option value="hygenist">Start New Hygienist</option>
                    <option value="provider">Start New Service Provider</option>
                </select>                
            </div>
            <br>&nbsp;
            <div class="col-xs-12">
                <input Placeholder="Join organisation with code (optional)" type="text" class="form-control" id="request" {id!="request"? : required } name="membership_request" @if(isset($_GET['code'])) value="{{ $_GET['code'] }}" @endif)>
                <input type="hidden" id="other" name="business_name" >
            </div>
            <!--
            <div class="col-xs-12" id="entityName" style="visibility: hidden; position: float;">
                <input Placeholder="Name of business" type="text" class="form-control" name="business_name">
            </div>
            -->
        </div>

        <div class="form-group m-t-40">
            <div class="col-xs-12">
                {{-- {!! NoCaptcha::renderJs() !!}
                {!! NoCaptcha::display() !!} --}}

        
                @if ($errors->has('g-recaptcha-response'))
                    <span class="help-block">
                        <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                    </span>
                @endif
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

    <script>
        function checkEntity(value)
        {
            div = document.getElementById("request")
            other = document.getElementById("other")

            if(value == "worker")
            {
                div.placeholder = "Join organisation (optional)"
                div.name = "membership_request"
                other.name = "business_name"
            }
            else
            {
                div.placeholder = "Name of business"
                div.name = "business_name"
                other.name = "membership_request"
            }
            
        }
    </script>
@endsection
