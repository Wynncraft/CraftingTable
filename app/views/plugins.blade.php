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
                                {{ Form::label('name-label', 'Plugin Name') }}
                                {{ Form::text('name', '', array('class'=>'form-control', 'placeholder' => 'i.e My Plugin')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('type') != null ? 'has-error' : '' }}">
                                {{ Form::label('type-label', 'Plugin Type') }}
                                <div>
                                    {{ Form::radio('type', 'SERVER') }} Server<br />
                                    {{ Form::radio('type', 'BUNGEE') }} Bungee
                                </div>
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('description') != null ? 'has-error' : '' }}">
                                {{ Form::label('description-label', 'Plugin Description') }}
                                {{ Form::text('description', '', array('class'=>'form-control', 'placeholder' => 'i.e This is my plugin')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('directory') != null ? 'has-error' : '' }}">
                                {{ Form::label('directory-label', 'Plugin Directory') }}
                                {{ Form::text('directory', '', array('class'=>'form-control', 'placeholder' => 'i.e myPluginDirectory')) }}
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
        @foreach(Plugin::all() as $plugin)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $plugin->id }}">
                            {{{ $plugin->name }}}
                            <small>{{{ $plugin->description }}}</small>
                        </a>
                    </h4>
                </div>
                <div id="collapse{{ $plugin->id }}" class="panel-collapse collapse {{ Session::has('open'.$plugin->id) ? 'in' : '' }}">
                    <div class="panel-body">
                        <ul class="nav nav-tabs">
                            <li role="presentation" class="active"><a href="#versions{{ $plugin->id }}" data-toggle="tab" style="{{ Session::has('errorVersion'.$plugin->id) == true ? 'color:red; font-weight:bold;' : ''}}">Versions</a></li>
                            <li role="presentation"><a href="#configs{{ $plugin->id }}" data-toggle="tab">Configs</a></li>
                            <li role="presentation"><a href="#edit{{ $plugin->id }}" data-toggle="tab" style="{{ Session::has('errorEdit'.$plugin->id) == true ? 'color:red; font-weight:bold;' : ''}}">Edit</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="versions{{ $plugin->id }}">
                                <ul style="margin-top: 10px" class="nav nav-tabs">
                                    <li role="presentation" class="active"><a href="#addVersion{{ $plugin->id }}" data-toggle="tab">Add Version</a></li>
                                    @foreach($plugin->versions()->get() as $version)
                                        <li role="presentation"><a href="#version{{ $version->id }}" data-toggle="tab">{{{ $version->version }}}</a></li>
                                    @endforeach
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="addVersion{{ $plugin->id }}">
                                        @if(Session::has('errorVersion'.$plugin->id))
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="alert alert-danger alert-dismissible">
                                                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                        <ul>
                                                            @foreach(Session::get('errorVersion'.$plugin->id)->all() as $errorMessage)
                                                                <li>{{ $errorMessage  }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        {{ Form::open(array('action' => array('PluginController@postVersion', $plugin->id), 'class' => 'form-horizontal')) }}
                                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorVersion'.$plugin->id) && Session::get('errorVersion'.$plugin->id)->get('version') != null ? 'has-error' : '' }}">
                                                {{ Form::label('version-label', 'Plugin Version') }}
                                                {{ Form::text('version', '', array('class'=>'form-control', 'placeholder' => 'i.e 1.2.5')) }}
                                            </div>

                                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorVersion'.$plugin->id) && Session::get('errorVersion'.$plugin->id)->get('description') != null ? 'has-error' : '' }}">
                                                {{ Form::label('description-label', 'Plugin Version Description') }}
                                                {{ Form::text('description', '', array('class'=>'form-control', 'placeholder' => 'i.e This is a plugin version')) }}
                                            </div>

                                            <div style="margin-top:10px" class="form-group">
                                                <div class="col-md-12">
                                                    {{ Form::submit('Add Version', array('class'=>'btn btn-success')) }}
                                                </div>
                                            </div>
                                        {{ Form::close() }}
                                    </div>
                                    @foreach($plugin->versions()->get() as $version)
                                        <div class="tab-pane" id="version{{ $version->id }}">
                                             <script>
                                                function ConfirmDeleteVersion(){
                                                    return confirm("Are you sure you want to delete the plugin version {{ $version->version }}?");
                                                }
                                             </script>
                                            {{ Form::open(array('action' => array('PluginController@deleteVersion', $plugin->id, $version->id), 'class' => 'form-horizontal', 'method' => 'DELETE', 'onsubmit' => 'return ConfirmDeleteVersion()')) }}
                                                <div style="margin-bottom: 25px" class="input-group">
                                                    {{ Form::label('version-label', 'Plugin Version') }}
                                                    {{ Form::text('version', $version->version, array('class'=>'form-control', 'placeholder' => 'i.e 1.2.5', 'disabled')) }}
                                                </div>

                                                <div style="margin-bottom: 25px" class="input-group">
                                                    {{ Form::label('description-label', 'Plugin Version Description') }}
                                                    {{ Form::text('description', $version->description, array('class'=>'form-control', 'placeholder' => 'i.e This is a plugin version', 'disabled')) }}
                                                </div>

                                                <div style="margin-top:10px" class="form-group">
                                                    <div class="col-md-12">
                                                        {{ Form::submit('Delete Version', array('class'=>'btn btn-danger')) }}
                                                    </div>
                                                </div>
                                            {{ Form::close() }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="tab-pane" id="configs{{ $plugin->id }}">
                                <ul style="margin-top: 10px" class="nav nav-tabs">
                                    <li role="presentation" class="active"><a href="#addConfig{{ $plugin->id }}" data-toggle="tab">Add Config</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane" id="addConfig{{ $plugin->id }}">
                                        <p>add config</p>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="edit{{ $plugin->id }}">
                                @if(Session::has('errorEdit'.$plugin->id))
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <ul>
                                                    @foreach(Session::get('errorEdit'.$plugin->id)->all() as $errorMessage)
                                                        <li>{{ $errorMessage  }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                {{ Form::open(array('action' => array('PluginController@putPlugin', $plugin->id), 'class' => 'form-horizontal', 'method' => 'PUT')) }}
                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorEdit'.$plugin->id) && Session::get('errorEdit'.$plugin->id)->get('name') != null ? 'has-error' : '' }}">
                                        {{ Form::label('name-label', 'Plugin Name') }}
                                        {{ Form::text('name', $plugin->name, array('class'=>'form-control', 'placeholder' => 'i.e My Plugin')) }}
                                    </div>
                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorEdit'.$plugin->id) && Session::get('errorEdit'.$plugin->id)->get('type') != null ? 'has-error' : '' }}">
                                        {{ Form::label('type-label', 'Plugin Type') }}
                                        <div>
                                            {{ Form::radio('type', 'SERVER', $plugin->type == 'SERVER' ? true : false, array('disabled')) }} Server<br />
                                            {{ Form::radio('type', 'BUNGEE', $plugin->type == 'BUNGEE' ? true : false, array('disabled')) }} Bungee
                                        </div>
                                    </div>
                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorEdit'.$plugin->id) && Session::get('errorEdit'.$plugin->id)->get('description') != null ? 'has-error' : '' }}">
                                        {{ Form::label('description-label', 'Plugin Description') }}
                                        {{ Form::text('description', $plugin->description, array('class'=>'form-control', 'placeholder' => 'i.e This is my plugin')) }}
                                    </div>
                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorEdit'.$plugin->id) && Session::get('errorEdit'.$plugin->id)->get('directory') != null ? 'has-error' : '' }}">
                                        {{ Form::label('directory-label', 'Plugin Directory') }}
                                        {{ Form::text('directory', $plugin->directory, array('class'=>'form-control', 'placeholder' => 'i.e myPluginDirectory')) }}
                                    </div>
                                    @if(Auth::user()->can('update_plugin'))
                                        <div style="margin-top:10px" class="form-group">
                                            <div class="col-md-12">
                                                {{ Form::submit('Save Plugin', array('class'=>'btn btn-primary')) }}
                                            </div>
                                        </div>
                                    @endif
                                {{ Form::close() }}
                                <script>
                                    function ConfirmDelete(){
                                        return confirm("Are you sure you want to delete the plugin {{ $plugin->name }}?");
                                    }
                                </script>
                                @if(Auth::user()->can('delete_plugin'))
                                    {{ Form::open(array('action' => array('PluginController@deletePlugin', $plugin->id), 'class' => 'form-horizontal', 'method'=>'DELETE', 'onsubmit' => 'return ConfirmDelete()')) }}
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
                </div>
            </div>
        @endforeach
    </div>
@endif

@stop