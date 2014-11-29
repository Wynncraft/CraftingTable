@extends('layouts.master')

@section('content')
@include('navbars.topnav', array('navBarPage'=>'nodes'))

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

@if(Auth::user()->can('read_node'))
    <div class="panel-group" id="accordion">
        @if(Auth::user()->can('create_node'))
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseAdd">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add User
                            <small>Click to add a new node</small>
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
                        {{ Form::open(array('action' => array('NodeController@postNode', true), 'class' => 'form-horizontal')) }}
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('name') != null ? 'has-error' : '' }}">
                                {{ Form::text('name', '', array('class'=>'form-control', 'placeholder' => 'name')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('address') != null ? 'has-error' : '' }}">
                                {{ Form::text('address', '', array('class'=>'form-control', 'placeholder' => '172.16.0.1')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('ram') != null ? 'has-error' : '' }}">
                                {{ Form::password('ram', array('class'=>'form-control', 'placeholder' => '8192')) }}
                            </div>
                            <div style="margin-top:10px" class="form-group">
                                <div class="col-md-12">
                                    {{ Form::submit('Create Node', array('class'=>'btn btn-success')) }}
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        @endif
        @foreach(Node::all() as $node)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $node->id }}">
                            {{ $node->name }}
                            <small>{{ $node->address }}</small>
                        </a>
                    </h4>
                </div>
                <div id="collapse{{ $node->id }}" class="panel-collapse collapse {{ Session::has('error'.$node->id) ? 'in' : '' }}">
                    <div class="panel-body">
                        @if(Session::has('error'.$node->id))
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        <ul>
                                            @foreach(Session::get('error'.$node->id)->all() as $errorMessage)
                                                <li>{{ $errorMessage  }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                        {{ Form::open(array('action' => array('NodeController@putNode', $node->id), 'class' => 'form-horizontal', 'method' => 'PUT')) }}
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('error'.$node->id) && Session::get('error'.$node->id)->get('name') != null ? 'has-error' : '' }}">
                                {{ Form::text('name', $user->name, array('class'=>'form-control', 'placeholder' => 'name')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('error'.$node->id) && Session::get('error'.$node->id)->get('address') != null ? 'has-error' : '' }}">
                                {{ Form::text('address', $user->address, array('class'=>'form-control', 'placeholder' => 'address', 'disabled')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('error'.$node->id) && Session::get('error'.$node->id)->get('ram') != null ? 'has-error' : '' }}">
                                {{ Form::text('ram', $user->ram, array('class'=>'form-control', 'placeholder' => 'ram')) }}
                            </div>
                            <div style="margin-top:10px" class="form-group">
                                <div class="col-md-12">
                                    {{ Form::submit('Save', array('class'=>'btn btn-primary')) }}
                                </div>
                            </div>
                        {{ Form::close() }}
                        <script>
                            function ConfirmDelete(){
                                return confirm("Are you sure you want to delete the node {{ $node->name }}?");
                            }
                        </script>
                        @if(Auth::user()->can('delete_node'))
                            {{ Form::open(array('action' => array('NodeController@deleteNode', $node->id), 'class' => 'form-horizontal', 'method'=>'DELETE', 'onsubmit' => 'return ConfirmDelete()')) }}
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