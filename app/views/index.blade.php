@extends('layouts.master')

@section('content')
@include('navbars.topnav', array('navBarPage'=>'networks'))


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

@if(Auth::user()->can('read_network'))
    <div class="panel-group" id="accordion">
        @if(Auth::user()->can('create_network'))
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseAdd">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Network
                            <small>Click to add a new network</small>
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
                        {{ Form::open(array('action' => 'NetworkController@postNetwork', 'class' => 'form-horizontal')) }}

                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('name') != null ? 'has-error' : '' }}">
                                {{ Form::text('name', '', array('class'=>'form-control', 'placeholder' => 'network name', 'maxlength' => '100')) }}
                            </div>

                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('description') != null ? 'has-error' : '' }}">
                                {{ Form::text('description', '', array('class'=>'form-control', 'placeholder' => 'network description', 'maxlength' => '255')) }}
                            </div>

                            <div style="margin-top:10px" class="form-group">
                                <div class="col-md-12">
                                    {{ Form::submit('Add Network', array('class'=>'btn btn-success')) }}
                                </div>
                            </div>

                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        @endif
        @foreach(Network::all() as $network)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $network->id }}">
                            {{ $network->name }}
                            <small>{{ $network->description }}</small>
                        </a>
                    </h4>
                </div>
                <div id="collapse{{ $network->id }}" class="panel-collapse collapse {{ Session::has('error'.$network->id) ? 'in' : '' }}">
                    <div class="panel-body">
                         @if(Session::has('error'.$network->id))
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            <ul>
                                                @foreach(Session::get('error'.$network->id)->all() as $errorMessage)
                                                    <li>{{ $errorMessage  }}</li>
                                                @endforeach
                                            </ul>
                                    </div>
                                </div>
                            </div>
                         @endif
                        <ul class="nav nav-tabs">
                            <li role="presentation" class="active"><a href="#stats{{ $network->id }}" data-toggle="tab">Stats</a></li>
                            <li role="presentation"><a href="#plugins{{ $network->id }}" data-toggle="tab">Plugins</a></li>
                            <li role="presentation"><a href="#servertypes{{ $network->id }}" data-toggle="tab">Server Types</a></li>
                            <li role="presentation"><a href="#nodes{{ $network->id }}" data-toggle="tab">Nodes</a></li>
                            <li role="presentation"><a href="#edit{{ $network->id }}" data-toggle="tab">Edit</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="stats{{ $network->id }}">stats</div>
                            <div class="tab-pane" id="plugins{{ $network->id }}">plugins</div>
                            <div class="tab-pane" id="servertypes{{ $network->id }}">server types</div>
                            <div class="tab-pane" id="nodes{{ $network->id }}">nodes</div>
                            <div class="tab-pane" id="edit{{ $network->id }}">
                                {{ Form::open(array('action' => array('NetworkController@putNetwork', $network->id), 'class' => 'form-horizontal', 'method' => 'PUT')) }}

                                    <div style="margin-top: 15px; margin-bottom: 25px" class="input-group {{ Session::has('error'.$network->id) && Session::get('error'.$network->id)->get('name') != null ? 'has-error' : '' }}">
                                        {{ Form::text('name', $network->name, array('class'=>'form-control', 'placeholder' => 'network name', 'maxlength' => '100')) }}
                                    </div>

                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('error'.$network->id) && Session::get('error'.$network->id)->get('description') != null ? 'has-error' : '' }}">
                                        {{ Form::text('description', $network->description, array('class'=>'form-control', 'placeholder' => 'network description', 'maxlength' => '255')) }}
                                    </div>

                                    <div style="margin-top:10px" class="form-group">
                                        <div class="col-md-12">
                                            {{ Form::submit('Save Network', array('class'=>'btn btn-success')) }}
                                        </div>
                                    </div>

                                {{ Form::close() }}

                                {{ Form::open(array('action' => array('NetworkController@deleteNetwork', $network->id), 'class' => 'form-horizontal', 'method'=>'DELETE')) }}
                                    <div style="margin-top:10px" class="form-group">
                                        <div class="col-md-12">
                                            {{ Form::submit('Delete', array('class'=>'btn btn-danger')) }}
                                        </div>
                                    </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@stop