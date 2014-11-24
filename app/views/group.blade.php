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
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
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
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <p>{{ $success }}</p>
            </div>
        </div>
    </div>
@endif

{{ Form::open(array('class' => 'form-horizontal', 'method'=>$role->exists == true ? 'PUT' : 'POST')) }}
    <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('name') != null ? 'has-error' : '' }}">
        {{ Form::label('name', 'Group Name', array('class'=>'input-control','for'=>'name')) }}
        {{ Form::text('name', $role->name, array('class'=>'form-control', 'placeholder' => 'group name', 'id' => 'name')) }}
    </div>

    <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('description') != null ? 'has-error' : '' }}">
        {{ Form::label('description', 'Group Description', array('class'=>'input-control','for'=>'description')) }}
        {{ Form::text('description', $role->description, array('class'=>'form-control', 'placeholder' => 'group name', 'id' => 'description', 'maxlength'=> '255')) }}
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
                {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $perm->name)) }}
                <td><button id="{{$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'read_user')) /*--}}
                {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $perm->name)) }}
                <td><button id="{{$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'update_user')) /*--}}
                {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $perm->name)) }}
                <td><button id="{{$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'delete_user')) /*--}}
                {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $perm->name)) }}
                <td><button id="{{$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
            </tr>
            <tr>
                <td>Group</td>
                {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'create_group')) /*--}}
                {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $perm->name)) }}
                <td><button id="{{$perm->name}}" type="button" class="btn perm {{$hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'read_group')) /*--}}
                {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $perm->name)) }}
                <td><button id="{{$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'update_group')) /*--}}
                {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $perm->name)) }}
                <td><button id="{{$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'delete_group')) /*--}}
                {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $perm->name)) }}
                <td><button id="{{$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
            </tr>
            <tr>
                <td>Network</td>
                {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'create_network')) /*--}}
                {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $perm->name)) }}
                <td><button id="{{$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'read_network')) /*--}}
                {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $perm->name)) }}
                <td><button id="{{$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'update_network')) /*--}}
                {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $perm->name)) }}
                <td><button id="{{$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'delete_network')) /*--}}
                {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $perm->name)) }}
                <td><button id="{{$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top:10px" class="form-group">
        <div class="col-md-12">
            {{ Form::submit('Save', array('class'=>'btn btn-primary')) }}
        </div>
    </div>

{{ Form::close() }}


@if($role->exists == true)
    {{ Form::open(array('class' => 'form-horizontal', 'method'=>'DELETE')) }}
        <div style="margin-top:10px" class="form-group">
            <div class="col-md-12">
                {{ Form::submit('Delete', array('class'=>'btn btn-danger')) }}
            </div>
        </div>
    {{ Form::close() }}
@endif


@stop