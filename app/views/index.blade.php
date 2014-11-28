@extends('layouts.master')

@section('content')
@include('navbars.topnav', array('navBarPage'=>'home'))

<script>
$(document).ready(function() {
    $('.edit').click(function(event) {
        window.location.href = '{{ URL::to('/networks') }}/'+event.target.id;
    });
    $('.add').click(function() {
            window.location.href = '{{ URL::to('/networks/add') }}';
    });
});
</script>

@if(Session::has('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <p>{{ Session::get('error') }}</p>
    </div>
@endif

@if(Session::has('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <p>{{ Session::get('success') }}</p>
    </div>
@endif

@if(Auth::user()->can('create_network'))
    <div style="margin-bottom: 25px">
        <button type="button" class="add btn btn-default btn-primary" aria-label="Plus">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> New Network
        </button>
    </div>
@endif

@if(Auth::user()->can('read_network'))
    <table class="table table-striped table-bordered table-hover">
        <thread>
            <tr>
                <th>Network Name</th>
                <th>Description</th>
                <th>Edit</th>
            </tr>
        </thread>
        <tbody>
            @foreach(Network::all() as $network)
                <tr>
                    <td>{{ $network->name }}</td>
                    <td>{{ $network->description }}</td>
                    <td><button id="{{ $network->id }}" type="button" class="edit btn btn-default btn-xs" aria-label="Pencil">
                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                    </button></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@stop