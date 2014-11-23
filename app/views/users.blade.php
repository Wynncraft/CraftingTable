@extends('layouts.master')

@section('content')
@include('navbars.topnav', array('navBarPage'=>'users'))

@if(Session::has('error'))
    <div class="alert alert-danger">
        <p>{{ Session::get('error') }}</p>
    </div>
@endif

@stop