@extends('layouts.master')

@section('content')
    @include('navbars.topnav', array('topNavPage'=>'logout'))
    <div class="row-fluid">
            <div class="row">
                <div style="margin-top:50px;" class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="panel-title">Logout</div>
                        </div>
                        <div style="padding-top:30px" class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <p>You are now signed out of Minestack. <a href="{{ URL::to('/login') }}">Sign In?</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@stop