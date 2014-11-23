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

                        @if(isset($success))
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="alert alert-success">
                                    <p>{{ $success  }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{ Form::open(array('url '=> 'user/'.$user->id, 'class' => 'form-horizontal', 'method' => 'put')) }}

                                <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('email') != null ? 'has-error' : '' }}">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                                    {{ Form::text('email', $user->email, array('class'=>'form-control', 'placeholder' => 'email')) }}
                                </div>

                                <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('username') != null ? 'has-error' : '' }}">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                    {{ Form::text('username', $user->username, array('class'=>'form-control', 'placeholder' => 'username', Auth::user()->id == $user->id ? 'disabled' : 'enabled')) }}
                                </div>

                                <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('group') != null ? 'has-error' : '' }}">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-heart"></i></span>
                                    <select name='group' class="form-control" {{ Auth::user()->id == $user->id ? 'disabled' : 'enabled' }}>
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
                                        <label>{{ Form::checkbox('disabled', 1, $user->disabled == true ? true : false, array('class'=>'', Auth::user()->id == $user->id ? 'disabled' : 'enabled')) }} Disabled Account</label>
                                    </div>
                                </div>

                                <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('npassword') != null ? 'has-error' : '' }}">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                    {{ Form::password('npassword', array('class'=>'form-control', 'placeholder' => 'new password')) }}
                                </div>

                                <div style="margin-bottom: 25px" class="input-group {{ isset($error) && $error->get('npassword') != null ? 'has-error' : '' }} {{ isset($error) && $error->get('npassword_confirmation') != null ? 'has-error' : '' }}">
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
                                    {{ Form::submit('Save', array('class'=>'btn btn-success')) }}
                                </div>
                            </div>
                            {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop