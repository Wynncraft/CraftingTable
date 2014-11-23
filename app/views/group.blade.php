@extends('layouts.master')

@section('content')
@include('navbars.topnav', array('navBarPage'=>'groups'))

<script>
$(document).ready(function() {
    $('.perm').click(function(event) {
        var hidden = $('#'+event.target.id);
        if (hidden.val() == "false") {
            $(this).text('Allow');
            $(this).removeClass('btn-danger');
            $(this).addClass('btn-success');
            hidden.val("true");
        } else {
            $(this).text('Deny');
            $(this).removeClass('btn-success');
            $(this).addClass('btn-danger');
            hidden.val("false");
        }
    });
});
</script>

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

{{ Form::open(array('url '=> $role->exits == true ? 'group/'.$role->id : 'group/add', 'class' => 'form-horizontal', 'method' => $role->exits == true ? 'put' : 'post')) }}

    <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('name') != null ? 'has-error' : '' }}">
            {{ Form::label('name', 'Group Name', array('class'=>'input-control','for'=>'name')) }}
            {{ Form::text('name', $role->name, array('class'=>'form-control', 'placeholder' => 'group name', 'id' => 'name')) }}
    </div>

    <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('description') != null ? 'has-error' : '' }}">
                {{ Form::label('description', 'Group Description', array('class'=>'input-control','for'=>'description')) }}
                {{ Form::text('name', $role->description, array('class'=>'form-control', 'placeholder' => 'group name', 'id' => 'description', 'maxlength'=> '255')) }}
    </div>

    {{ Form::label('permission_matrix', 'Permission Matrix', array('class'=>'input-control')) }}
    <table class="table table-striped table-bordered table-hover">
        <thread>
            <tr>
                <th>Permissions</th>
                <th>Create</th>
                <th>Read</th>
                <th>Update</th>
                <th>Delete</th>
            </tr>
        </thread>
        <tbody>
            <tr>
                <td>User</td>
                {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'create_user')) /*--}}
                {{ Form::hidden($perm->name, $role->has($perm) ? 'true' : 'false', array('id' => $perm->name)) }}
                <td><button id="{{$perm->name}}" type="button" class="btn perm {{ $role->has($perm) ? 'btn-success' : 'btn-danger' }}">{{ $role->has($perm) ? 'Allow' : 'Deny' }}</button></td>
                {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'read_user')) /*--}}
                {{ Form::hidden($perm->name, $role->has($perm) ? 'true' : 'false', array('id' => $perm->name)) }}
                <td><button id="{{$perm->name}}" type="button" class="btn perm {{ $role->has($perm) ? 'btn-success' : 'btn-danger' }}">{{ $role->has($perm) ? 'Allow' : 'Deny' }}</button></td>
                {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'update_user')) /*--}}
                {{ Form::hidden($perm->name, $role->has($perm) ? 'true' : 'false', array('id' => $perm->name)) }}
                <td><button id="{{$perm->name}}" type="button" class="btn perm {{ $role->has($perm) ? 'btn-success' : 'btn-danger' }}">{{ $role->has($perm) ? 'Allow' : 'Deny' }}</button></td>
                {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'delete_user')) /*--}}
                {{ Form::hidden($perm->name, $role->has($perm) ? 'true' : 'false', array('id' => $perm->name)) }}
                <td><button id="{{$perm->name}}" type="button" class="btn perm {{ $role->has($perm) ? 'btn-success' : 'btn-danger' }}">{{ $role->has($perm) ? 'Allow' : 'Deny' }}</button></td>
            </tr>
        </tbody>
    </table>

{{ Form::close() }}

@stop