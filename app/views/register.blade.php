@extends('layouts.master')

@section('content')
    @include('navbars.topnav', array('topNavPage'=>'login'))
    <div class="row-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3>Sign Up! <small>New user registration</small></h3>
                    </div>
                    <div class="panel-body">
                        {{ Form::open(array('url '=> 'register')) }}

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    {{ Form::label('email','Email Address') }}
                                    {{ Form::text('email', '', array('class'=>'form-control')) }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    {{ Form::label('username','Username') }}
                                    {{ Form::text('username', '', array('class'=>'form-control')) }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    {{ Form::label('password','Password') }}
                                    {{ Form::password('password', array('class'=>'form-control')) }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                {{ Form::submit('Sign Up', array('class'=>'btn btn-info btn-block')) }}

                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop