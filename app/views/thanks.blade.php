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
                        <p>Thanks {{ $theEmail }} for registering!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop