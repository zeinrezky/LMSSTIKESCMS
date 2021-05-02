@extends('adminlte::master')

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    @yield('css')
@stop

@section('body_class', 'login-page')

@section('body')
    <div class="login-box">
        <!-- <div class="login-logo">
            <a href="{{ url(config('adminlte.dashboard_url', 'home')) }}">
                {-- config('adminlte.logo', '<b>Admin</b>LTE') --}
            </a>
        </div> -->
        <!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg pb-0">
                <img src="{{ asset('logo.png') }}" width="250px" class="mb-0">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h4>CONTENT MANAGEMENT SYSTEM (CMS)</h4>
                    </div>
                </div>
                
                @if(__('adminlte::adminlte.login_message') != 'Sign in to start your session')
                    {{ __('adminlte::adminlte.login_message') }}
                @endif

                @if(Session::has('message'))
                    <div class="alert alert-{{ Session::get('type') }}">
                        <div class="alert-body">
                            <button class="close" data-dismiss="alert">
                            <span>&times;</span>
                            </button>
                            @if(Session::get('type') == 'success')
                                <div class="alert-title">Success !</div>
                            @elseif(Session::get('type') == 'warning')
                                <div class="alert-title">Warning !</div>
                            @else
                                <div class="alert-title">Failed !</div>
                            @endif
                            {{ Session::get('message') }}
                        </div>
                    </div>
                @endif
            </p>
            <form action="{{ url(config('adminlte.login_url', 'login')) }}" method="post">
                {{ csrf_field() }}

                <div class="form-group has-feedback {{ $errors->has('nip') ? 'has-error' : '' }}">
                    <input type="nip" name="nip" class="form-control" value="{{ old('nip') }}"
                           placeholder="NIP">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    @if ($errors->has('nip'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nip') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group has-feedback {{ $errors->has('password') ? 'has-error' : '' }}">
                    <input type="password" name="password" class="form-control"
                           placeholder="{{ __('adminlte::adminlte.password') }}">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="icheck-primary">
                            <input type="checkbox" name="remember" id="remember">
                            <label for="remember">{{ __('adminlte::adminlte.remember_me') }}</label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">
                            {{ __('adminlte::adminlte.sign_in') }}
                        </button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
            <br>
            <p>
                <a href="{{ url(config('adminlte.password_reset_url', 'password/reset')) }}" class="text-center">
                    {{ __('adminlte::adminlte.i_forgot_my_password') }}
                </a>
            </p>
            <?php /*
            @if (config('adminlte.register_url', 'register'))
                <p>
                    <a href="{{ url(config('adminlte.register_url', 'register')) }}" class="text-center">
                        {{ __('adminlte::adminlte.register_a_new_membership') }}
                    </a>
                </p>
            @endif
            */
            ?>
        </div>
        <!-- /.login-box-body -->
    </div><!-- /.login-box -->
@stop

@section('adminlte_js')
    @yield('js')
@stop
