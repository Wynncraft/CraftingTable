@extends('layouts.master')

@section('content')
@include('navbars.topnav', array('navBarPage'=>'plugins'))

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

@if(Auth::user()->can('read_plugin'))
    <div class="panel-group" id="accordion">
        @if(Auth::user()->can('create_plugin'))
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseAdd">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Plugin
                            <small>Click to add a new plugin</small>
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
                        {{ Form::open(array('action' => array('PluginController@postPlugin', true), 'class' => 'form-horizontal')) }}
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('name') != null ? 'has-error' : '' }}">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                                {{ Form::text('name', '', array('class'=>'form-control', 'placeholder' => 'plugin name')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('description') != null ? 'has-error' : '' }}">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                {{ Form::text('description', '', array('class'=>'form-control', 'placeholder' => 'plugin description')) }}
                            </div>
                            <div style="margin-top:10px" class="form-group">
                                <div class="col-md-12">
                                    {{ Form::submit('Create Plugin', array('class'=>'btn btn-success')) }}
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        @endif
        @foreach(Plugin as $plugin)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $plugin->id }}">
                            {{ $plugin->name }}
                            <small>{{ $plugin->description }}</small>
                        </a>
                    </h4>
                </div>
                <div id="collapse{{ $plugin->id }}" class="panel-collapse collapse {{ Session::has('error'.$plugin->id) ? 'in' : '' }}">
                    <div class="panel-body">
                        @if(Session::has('error'.$plugin->id))
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        <ul>
                                            @foreach(Session::get('error'.$plugin->id)->all() as $errorMessage)
                                                <li>{{ $errorMessage  }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                        {{ Form::open(array('action' => array('PluginController@putPlugin', $plugin->id), 'class' => 'form-horizontal', 'method' => 'PUT')) }}
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('error'.$plugin->id) && Session::get('error'.$plugin->id)->get('name') != null ? 'has-error' : '' }}">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                                {{ Form::text('name', $plugin->name, array('class'=>'form-control', 'placeholder' => 'email')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('error'.$plugin->id) && Session::get('error'.$plugin->id)->get('description') != null ? 'has-error' : '' }}">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                {{ Form::text('description', $plugin->description, array('class'=>'form-control', 'placeholder' => 'username')) }}
                            </div>
                        {{ Form::close() }}
                        <script>
                            function ConfirmDelete(){
                                return confirm("Are you sure you want to delete the user {{ $user->username }}?");
                            }
                        </script>
                        @if(Auth::user()->can('delete_plugin'))
                            {{ Form::open(array('action' => array('PluginController@deletePlugin', $user->id), 'class' => 'form-horizontal', 'method'=>'DELETE', 'onsubmit' => 'return ConfirmDelete()')) }}
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