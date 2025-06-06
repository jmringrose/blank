@extends('layouts.login')

@section('content')
    <div class="bg-overlay"></div>
    <div class="center-container">
        <div class="panel">
            <div class="bg"></div>
            <div class="bg-overlay"></div>
            <div class="content">
                <div class="form-group text-center">
                    <img src="{{ config('rcp.APP_URL') }}/img/jringrose300_.png" width="310" height="auto" alt="Logo">
                </div>
                <div class="form-group text-2xl font-semibold text-center">
                    <h2>James' Videos Login</h2>
                </div>
                <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}" class="rounded" autocomplete="on">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus placeholder="Email" autocomplete="email">
                         @if (isset($errors) && $errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input id="password" type="password" class="form-control" name="password" required placeholder="Password" autocomplete="current-password">
                         @if (isset($errors) && $errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="remember-me">
                        <label for="remember">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            {{ __('Remember me') }}
                        </label>
                    </div>

                    <div class="flash"></div>

                    <div class="form-group">
                        <div class="text-center mt-2">
                            <button type="submit" class="btn btn-accent">
                                {{ __('Login') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="w-full text-center mx-auto align-content-center justify-center text-gray-400">
            Copyright RCP Learning, Inc. 2011-2025
        </div>
    </div>
@endsection
