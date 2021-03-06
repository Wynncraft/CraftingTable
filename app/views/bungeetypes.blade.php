@extends('layouts.master')

@section('content')
@include('navbars.topnav', array('navBarPage'=>'bungeetypes'))

<script>
    function ConfirmDeletePlugin(plugin){
        return confirm("Are you sure you want to delete the plugin "+plugin+"?");
    }
</script>

@if(Session::has('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <p>{{{ Session::get('error') }}}</p>
    </div>
@endif

@if(Session::has('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <p>{{{ Session::get('success') }}}</p>
    </div>
@endif

@if(Auth::user()->can('read_bungeetype'))
    <div class="panel-group" id="accordion">
        @if(Auth::user()->can('create_bungeetype'))
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseAdd">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Bungee Type
                            <small>Click to add a new bungee type</small>
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
                        {{ Form::open(array('action' => array('BungeeTypeController@postBungeeType', true), 'class' => 'form-horizontal')) }}
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('name') != null ? 'has-error' : '' }}">
                                {{ Form::label('name-label', 'Name') }}
                                {{ Form::text('name', '', array('class'=>'form-control', 'placeholder' => 'i.e My Bungee Type', 'maxlength' => '100')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('description') != null ? 'has-error' : '' }}">
                                {{ Form::label('description-label', 'Description') }}
                                {{ Form::text('description', '', array('class'=>'form-control', 'placeholder' => 'i.e This is a bungee type', 'maxlength' => '255')) }}
                            </div>

                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('ram') != null ? 'has-error' : '' }}">
                                {{ Form::label('ram-label', 'Memory (MB)') }}
                                {{ Form::number('ram', 1024, array('class'=>'form-control', 'min' => 1024)) }}
                            </div>

                            <div style="margin-top:10px" class="form-group">
                                <div class="col-md-12">
                                    {{ Form::submit('Add Bungee Type', array('class'=>'btn btn-success')) }}
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        @endif
        @foreach(BungeeType::all() as $bungeeType)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $bungeeType->id }}">
                            {{{ $bungeeType->name }}}
                            <small>{{{ $bungeeType->description }}}</small>
                        </a>
                    </h4>
                </div>
                <div id="collapse{{ $bungeeType->id }}" class="panel-collapse collapse {{ Session::has('open'.$bungeeType->id) ? 'in' : '' }}">
                    <div class="panel-body">
                        <ul class="nav nav-tabs">
                            <li role="presentation" class="active"><a href="#plugins{{ $bungeeType->id }}" data-toggle="tab" style="{{ Session::has('errorAddPlugin'.$bungeeType->id) == true ? 'color:red; font-weight:bold;' : ''}}">Plugins</a></li>
                            <li role="presentation"><a href="#edit{{ $bungeeType->id }}" data-toggle="tab" style="{{ Session::has('errorEdit'.$bungeeType->id) == true ? 'color:red; font-weight:bold;' : ''}}">Edit</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="plugins{{ $bungeeType->id }}" style="margin-top: 10px">
                                @if(Session::has('errorAddPlugin'.$bungeeType->id))
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <ul>
                                                    @foreach(Session::get('errorAddPlugin'.$bungeeType->id)->all() as $errorMessage)
                                                        <li>{{ $errorMessage  }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                    @if(Session::has('errorSavePlugin'.$bungeeType->id))
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="alert alert-danger alert-dismissible">
                                                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                    <ul>
                                                        @foreach(Session::get('errorSavePlugin'.$bungeeType->id)->all() as $errorMessage)
                                                            <li>{{{ $errorMessage  }}}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    {{ Form::open(array('action' => array('BungeeTypeController@putBungeeTypePlugin', $bungeeType->id), 'class' => 'form-horizontal', 'method' => 'PUT')) }}
                                    <table style="margin-top: 10px" class="table table-bordered" data-toggle="table">
                                        <thread>
                                            <tr>
                                                <th>Plugin Name</th>
                                                <th>Plugin Version</th>
                                                <th>Plugin Config</th>
                                                <th>Remove</th>
                                            </tr>
                                        </thread>
                                        <tbody>
                                        @foreach($bungeeType->plugins()->get() as $plugin)
                                            <tr>
                                                <td>{{ Form::text($bungeeType->id.'name'.$plugin->id, $plugin->plugin()->name, array('class'=>'form-control', 'disabled')) }}</td>
                                                <td>
                                                    <select name="{{$bungeeType->id}}pluginVersion{{$plugin->id}}" id="{{$bungeeType->id}}pluginVersion{{$plugin->id}}">
                                                        <option selected value="-1">Please select a plugin version</option>
                                                        @foreach($plugin->plugin()->versions()->get() as $pluginVersion)
                                                            @if($plugin->pluginVersion() != null && $plugin->pluginVersion()->id == $pluginVersion->id)
                                                                <option selected value="{{ $pluginVersion->id }}">{{{ $pluginVersion->version }}}</option>
                                                            @else
                                                                <option value="{{ $pluginVersion->id }}">{{{ $pluginVersion->version }}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="{{$bungeeType->id}}pluginConfig{{$plugin->id}}" id="{{$bungeeType->id}}pluginConfig{{$plugin->id}}">
                                                        <option selected value="-1">Please select a plugin config</option>
                                                        @foreach($plugin->plugin()->configs()->get() as $pluginconfig)
                                                            @if($plugin->pluginConfig() != null && $plugin->pluginConfig()->id == $pluginconfig->id)
                                                                <option selected value="{{ $pluginconfig->id }}">{{{ $pluginconfig->name }}}</option>
                                                            @else
                                                                <option value="{{ $pluginconfig->id }}">{{{ $pluginconfig->name }}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><a href="{{ action("BungeeTypeController@deleteBungeeTypePlugin", [$bungeeType->id, $plugin->id]) }}" class="btn btn-danger" onclick="return ConfirmDeletePlugin('{{ $plugin->plugin()->name }}')"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <div style="margin-top:10px" class="form-group">
                                        <div class="col-md-12">
                                            {{ Form::submit('Save Plugins', array('class'=>'btn btn-success')) }}
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                {{ Form::open(array('action' => array('BungeeTypeController@postBungeeTypePlugin', $bungeeType->id), 'class' => 'form-horizontal', 'method' => 'POST')) }}
                                    <div style="margin-bottom: 25px" class="input-group">
                                        {{ Form::label('plugin-label', 'Plugin Name') }}
                                        <select name='plugin' class="form-control pluginList" id="{{$bungeeType->id}}">
                                            <option selected value="-1">Please select a plugin</option>
                                            @foreach(Plugin::all() as $plugin)
                                                @if($plugin->type == 'BUNGEE')
                                                    <option value="{{ $plugin->id }}">{{{ $plugin->name }}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    {{--
                                    <!--
                                    <div style="margin-bottom: 25px" class="input-group">
                                        {{ Form::label('pluginVersion-label', 'Plugin Version') }}
                                        <select name='pluginVersion' class="form-control" id="pluginVersionList{{$bungeeType->id}}">
                                        </select>
                                    </div>
                                    <div style="margin-bottom: 25px" class="input-group">
                                        {{ Form::label('pluginConfig-label', 'Plugin Config') }}
                                        <select name='pluginConfig' class="form-control" id="pluginConfigList{{$bungeeType->id}}">
                                        </select>
                                    </div>
                                    -->
                                    --}}
                                    <div style="margin-top:10px" class="form-group">
                                        <div class="col-md-12">
                                            {{ Form::submit('Add Plugin', array('class'=>'btn btn-primary')) }}
                                        </div>
                                    </div>
                                {{ Form::close() }}
                            </div>
                            <div class="tab-pane" id="edit{{ $bungeeType->id }}">
                                @if(Session::has('errorEdit'.$bungeeType->id))
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <ul>
                                                    @foreach(Session::get('errorEdit'.$bungeeType->id)->all() as $errorMessage)
                                                        <li>{{ $errorMessage  }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                {{ Form::open(array('action' => array('BungeeTypeController@putBungeeType', $bungeeType->id), 'class' => 'form-horizontal', 'method' => 'PUT')) }}
                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorEdit') && Session::get('errorEdit')->get('name') != null ? 'has-error' : '' }}">
                                        {{ Form::label('name-label', 'Name') }}
                                        {{ Form::text('name', $bungeeType->name, array('class'=>'form-control', 'placeholder' => 'i.e My Bungee Type', 'maxlength' => '100')) }}
                                    </div>
                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorEdit') && Session::get('errorEdit')->get('description') != null ? 'has-error' : '' }}">
                                        {{ Form::label('description-label', 'Description') }}
                                        {{ Form::text('description', $bungeeType->description, array('class'=>'form-control', 'placeholder' => 'i.e This is a bungee type', 'maxlength' => '255')) }}
                                    </div>

                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorEdit') && Session::get('errorEdit')->get('ram') != null ? 'has-error' : '' }}">
                                        {{ Form::label('ram-label', 'Memory (MB)') }}
                                        {{ Form::number('ram', $bungeeType->ram, array('class'=>'form-control', 'min' => 1024)) }}
                                    </div>

                                    <div style="margin-top:10px" class="form-group">
                                        <div class="col-md-12">
                                            {{ Form::submit('Save Bungee Type', array('class'=>'btn btn-primary')) }}
                                        </div>
                                    </div>
                                {{ Form::close() }}
                                <script>
                                    function ConfirmDelete(){
                                        return confirm("Are you sure you want to delete the bungee type {{{ $bungeeType->name }}}?");
                                    }
                                </script>
                                @if(Auth::user()->can('delete_bungeetype'))
                                    {{ Form::open(array('action' => array('BungeeTypeController@deleteBungeeType', $bungeeType->id), 'class' => 'form-horizontal', 'method'=>'DELETE', 'onsubmit' => 'return ConfirmDelete()')) }}
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