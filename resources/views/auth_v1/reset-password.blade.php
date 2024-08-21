@section('title', 'Register')
{{-- @extends('layouts.login_app') --}}

@extends('layouts.master_dashboard')

@section('content')



<h4 class="card-title mb-1">Reset Password 🔒</h4>

<form class="auth-reset-password-form mt-2" action="{{ route('resetPass') }}" method="POST">

    <div class="row">
        <div class="col-md-4 col-12">
            <div class="form-group">
                <label for="oldpassword">Old Password</label>
                <div class="input-group input-group-merge form-password-toggle">
                    <input type="password" class="form-control @error('old_password') is-invalid @enderror" id="old_password" name="old_password" placeholder="Enter old password" aria-describedby="password" tabindex="1" autofocus />
                    <div class="input-group-append">
                        <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                    </div>
                    @error('old_password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="col-md-4 col-12">
            <div class="form-group">
                <label for="password"> New password</label>
                <div class="input-group input-group-merge form-password-toggle">
                    <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="reset-password-new" name="new_password" placeholder="Enter new password" aria-describedby="password" tabindex="1" autofocus />
                    <div class="input-group-append">
                        <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                    </div>
                    @error('new_password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>


            </div>
        </div>

        <div class="col-md-4 col-12">
            <div class="form-group">
                <label for="phone_number">Confirm password</label>
                <div class="input-group input-group-merge form-password-toggle">
                    <input type="password" class="form-control @error('confirm_password') is-invalid @enderror" id="confirm_password" name="confirm_password" placeholder="Enter confirm password" aria-describedby="confirm_password" tabindex="1" autofocus />
                    <div class="input-group-append">
                        <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                    </div>
                    @error('confirm_password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                </div>

            </div>
        </div>


        <div class="col-12">
            <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">{{ isset($data->id)? 'Update':'Add' }}</button>
            <button type="reset" class="btn btn-outline-secondary waves-effect">Reset</button>
        </div>
    </div>
</form>

{{-- <p class="text-center mt-2">
    <a href="{{ route('sp-login') }}"> <i data-feather="chevron-left"></i> Back to login </a>
</p> --}}


@endsection
