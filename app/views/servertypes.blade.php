@extends('layouts.master')

@section('content')
@include('navbars.topnav', array('navBarPage'=>'servertypes'))

<script>
    $(document).ready(function(){
        var pluginSelect = $('#pluginList');
        var pluginVersionSelect = $('#pluginVersionList');
        var pluginConfigSelect = $('#pluginVersionList');

        pluginSelect.change(function() {
            pluginVersionSelect.find('option').remove();
            pluginConfigSelect.find('option').remove();

            $.getJSON('plugins/'+pluginSelect.val()+'/versions/json', function(data) {
                for (var i = 0; i < data.length; i++) {
                    pluginVersionSelect.append('<option value='+data[i].id+'>'+data[i].version+'</option>');
                }
            });

            $.getJSON('plugins/'+pluginSelect.val()+'/configs/json', function(data) {
                for (var i = 0; i < data.length; i++) {
                    pluginConfigSelect.append('<option value='+data[i].id+'>'+data[i].name+'</option>');
                }
            });
        });
    });

    function ConfirmDeletePlugin(plugin){
        return confirm("Are you sure you want to delete the plugin "+plugin+"?");
    }
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
                                {{ Form::text('players', '', array('class'=>'form-control', 'placeholder' => 'i.e 10')) }}
                            </div>

                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('ram') != null ? 'has-error' : '' }}">
                                {{ Form::label('ram-label', 'Memory (MB)') }}
                                {{ Form::text('ram', '', array('class'=>'form-control', 'placeholder' => 'i.e 1024')) }}
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
                            {{ $serverType->name }}
                            <small>{{ $serverType->description }}</small>
                        </a>
                    </h4>
                </div>
                <div id="collapse{{ $serverType->id }}" class="panel-collapse collapse {{ Session::has('open'.$serverType->id) ? 'in' : '' }}">
                    <div class="panel-body">
                        <ul class="nav nav-tabs">
                            <li role="presentation" class="active"><a href="#plugins{{ $serverType->id }}" data-toggle="tab" style="{{ Session::has('errorAddPlugin'.$serverType->id) == true ? 'color:red; font-weight:bold;' : ''}}">Plugins</a></li>
                            <li role="presentation"><a href="#worlds{{ $serverType->id }}" data-toggle="tab">Worlds</a></li>
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
                                                    <td>{{ $plugin->plugin()->name }}</td>
                                                    <td>{{ $plugin->pluginVersion()->version }}</td>
                                                    <td></td>
                                                    <td>{{ Form::submit('Remove Plugin', array('class'=>'btn btn-danger')) }}</td>
                                                {{ Form::close() }}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ Form::open(array('action' => array('ServerTypeController@postServerTypePlugin', $serverType->id), 'class' => 'form-horizontal', 'method' => 'POST')) }}
                                    <div style="margin-bottom: 25px" class="input-group">
                                        {{ Form::label('plugin-label', 'Plugin Name') }}
                                        <select name='plugin' class="form-control" id="pluginList">
                                            <option selected value="-1">Please select a plugin</option>
                                            @foreach(Plugin::all() as $plugin)
                                                <option value="{{ $plugin->id }}">{{ $plugin->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="margin-bottom: 25px" class="input-group">
                                        {{ Form::label('pluginVersion-label', 'Plugin Version') }}
                                        <select name='pluginVersion' class="form-control" id="pluginVersionList">
                                        </select>
                                    </div>
                                    <div style="margin-bottom: 25px" class="input-group">
                                        {{ Form::label('pluginConfig-label', 'Plugin Config') }}
                                        <select name='pluginConfig' class="form-control" id="pluginConfigList">
                                        </select>
                                    </div>
                                    <div style="margin-top:10px" class="form-group">
                                        <div class="col-md-12">
                                            {{ Form::submit('Add Plugin', array('class'=>'btn btn-primary')) }}
                                        </div>
                                    </div>
                                {{ Form::close() }}
                            </div>
                            <div class="tab-pane" id="worlds{{ $serverType->id }}">worlds</div>
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
                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('name') != null ? 'has-error' : '' }}">
                                        {{ Form::label('name-label', 'Name') }}
                                        {{ Form::text('name', $serverType->name, array('class'=>'form-control', 'placeholder' => 'i.e My Server Type', 'maxlength' => '100')) }}
                                    </div>
                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('description') != null ? 'has-error' : '' }}">
                                        {{ Form::label('description-label', 'Description') }}
                                        {{ Form::text('description', $serverType->description, array('class'=>'form-control', 'placeholder' => 'i.e This is a server type', 'maxlength' => '255')) }}
                                    </div>

                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('players') != null ? 'has-error' : '' }}">
                                        {{ Form::label('players-label', 'Players') }}
                                        {{ Form::text('players', $serverType->players, array('class'=>'form-control', 'placeholder' => 'i.e 10')) }}
                                    </div>

                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('ram') != null ? 'has-error' : '' }}">
                                        {{ Form::label('ram-label', 'Memory (MB)') }}
                                        {{ Form::text('ram', $serverType->ram, array('class'=>'form-control', 'placeholder' => 'i.e 1024')) }}
                                    </div>

                                    <div style="margin-top:10px" class="form-group">
                                        <div class="col-md-12">
                                            {{ Form::submit('Save Server Type', array('class'=>'btn btn-primary')) }}
                                        </div>
                                    </div>
                                {{ Form::close() }}
                                <script>
                                    function ConfirmDelete(){
                                        return confirm("Are you sure you want to delete the server type {{ $serverType->name }}?");
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