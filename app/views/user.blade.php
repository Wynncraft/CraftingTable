@extends('layouts.master')

@section('content')
    @include('navbars.topnav', array('navBarPage'=>'login'))
    <div class="row-fluid">
        <div class="row">
            <div style="margin-top:50px;" class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <div class="panel-title">Edit Account</div>
                    </div>
                    <div style="padding-top:30px" class="panel-body">

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
                                    <p>{{ $success  }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{ Form::open(array('action' => array('UserController@putUser', $user->id, true), 'class' => 'form-horizontal', 'method' => 'PUT')) }}

                                <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('email') != null ? 'has-error' : '' }}">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                                    {{ Form::text('email', $user->email, array('class'=>'form-control', 'placeholder' => 'email')) }}
                                </div>

                                <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('username') != null ? 'has-error' : '' }}">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                    {{ Form::text('username', $user->username, array('class'=>'form-control', 'placeholder' => 'username', 'disabled')) }}
                                </div>

                                <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('group') != null ? 'has-error' : '' }}">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-heart"></i></span>
                                    <select name='group' class="form-control" disabled>
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

                                <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('disabled') != null ? 'has-error' : '' }}">
                                    <div class="checkbox">
                                        <label>{{ Form::checkbox('disabled', 1, $user->disabled == true ? true : false, array('class'=>'', 'disabled')) }} Disabled Account</label>
                                    </div>
                                </div>

                                <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('new password') != null ? 'has-error' : '' }}">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                    {{ Form::password('npassword', array('class'=>'form-control', 'placeholder' => 'new password')) }}
                                </div>

                                <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('new password') != null ? 'has-error' : '' }} {{ isset($error) && $error->get('new password_confirmation') != null ? 'has-error' : '' }}">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                    {{ Form::password('npassword_confirmation', array('class'=>'form-control', 'placeholder' => 'confirm new password')) }}
                                </div>

                                @if(Auth::user()->id == $user->id)
                                    <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('password') != null ? 'has-error' : '' }}">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                        {{ Form::password('password', array('class'=>'form-control', 'placeholder' => 'current password')) }}
                                    </div>
                                @endif

                            <div style="margin-top:10px" class="form-group">
                                <div class="col-md-12">
                                    {{ Form::submit('Save', array('class'=>'btn btn-primary')) }}
                                </div>
                            </div>
                            {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop