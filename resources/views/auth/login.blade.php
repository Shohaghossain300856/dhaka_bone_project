@extends('layouts.app')
@section('content')
    <!-- Login -->
    <div class="card">
        <div class="card-body">
            <!-- Logo -->
            <div class="app-brand justify-content-center mb-4 mt-2">
                <a href="{{route('home')}}" class="app-brand-link gap-2">
                    <span class="app-brand-text demo text-body fw-bold ms-1">
                        <img style="width: 100px;" src="https://upload.wikimedia.org/wikipedia/bn/thumb/8/82/%E0%A6%AE%E0%A6%AE%E0%A7%87%E0%A6%95_%E0%A6%B2%E0%A7%8B%E0%A6%97%E0%A7%8B.png/250px-%E0%A6%AE%E0%A6%AE%E0%A7%87%E0%A6%95_%E0%A6%B2%E0%A7%8B%E0%A6%97%E0%A7%8B.png">
                    </span>
                </a>
            </div>
            <!-- /Logo -->
            <h4 class="mb-1 pt-2">Mymensingh Medical College</h4>
            <p class="mb-4">Log in to your account and get started</p>
            <form id="formAuthentication" class="mb-3" action="{{route('login')}}" method="post">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter your email" autofocus/>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3 form-password-toggle">
                    <div class="d-flex justify-content-between">
                        <label class="form-label" for="password">Password</label>
                        <a href="{{route('password.request')}}">
                            <small>Forgot Password?</small>
                        </a>
                    </div>
                    <div class="input-group input-group-merge">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password"/>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember-me" {{ old('remember') ? 'checked' : '' }}/>
                        <label class="form-check-label" for="remember-me"> Remember Me </label>
                    </div>
                </div>
                <div class="mb-3">
                    <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                </div>
            </form>
            <p class="text-center">
                <span>New on our platform?</span>
                <a href="{{route('register')}}">
                    <span>Create an account</span>
                </a>
            </p>
<!-- 
            @if(env('APP_ENV') == "local")
                <button type="button" id="superAdmin" class="btn btn-outline-primary waves-effect">Super Admin</button>
                <button type="button" id="admin" class="btn btn-outline-success waves-effect">Admin</button>
                <button type="button" id="user" class="btn btn-outline-info waves-effect">User</button>
                {{-- <button type="button" class="btn btn-outline-danger waves-effect">User Status Deactivated</button> --}}
            
            @endif -->

        </div>
    </div>
    <!-- /Register -->

@endsection
@push('script')
    <script>
        $('#superAdmin').on('click',function() {
            $('#email').val('superadmin@gmail.com');
            $('#password').val('12345678');
        });
        $('#admin').on('click',function() {
            $('#email').val('admin@gmail.com');
            $('#password').val('12345678');
        });
        $('#user').on('click',function() {
            $('#email').val('user@gmail.com');
            $('#password').val('12345678');
        });
    </script>
@endpush
