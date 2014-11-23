@extends('layouts.master')

@section('content')
    @include('navbars.topnav', array('navBarPage'=>'login'))
    <div class="row-fluid">
        <div class="row">
            <div style="margin-top:50px;" class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <div class="panel-title">Sign Up!</div>
                    </div>
                    <div style="padding-top:30px" class="panel-body">

                        @if(isset($error))
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach($error->all() as $errorMessage)
                                                <li>{{ $errorMessage  }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(isset($success))
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="alert alert-success">
                                    <p>{{ $success  }}</p>
                                    <p>Please <a href="{{ URL::to('login') }}">click here</a> to login</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{ Form::open(array('url '=> 'register', 'class' => 'form-horizontal')) }}

                                <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('email') != null ? 'has-error' : '' }}">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                                    {{ Form::text('email', '', array('class'=>'form-control', 'placeholder' => 'email')) }}
                                </div>

                                <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('username') != null ? 'has-error' : '' }}">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                    {{ Form::text('username', '', array('class'=>'form-control', 'placeholder' => 'username')) }}
                                </div>

                                <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('password') != null ? 'has-error' : '' }}">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                    {{ Form::password('password', array('class'=>'form-control', 'placeholder' => 'password')) }}
                                </div>

                                <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('password') != null ? 'has-error' : '' }} {{ isset($error) && $error->get('password_confirmation') != null ? 'has-error' : '' }}">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                    {{ Form::password('password_confirmation', array('class'=>'form-control', 'placeholder' => 'confirm password')) }}
                                </div>

                            <div style="margin-top:10px" class="form-group">
                                <div class="col-md-12">
                                    {{ Form::submit('Sign Up', array('class'=>'btn btn-success')) }}
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <div style="border-top: 1px solid#888; padding-top:15px; font-size:85%" >
                                        Already have an account?
                                        <a href="{{ URL::to('/login') }}">
                                            Sign In Here
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