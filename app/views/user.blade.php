@extends('layouts.master')

@section('content')
    @include('navbars.topnav', array('topNavPage'=>'login'))
    <div class="row-fluid">
        <div class="row">
            <div style="margin-top:50px;" class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <div class="panel-title">Edit Account</div>
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
                                </div>
                            </div>
                        </div>
                        @endif

                        {{ Form::open(array('url '=> 'register', 'class' => 'form-horizontal', 'method' => 'put')) }}

                                <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('email') != null ? 'has-error' : '' }}">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                                    {{ Form::text('email', $user->email, array('class'=>'form-control', 'placeholder' => 'email')) }}
                                </div>

                                <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('username') != null ? 'has-error' : '' }}">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                    {{ Form::text('username', $user->username, array('class'=>'form-control', 'placeholder' => 'username', 'disabled')) }}
                                </div>

                                <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('npassword') != null ? 'has-error' : '' }}">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                    {{ Form::password('npassword', array('class'=>'form-control', 'placeholder' => 'new password')) }}
                                </div>

                                <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('password') != null ? 'has-error' : '' }}">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                    {{ Form::password('password', array('class'=>'form-control', 'placeholder' => 'current password')) }}
                                </div>

                            <div style="margin-top:10px" class="form-group">
                                <div class="col-md-12">
                                    {{ Form::submit('Sign Up', array('class'=>'btn btn-success')) }}
                                </div>
                            </div>
                            {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop