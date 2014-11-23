@extends('layouts.master')

@section('content')
@include('navbars.topnav', array('navBarPage'=>'groups'))

<script>
$(document).ready(function() {
    $('.edit').click(function(event) {
        window.location.href = '{{ URL::to('/groups') }}/'+event.target.id;
    });
    $('.add').click(function() {
            window.location.href = '{{ URL::to('/groups/add') }}';
    });
});
</script>


@if(Session::has('error'))
    <div class="alert alert-danger">
        <p>{{ Session::get('error') }}</p>
    </div>
@endif

@if(Session::has('success'))
    <div class="alert alert-success">
        <p>{{ Session::get('success') }}</p>
    </div>
@endif

<div style="margin-bottom: 25px">
<button type="button" class="add btn btn-default btn-primary" aria-label="Plus">
<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> New Group
</button>
</div>

<table class="table table-striped table-bordered table-hover">
    <thread>
        <tr>
            <th>Group Name</th>
            <th>Description</th>
            <th>Edit</th>
        </tr>
    </thread>
    <tbody>
        @foreach(Toddish\Verify\Models\Role::all() as $role)
            <tr>
                <td>{{ $role->name }}</td>
                <td>{{ $role->description }}</td>
                <td><button id="{{ $role->id }}" type="button" class="edit btn btn-default btn-xs" aria-label="Pencil">
                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                </button></td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop