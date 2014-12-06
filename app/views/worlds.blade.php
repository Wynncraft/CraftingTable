@extends('layouts.master')

@section('content')
@include('navbars.topnav', array('navBarPage'=>'worlds'))

@if(Session::has('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <p>{{ Session::get('error') }}</p>
    </div>
@endif

@if(Session::has('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <p>{{{ Session::get('success') }}}</p>
    </div>
@endif

@if(Auth::user()->can('read_world'))
    <div class="panel-group" id="accordion">
        @if(Auth::user()->can('create_world'))
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseAdd">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add World
                            <small>Click to add a new world</small>
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
                        {{ Form::open(array('action' => array('WorldController@postWorld', true), 'class' => 'form-horizontal')) }}
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('name') != null ? 'has-error' : '' }}">
                                {{ Form::label('name-label', 'World Name') }}
                                {{ Form::text('name', '', array('class'=>'form-control', 'placeholder' => 'i.e My World')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('description') != null ? 'has-error' : '' }}">
                                {{ Form::label('description-label', 'World Description') }}
                                {{ Form::text('description', '', array('class'=>'form-control', 'placeholder' => 'i.e This is my world')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('directory') != null ? 'has-error' : '' }}">
                                {{ Form::label('directory-label', 'World Directory') }}
                                {{ Form::text('directory', '', array('class'=>'form-control', 'placeholder' => 'i.e myWorldDirectory')) }}
                            </div>
                            <div style="margin-top:10px" class="form-group">
                                <div class="col-md-12">
                                    {{ Form::submit('Create World', array('class'=>'btn btn-success')) }}
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        @endif
        @foreach(World::all() as $world)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $world->id }}">
                            {{{ $world->name }}}
                            <small>{{{ $world->description }}}</small>
                        </a>
                    </h4>
                </div>
                <div id="collapse{{ $world->id }}" class="panel-collapse collapse {{ Session::has('open'.$world->id) ? 'in' : '' }}">
                    <div class="panel-body">
                        <ul class="nav nav-tabs">
                            <li role="presentation" class="active"><a href="#versions{{ $world->id }}" data-toggle="tab" style="{{ Session::has('errorVersion'.$world->id) == true ? 'color:red; font-weight:bold;' : ''}}">Versions</a></li>
                            <li role="presentation"><a href="#edit{{ $world->id }}" data-toggle="tab" style="{{ Session::has('errorEdit'.$world->id) == true ? 'color:red; font-weight:bold;' : ''}}">Edit</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="versions{{ $world->id }}">
                                <ul style="margin-top: 10px" class="nav nav-tabs">
                                    <li role="presentation" class="active"><a href="#addVersion{{ $world->id }}" data-toggle="tab">Add Version</a></li>
                                    @foreach($world->versions()->get() as $version)
                                        <li role="presentation"><a href="#version{{ $version->id }}" data-toggle="tab">{{{ $version->version }}}</a></li>
                                    @endforeach
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="addVersion{{ $world->id }}">
                                        @if(Session::has('errorVersion'.$world->id))
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="alert alert-danger alert-dismissible">
                                                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                        <ul>
                                                            @foreach(Session::get('errorVersion'.$world->id)->all() as $errorMessage)
                                                                <li>{{ $errorMessage  }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        {{ Form::open(array('action' => array('WorldController@postVersion', $world->id), 'class' => 'form-horizontal')) }}
                                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorVersion'.$world->id) && Session::get('errorVersion'.$world->id)->get('version') != null ? 'has-error' : '' }}">
                                                {{ Form::label('version-label', 'World Version') }}
                                                {{ Form::text('version', '', array('class'=>'form-control', 'placeholder' => 'i.e 1.2.5')) }}
                                            </div>

                                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorVersion'.$world->id) && Session::get('errorVersion'.$world->id)->get('description') != null ? 'has-error' : '' }}">
                                                {{ Form::label('description-label', 'World Version Description') }}
                                                {{ Form::text('description', '', array('class'=>'form-control', 'placeholder' => 'i.e This is a world version')) }}
                                            </div>

                                            <div style="margin-top:10px" class="form-group">
                                                <div class="col-md-12">
                                                    {{ Form::submit('Add Version', array('class'=>'btn btn-success')) }}
                                                </div>
                                            </div>
                                        {{ Form::close() }}
                                    </div>
                                    @foreach($world->versions()->get() as $version)
                                        <div class="tab-pane" id="version{{ $version->id }}">
                                             <script>
                                                function ConfirmDeleteVersion(){
                                                    return confirm("Are you sure you want to delete the world version {{ $version->version }}?");
                                                }
                                             </script>
                                            {{ Form::open(array('action' => array('WorldController@deleteVersion', $world->id, $version->id), 'class' => 'form-horizontal', 'method' => 'DELETE', 'onsubmit' => 'return ConfirmDeleteVersion()')) }}
                                                <div style="margin-bottom: 25px" class="input-group">
                                                    {{ Form::label('version-label', 'World Version') }}
                                                    {{ Form::text('version', $version->version, array('class'=>'form-control', 'placeholder' => 'i.e 1.2.5', 'disabled')) }}
                                                </div>

                                                <div style="margin-bottom: 25px" class="input-group">
                                                    {{ Form::label('description-label', 'World Version Description') }}
                                                    {{ Form::text('description', $version->description, array('class'=>'form-control', 'placeholder' => 'i.e This is a world version', 'disabled')) }}
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
                            <div class="tab-pane" id="edit{{ $world->id }}">
                                @if(Session::has('errorEdit'.$world->id))
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <ul>
                                                    @foreach(Session::get('errorEdit'.$world->id)->all() as $errorMessage)
                                                        <li>{{ $errorMessage  }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                {{ Form::open(array('action' => array('WorldController@putWorld', $world->id), 'class' => 'form-horizontal', 'method' => 'PUT')) }}
                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorEdit'.$world->id) && Session::get('errorEdit'.$world->id)->get('name') != null ? 'has-error' : '' }}">
                                        {{ Form::label('name-label', 'World Name') }}
                                        {{ Form::text('name', $world->name, array('class'=>'form-control', 'placeholder' => 'i.e My World')) }}
                                    </div>
                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorEdit'.$world->id) && Session::get('errorEdit'.$world->id)->get('description') != null ? 'has-error' : '' }}">
                                        {{ Form::label('description-label', 'World Description') }}
                                        {{ Form::text('description', $world->description, array('class'=>'form-control', 'placeholder' => 'i.e This is my world')) }}
                                    </div>
                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorEdit'.$world->id) && Session::get('errorEdit'.$world->id)->get('directory') != null ? 'has-error' : '' }}">
                                        {{ Form::label('directory-label', 'World Directory') }}
                                        {{ Form::text('directory', $world->directory, array('class'=>'form-control', 'placeholder' => 'i.e myWorldDirectory')) }}
                                    </div>
                                    @if(Auth::user()->can('update_world'))
                                        <div style="margin-top:10px" class="form-group">
                                            <div class="col-md-12">
                                                {{ Form::submit('Save World', array('class'=>'btn btn-primary')) }}
                                            </div>
                                        </div>
                                    @endif
                                {{ Form::close() }}
                                <script>
                                    function ConfirmDelete(){
                                        return confirm("Are you sure you want to delete the world {{{ $world->name }}}?");
                                    }
                                </script>
                                @if(Auth::user()->can('delete_world'))
                                    {{ Form::open(array('action' => array('WorldController@deleteWorld', $world->id), 'class' => 'form-horizontal', 'method'=>'DELETE', 'onsubmit' => 'return ConfirmDelete()')) }}
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