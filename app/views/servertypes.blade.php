@extends('layouts.master')

@section('content')
@include('navbars.topnav', array('navBarPage'=>'servertypes'))

<script>
    $(document).ready(function(){
        var pluginSelect = $('.pluginList');

        pluginSelect.change(function(event) {
            console.log('id '+event.target.id);
            var pluginVersionSelect = $('#pluginVersionList'+event.target.id);
            var pluginConfigSelect = $('#pluginConfigList'+event.target.id);

            console.log(event.target);
            console.log(pluginVersionSelect);
            console.log(pluginConfigSelect);

            pluginVersionSelect.find('option').remove();
            pluginConfigSelect.find('option').remove();

            $.getJSON('plugins/'+$(this).val()+'/versions/json', function(data) {
                for (var i = 0; i < data.length; i++) {
                    pluginVersionSelect.append('<option value='+data[i].id+'>'+data[i].version+'</option>');
                }
            });

            $.getJSON('plugins/'+$(this).val()+'/configs/json', function(data) {
                for (var i = 0; i < data.length; i++) {
                    pluginConfigSelect.append('<option value='+data[i].id+'>'+data[i].name+'</option>');
                }
            });
        });

        var worldSelect = $('.worldList');

        worldSelect.change(function(event) {
            var worldVersionSelect = $('#worldVersionList'+event.target.id);
            worldVersionSelect.find('option').remove();

            $.getJSON('worlds/'+$(this).val()+'/versions/json', function(data) {
                for (var i = 0; i < data.length; i++) {
                    worldVersionSelect.append('<option value='+data[i].id+'>'+data[i].version+'</option>');
                }
            });
        });
    });

    function ConfirmDeletePlugin(plugin){
        return confirm("Are you sure you want to delete the plugin "+plugin+"?");
    }

    function ConfirmDeleteWorld(world){
            return confirm("Are you sure you want to delete the world "+world+"?");
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

@if(Auth::user()->can('read_servertype'))
    <div class="panel-group" id="accordion">
        @if(Auth::user()->can('create_servertype'))
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseAdd">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Server Type
                            <small>Click to add a new server type</small>
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
                        {{ Form::open(array('action' => array('ServerTypeController@postServerType', true), 'class' => 'form-horizontal')) }}
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('name') != null ? 'has-error' : '' }}">
                                {{ Form::label('name-label', 'Name') }}
                                {{ Form::text('name', '', array('class'=>'form-control', 'placeholder' => 'i.e My Server Type', 'maxlength' => '100')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('description') != null ? 'has-error' : '' }}">
                                {{ Form::label('description-label', 'Description') }}
                                {{ Form::text('description', '', array('class'=>'form-control', 'placeholder' => 'i.e This is a server type', 'maxlength' => '255')) }}
                            </div>

                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('players') != null ? 'has-error' : '' }}">
                                {{ Form::label('players-label', 'Players') }}
                                {{ Form::number('players', 1, array('class'=>'form-control', 'min' => 1)) }}
                            </div>

                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('ram') != null ? 'has-error' : '' }}">
                                {{ Form::label('ram-label', 'Memory (MB)') }}
                                {{ Form::number('ram', 1024, array('class'=>'form-control', 'min' => 1024)) }}
                            </div>

                            <div style="margin-top:10px" class="form-group">
                                <div class="col-md-12">
                                    {{ Form::submit('Add Server Type', array('class'=>'btn btn-success')) }}
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        @endif
        @foreach(ServerType::all() as $serverType)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $serverType->id }}">
                            {{{ $serverType->name }}}
                            <small>{{{ $serverType->description }}}</small>
                        </a>
                    </h4>
                </div>
                <div id="collapse{{ $serverType->id }}" class="panel-collapse collapse {{ Session::has('open'.$serverType->id) ? 'in' : '' }}">
                    <div class="panel-body">
                        @if($serverType->defaultWorld() == null)
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        <p>There is currently no default world set. This server will not function correctly without a default world.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <ul class="nav nav-tabs">
                            <li role="presentation" class="active"><a href="#plugins{{ $serverType->id }}" data-toggle="tab" style="{{ Session::has('errorAddPlugin'.$serverType->id) == true ? 'color:red; font-weight:bold;' : ''}}">Plugins</a></li>
                            <li role="presentation"><a href="#worlds{{ $serverType->id }}" data-toggle="tab" style="{{ Session::has('errorAddWorld'.$serverType->id) == true ? 'color:red; font-weight:bold;' : ''}}">Worlds</a></li>
                            <li role="presentation"><a href="#edit{{ $serverType->id }}" data-toggle="tab" style="{{ Session::has('errorEdit'.$serverType->id) == true ? 'color:red; font-weight:bold;' : ''}}">Edit</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="plugins{{ $serverType->id }}" style="margin-top: 10px">
                                @if(Session::has('errorAddPlugin'.$serverType->id))
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <ul>
                                                    @foreach(Session::get('errorAddPlugin'.$serverType->id)->all() as $errorMessage)
                                                        <li>{{ $errorMessage  }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <table style="margin-top: 10px" class="table table-bordered" data-toggle="table">
                                    <thread>
                                        <tr>
                                            <th>Plugin Name</th>
                                            <th>Plugin Version</th>
                                            <th>Plugin Config</th>
                                        </tr>
                                    </thread>
                                    <tbody>
                                        @foreach($serverType->plugins()->get() as $plugin)
                                            <tr>
                                                {{ Form::open(array('action' => array('ServerTypeController@deleteServerTypePlugin', $serverType->id, $plugin->id), 'class' => 'form-horizontal', 'method' => 'DELETE', 'onsubmit' => 'return ConfirmDeletePlugin("'.$plugin->plugin()->name.'")')) }}
                                                    <td>{{{ $plugin->plugin()->name }}}</td>
                                                    <td>{{{ $plugin->pluginVersion()->version }}}</td>
                                                    <td>{{{ $plugin->pluginConfig() != null ? $plugin->pluginConfig()->name : '' }}}</td>
                                                    <td>{{ Form::submit('Remove Plugin', array('class'=>'btn btn-danger')) }}</td>
                                                {{ Form::close() }}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ Form::open(array('action' => array('ServerTypeController@postServerTypePlugin', $serverType->id), 'class' => 'form-horizontal', 'method' => 'POST')) }}
                                    <div style="margin-bottom: 25px" class="input-group">
                                        {{ Form::label('plugin-label', 'Plugin Name') }}
                                        <select name='plugin' class="form-control pluginList" id="{{$serverType->id}}">
                                            <option selected value="-1">Please select a plugin</option>
                                            @foreach(Plugin::all() as $plugin)
                                                @if($plugin->type == 'SERVER')
                                                    <option value="{{ $plugin->id }}">{{{ $plugin->name }}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="margin-bottom: 25px" class="input-group">
                                        {{ Form::label('pluginVersion-label', 'Plugin Version') }}
                                        <select name='pluginVersion' class="form-control" id="pluginVersionList{{$serverType->id}}">
                                        </select>
                                    </div>
                                    <div style="margin-bottom: 25px" class="input-group">
                                        {{ Form::label('pluginConfig-label', 'Plugin Config') }}
                                        <select name='pluginConfig' class="form-control" id="pluginConfigList{{$serverType->id}}">
                                        </select>
                                    </div>
                                    <div style="margin-top:10px" class="form-group">
                                        <div class="col-md-12">
                                            {{ Form::submit('Add Plugin', array('class'=>'btn btn-primary')) }}
                                        </div>
                                    </div>
                                {{ Form::close() }}
                            </div>
                            <div class="tab-pane" id="worlds{{ $serverType->id }}" style="margin-top: 10px">
                                @if(Session::has('errorAddWorld'.$serverType->id))
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <ul>
                                                    @foreach(Session::get('errorAddWorld'.$serverType->id)->all() as $errorMessage)
                                                        <li>{{ $errorMessage  }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <table style="margin-top: 10px" class="table table-bordered" data-toggle="table">
                                                                    <thread>
                                                                        <tr>
                                                                            <th>World Name</th>
                                                                            <th>World Version</th>
                                                                            <th>Default</th>
                                                                        </tr>
                                                                    </thread>
                                                                    <tbody>
                                                                        @foreach($serverType->worlds()->get() as $world)
                                                                            <tr>
                                                                                {{ Form::open(array('action' => array('ServerTypeController@deleteServerTypeWorld', $serverType->id, $world->id), 'class' => 'form-horizontal', 'method' => 'DELETE', 'onsubmit' => 'return ConfirmDeleteWorld("'.$world->world()->name.'")')) }}
                                                                                    <td>{{{ $world->world()->name }}}</td>
                                                                                    <td>{{{ $world->worldVersion()->version }}}</td>
                                                                                    <td>{{ $world->default == true ? 'Yes' : 'No' }}</td>
                                                                                    <td>{{ Form::submit('Remove World', array('class'=>'btn btn-danger')) }}</td>
                                                                                {{ Form::close() }}
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                                {{ Form::open(array('action' => array('ServerTypeController@postServerTypeWorld', $serverType->id), 'class' => 'form-horizontal', 'method' => 'POST')) }}
                                                                    <div style="margin-bottom: 25px" class="input-group">
                                                                        {{ Form::label('plugin-label', 'World Name') }}
                                                                        <select name='world' class="form-control worldList" id="{{$serverType->id}}">
                                                                            <option selected value="-1">Please select a world</option>
                                                                            @foreach(World::all() as $world)
                                                                                <option value="{{ $world->id }}">{{{ $world->name }}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div style="margin-bottom: 25px" class="input-group">
                                                                        {{ Form::label('worldVersion-label', 'World Version') }}
                                                                        <select name='worldVersion' class="form-control" id="worldVersionList{{$serverType->id}}">
                                                                        </select>
                                                                    </div>
                                                                    <div style="margin-bottom: 25px" class="input-group">
                                                                        {{ Form::label('default-label', 'Default World') }}
                                                                        {{ Form::checkbox('default', '1', false, array('class'=>'form-control')) }}
                                                                    </div>
                                                                    <div style="margin-top:10px" class="form-group">
                                                                        <div class="col-md-12">
                                                                            {{ Form::submit('Add World', array('class'=>'btn btn-primary')) }}
                                                                        </div>
                                                                    </div>
                                                                {{ Form::close() }}
                            </div>
                            <div class="tab-pane" id="edit{{ $serverType->id }}">
                                @if(Session::has('errorEdit'.$serverType->id))
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <ul>
                                                    @foreach(Session::get('errorEdit'.$serverType->id)->all() as $errorMessage)
                                                        <li>{{ $errorMessage  }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                {{ Form::open(array('action' => array('ServerTypeController@putServerType', $serverType->id), 'class' => 'form-horizontal', 'method' => 'PUT')) }}
                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorEdit') && Session::get('errorEdit')->get('name') != null ? 'has-error' : '' }}">
                                        {{ Form::label('name-label', 'Name') }}
                                        {{ Form::text('name', $serverType->name, array('class'=>'form-control', 'placeholder' => 'i.e My Server Type', 'maxlength' => '100')) }}
                                    </div>
                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorEdit') && Session::get('errorEdit')->get('description') != null ? 'has-error' : '' }}">
                                        {{ Form::label('description-label', 'Description') }}
                                        {{ Form::text('description', $serverType->description, array('class'=>'form-control', 'placeholder' => 'i.e This is a server type', 'maxlength' => '255')) }}
                                    </div>

                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorEdit') && Session::get('errorEdit')->get('players') != null ? 'has-error' : '' }}">
                                        {{ Form::label('players-label', 'Players') }}
                                        {{ Form::number('players', $serverType->players, array('class'=>'form-control', 'min' =>  1)) }}
                                    </div>

                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorEdit') && Session::get('errorEdit')->get('ram') != null ? 'has-error' : '' }}">
                                        {{ Form::label('ram-label', 'Memory (MB)') }}
                                        {{ Form::number('ram', $serverType->ram, array('class'=>'form-control', 'min' => 1024)) }}
                                    </div>

                                    <div style="margin-top:10px" class="form-group">
                                        <div class="col-md-12">
                                            {{ Form::submit('Save Server Type', array('class'=>'btn btn-primary')) }}
                                        </div>
                                    </div>
                                {{ Form::close() }}
                                <script>
                                    function ConfirmDelete(){
                                        return confirm("Are you sure you want to delete the server type {{{ $serverType->name }}}?");
                                    }
                                </script>
                                @if(Auth::user()->can('delete_servertype'))
                                    {{ Form::open(array('action' => array('ServerTypeController@deleteServerType', $serverType->id), 'class' => 'form-horizontal', 'method'=>'DELETE', 'onsubmit' => 'return ConfirmDelete()')) }}
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