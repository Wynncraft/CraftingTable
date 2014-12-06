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

@if(Auth::user()->can('read_group'))
    <div class="panel-group" id="accordion">
        @if(Auth::user()->can('create_groups'))
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseAdd">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Group
                            <small>Click to add a new group</small>
                        </a>
                    </h4>
                </div>
                <div id="collapseAdd" class="panel-collapse collapse {{ Session::has('errorAdd') ? 'in' : '' }}">
                    <div class="panel-body">
                        @if(Session::has('errorAdd'))
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            <ul>
                                                @foreach(Session::get('errorAdd')->all() as $errorMessage)
                                                    <li>{{ $errorMessage  }}</li>
                                                @endforeach
                                            </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                        {{ Form::open(array('action' => 'GroupController@postGroup', 'class' => 'form-horizontal')) }}
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('name') != null ? 'has-error' : '' }}">
                                {{ Form::label('name-label', 'Group Name') }}
                                {{ Form::text('name', '', array('class'=>'form-control', 'placeholder' => 'i.e My Group', 'maxlength' => '100')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('description') != null ? 'has-error' : '' }}">
                                {{ Form::label('description-label', 'Group Description') }}
                                {{ Form::text('description', '', array('class'=>'form-control', 'placeholder' => 'i.e This is my group', 'maxlength' => '255')) }}
                            </div>
                            <div style="margin-top:10px" class="form-group">
                                <div class="col-md-12">
                                    {{ Form::submit('Add Group', array('class'=>'btn btn-success')) }}
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        @endif
        @foreach(Toddish\Verify\Models\Role::all() as $role)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $role->id }}">
                            {{{ $role->name }}}
                            <small>{{{ $role->description }}}</small>
                        </a>
                    </h4>
                </div>
                <div id="collapse{{ $role->id }}" class="panel-collapse collapse {{ Session::has('open'.$role->id) ? 'in' : '' }}">
                    <div class="panel-body">
                        @if(Session::has('error'.$role->id))
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            <ul>
                                                @foreach(Session::get('error'.$role->id)->all() as $errorMessage)
                                                    <li>{{ $errorMessage  }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                </div>
                            </div>
                        @endif
                        {{ Form::open(array('action' => array('GroupController@putGroup', $role->id), 'class' => 'form-horizontal', 'method'=>$role->exists == true ? 'PUT' : 'POST')) }}
                            <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('name') != null ? 'has-error' : '' }}">
                                {{ Form::label('name-label', 'Group Name', array('class'=>'input-control','for'=>'name')) }}
                                {{ Form::text('name', $role->name, array('class'=>'form-control', 'placeholder' => 'i.e My Group', 'id' => 'name')) }}
                            </div>

                            <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('description') != null ? 'has-error' : '' }}">
                                {{ Form::label('description-label', 'Group Description', array('class'=>'input-control','for'=>'description')) }}
                                {{{ Form::text('description', $role->description, array('class'=>'form-control', 'placeholder' => 'i.e This is my group', 'id' => 'description', 'maxlength'=> '255')) }}}
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
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'read_user')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'update_user')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'delete_user')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                    </tr>
                                    <tr>
                                        <td>Group</td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'create_group')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{$hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'read_group')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'update_group')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'delete_group')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                    </tr>
                                    <tr>
                                        <td>Network</td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'create_network')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'read_network')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'update_network')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'delete_network')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                    </tr>
                                    <tr>
                                        <td>Node</td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'create_node')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'read_node')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'update_node')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'delete_node')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                    </tr>
                                    <tr>
                                        <td>Server Type</td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'create_servertype')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'read_servertype')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'update_servertype')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'delete_servertype')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                    </tr>
                                    <tr>
                                        <td>World</td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'create_world')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'read_world')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'update_world')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'delete_world')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                    </tr>
                                    <tr>
                                        <td>Plugin</td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'create_plugin')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'read_plugin')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'update_plugin')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                        {{--*/ $perm = Toddish\Verify\Models\Permission::firstOrNew(array('name'=>'delete_plugin')) /*--}}
                                        {{--*/ $hasPerm = ($role->permissions()->where('name', '=', $perm->name)->first() == null) ? false : true /*--}}
                                        {{ Form::hidden('PERM:'.$perm->name, $hasPerm ? 'true' : 'false', array('id' => $role->id.$perm->name)) }}
                                        <td><button id="{{$role->id.$perm->name}}" type="button" class="btn perm {{ $hasPerm ? 'btn-success' : 'btn-danger' }}">{{ $hasPerm ? 'Allow' : 'Deny' }}</button></td>
                                    </tr>
                                </tbody>
                            </table>

                            @if(Auth::user()->can('update_group'))
                                <div style="margin-top:10px" class="form-group">
                                    <div class="col-md-12">
                                        {{ Form::submit('Save Group', array('class'=>'btn btn-primary')) }}
                                    </div>
                                </div>
                            @endif

                        {{ Form::close() }}
                        <script>
                            function ConfirmDelete(){
                                return confirm("Are you sure you want to delete the group {{{ $role->name }}}?");
                            }
                        </script>
                        @if(Auth::user()->can('delete_group'))
                            {{ Form::open(array('action' => array('GroupController@deleteGroup', $role->id), 'class' => 'form-horizontal', 'method'=>'DELETE', 'onsubmit' => 'return ConfirmDelete()')) }}
                                <div style="margin-top:10px" class="form-group">
                                    <div class="col-md-12">
                                        {{ Form::submit('Delete', array('class'=>'btn btn-danger')) }}
                                    </div>
                                </div>
                            {{ Form::close() }}
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@stop