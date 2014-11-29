@extends('layouts.master')

@section('content')
@include('navbars.topnav', array('navBarPage'=>'users'))

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

@if(Auth::user()->can('read_users'))
    <div class="panel-group" id="accordion">
        @if(Auth::user()->can('create_groups'))
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseAdd">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add User
                            <small>Click to add a new user</small>
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
                        {{ Form::open(array('action' => array('UserController@postUser', true), 'class' => 'form-horizontal')) }}
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('email') != null ? 'has-error' : '' }}">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                                {{ Form::text('email', '', array('class'=>'form-control', 'placeholder' => 'email')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('username') != null ? 'has-error' : '' }}">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                {{ Form::text('username', '', array('class'=>'form-control', 'placeholder' => 'username')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('password') != null ? 'has-error' : '' }}">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                {{ Form::password('password', array('class'=>'form-control', 'placeholder' => 'password')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('password') != null ? 'has-error' : '' }} {{ isset($error) && $error->get('password_confirmation') != null ? 'has-error' : '' }}">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                {{ Form::password('password_confirmation', array('class'=>'form-control', 'placeholder' => 'confirm password')) }}
                            </div>
                            <div style="margin-top:10px" class="form-group">
                                <div class="col-md-12">
                                    {{ Form::submit('Create User', array('class'=>'btn btn-success')) }}
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        @endif
        @foreach(Toddish\Verify\Models\User::all() as $user)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $user->id }}">
                            {{ $user->username }}
                            <small>{{ $user->email }}</small>
                        </a>
                    </h4>
                </div>
                <div id="collapse{{ $user->id }}" class="panel-collapse collapse {{ Session::has('error'.$user->id) ? 'in' : '' }}">
                    <div class="panel-body">
                        @if(Session::has('error'.$user->id))
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        <ul>
                                            @foreach(Session::get('error'.$user->id)->all() as $errorMessage)
                                                <li>{{ $errorMessage  }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                        {{ Form::open(array('action' => array('UserController@putUser', $user->id), 'class' => 'form-horizontal', 'method' => 'PUT')) }}
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('error'.$user->id) && Session::get('error'.$user->id)->get('email') != null ? 'has-error' : '' }}">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                                {{ Form::text('email', $user->email, array('class'=>'form-control', 'placeholder' => 'email')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('error'.$user->id) && Session::get('error'.$user->id)->get('username') != null ? 'has-error' : '' }}">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                {{ Form::text('username', $user->username, array('class'=>'form-control', 'placeholder' => 'username')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('error'.$user->id) && Session::get('error'.$user->id)->get('group') != null ? 'has-error' : '' }}">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-heart"></i></span>
                                <select name='group' class="form-control">
                                    <option selected value="-1">Please select a group</option>
                                        @foreach(Toddish\Verify\Models\Role::all() as $role)
                                            @if(count(Toddish\Verify\Models\Role::find($user->roles()->getRelatedIds())->all()) > 0 &&
                                                $role->id == Toddish\Verify\Models\Role::find($user->roles()->getRelatedIds())->all()[0]->id)
                                                <option selected value="{{ $role->id }}">{{ $role->name }}</option>
                                            @else
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endif
                                        @endforeach
                                </select>
                            </div>
                            <div style="margin-bottom: 25px" class="input-group">
                                <div class="checkbox">
                                    <label>{{ Form::checkbox('disabled', 1, $user->disabled == true ? true : false, array('class'=>'')) }} Disabled Account</label>
                                </div>
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('error'.$user->id) && Session::get('error'.$user->id)->get('new password') != null ? 'has-error' : '' }}">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                    {{ Form::password('npassword', array('class'=>'form-control', 'placeholder' => 'new password')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('error'.$user->id) && Session::get('error'.$user->id)->get('new password') != null ? 'has-error' : '' }} {{ isset($error) && $error->get('new password_confirmation') != null ? 'has-error' : '' }}">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                    {{ Form::password('npassword_confirmation', array('class'=>'form-control', 'placeholder' => 'confirm new password')) }}
                            </div>
                            <div style="margin-top:10px" class="form-group">
                                <div class="col-md-12">
                                    {{ Form::submit('Save', array('class'=>'btn btn-primary')) }}
                                </div>
                            </div>
                        {{ Form::close() }}
                        <script>
                            function ConfirmDelete(){
                                return confirm("Are you sure you want to delete the user {{ $user->username }}?");
                            }
                        </script>
                        @if(Auth::user()->can('delete_user'))
                            {{ Form::open(array('action' => array('UserController@deleteUser', $user->id), 'class' => 'form-horizontal', 'method'=>'DELETE', 'onsubmit' => 'return ConfirmDelete()')) }}
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