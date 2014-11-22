@extends('layouts.master')

@section('content')
    @include('navbars.topnav', array('topNavPage'=>'login'))
    <div class="row-fluid">
            <div class="row">
                <div style="margin-top:50px;" class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="panel-title">
                                Sign In
                            </div>
                            <div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="#">Forgot password?</a></div>
                        </div>
                        <div style="padding-top:30px" class="panel-body">

                            @if(isset($error))
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="alert alert-danger">
                                            <p>{{ $error  }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(isset($success))
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="alert alert-success">
                                        <p>{{ $success  }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{ Form::open(array('url '=> 'login', 'class' => 'form-horizontal')) }}

                                    <div style="margin-bottom: 25px" class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                                        {{ Form::text('email', '', array('class'=>'form-control', 'placeholder' => 'email')) }}
                                    </div>

                                    <div style="margin-bottom: 25px" class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                        {{ Form::password('password', array('class'=>'form-control', 'placeholder' => 'password')) }}
                                    </div>

                            <div style="margin-top:10px" class="form-group">
                                <div class="col-md-12">
                                    {{ Form::submit('Login', array('class'=>'btn btn-success')) }}

                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <div style="border-top: 1px solid#888; padding-top:15px; font-size:85%" >
                                        Don't have an account!
                                        <a href="{{ URL::to('/register') }}">
                                            Sign Up Here
                                        </a>
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}

                        </div>
                    </div>
                </div>
            </div>
        </div>
@stop