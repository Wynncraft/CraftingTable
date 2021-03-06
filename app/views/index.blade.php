@extends('layouts.master')

@section('content')
@include('navbars.topnav', array('navBarPage'=>'networks'))

<script>
    function ConfirmDeleteServerType(servertype){
            return confirm("Are you sure you want to delete the server type "+servertype+"?");
    }

    function ConfirmDeleteBungeeType(bungeetype){
        return confirm("Are you sure you want to delete the bungee type "+bungeetype+"?");
    }

    function ConfirmDeleteNode(node){
            return confirm("Are you sure you want to delete the node "+node+"?");
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
                                                    <li>{{{ $errorMessage  }}}</li>
                                                @endforeach
                                            </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                        {{ Form::open(array('action' => 'NetworkController@postNetwork', 'class' => 'form-horizontal')) }}

                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('name') != null ? 'has-error' : '' }}">
                                {{ Form::label('name-label', 'Network Name') }}
                                {{ Form::text('name', '', array('class'=>'form-control', 'placeholder' => 'i.e My Network', 'maxlength' => '100')) }}
                            </div>

                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('description') != null ? 'has-error' : '' }}">
                                {{ Form::label('description-label', 'Network Description') }}
                                {{ Form::text('description', '', array('class'=>'form-control', 'placeholder' => 'i.e This is my network', 'maxlength' => '255')) }}
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
                            {{{ $network->name }}}
                            <small>{{{ $network->description }}}</small>
                        </a>
                    </h4>
                </div>
                <div id="collapse{{ $network->id }}" class="panel-collapse collapse {{ Session::has('open'.$network->id) ? 'in' : '' }}">
                    <div class="panel-body">
                        @if($network->overProvisioned() == true)
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <p>This network is currently over provisioned. Please consider adding more nodes or decrease the amount of servers for optimal performance.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($network->defaultServerType() == null)
                            <div class="row">
                                <div class="col-sm-12">
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <p>There is currently no default server type. This network will not function correctly without a default server type.</p>
                                </div>
                                </div>
                            </div>
                        @endif
                        @if($network->hasBungee() == false)
                            <div class="row">
                                <div class="col-sm-12">
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <p>This network is currently unjoinable. Please add a node with a bungee type.</p>
                                </div>
                                </div>
                            </div>
                        @endif
                        <ul class="nav nav-tabs">
                            <li role="presentation" class="active"><a href="#stats{{ $network->id }}" data-toggle="tab">Stats</a></li>
                            <li role="presentation"><a href="#servertypes{{ $network->id }}" data-toggle="tab" style="{{ Session::has('errorAddServerType'.$network->id) == true || Session::has('errorUpdateServerType'.$network->id) == true ? 'color:red; font-weight:bold;' : ''}}">Server Types</a></li>
                            <li role="presentation"><a href="#bungeetypes{{ $network->id }}" data-toggle="tab" style="{{ Session::has('errorAddBungeeType'.$network->id) == true || Session::has('errorUpdateBungeeType'.$network->id) == true ? 'color:red; font-weight:bold;' : ''}}">Bungee Types</a></li>
                            <li role="presentation"><a href="#forcedhosts{{ $network->id }}" data-toggle="tab" style="{{ Session::has('errorAddForcedHost'.$network->id) == true || Session::has('errorUpdateForcedHost'.$network->id) == true ? 'color:red; font-weight:bold;' : ''}}">Forced Hosts</a></li>
                            <li role="presentation"><a href="#nodes{{ $network->id }}" data-toggle="tab" style="{{ Session::has('errorAddNode'.$network->id) == true ? 'color:red; font-weight:bold;' : ''}}">Nodes</a></li>
                            <li role="presentation"><a href="#edit{{ $network->id }}" data-toggle="tab" style="{{ Session::has('errorEdit'.$network->id) == true ? 'color:red; font-weight:bold;' : ''}}">Edit</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="stats{{ $network->id }}">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <h4 class="text-center">Online Players</h4>
                                        <p class="text-center"><span class="text-muted">{{ $network->getOnlinePlayers() }} / {{ $network->getTotalPlayers() }}</span></p>
                                    </div>
                                    <div class="col-xs-4">
                                        <h4 class="text-center">Memory Usage</h4>
                                        <p class="text-center"><span class="text-muted">{{ $network->getUsedRam() }} MB / {{ $network->getTotalRam() }} MB</span></p>
                                    </div>
                                    <div class="col-xs-4">
                                        <h4 class="text-center">Provisioned Memory</h4>
                                        <p class="text-center"><span class="text-muted">{{ $network->getProvisionedRam() }} MB / {{ $network->getTotalRam() }} MB</span></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h4>Online Bungees</h4>
                                        <table class="table table-striped table-bordered table-hover">
                                            <thread>
                                                <tr>
                                                    <th>Bungee Type</th>
                                                    <th>Node</th>
                                                    <th>Public IP Address</th>
                                                    <th>Manage</th>
                                                </tr>
                                            </thread>
                                            <tbody>
                                                @foreach($network->bungees()->get()->all() as $bungee)
                                                    <tr>
                                                        <td>{{{ $bungee->bungeetype()->name }}}</td>
                                                        <td>{{{ $bungee->node()->name }}}</td>
                                                        <td>{{{ $bungee->publicaddress()->publicAddress }}}</td>
                                                        <td>Button</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <h4>Online Servers</h4>
                                        <table class="table table-striped table-bordered table-hover">
                                            <thread>
                                                <tr>
                                                    <th>Server Type</th>
                                                    <th>Server Number</th>
                                                    <th>Node</th>
                                                    <th>Port</th>
                                                    <th>Manage</th>
                                                </tr>
                                            </thread>
                                            <tbody>
                                                @foreach($network->servers()->get()->all() as $server)
                                                    <tr>
                                                        <td>{{{ $server->servertype()->name }}}</td>
                                                        <td>{{{ $server->number }}}</td>
                                                        <td>{{{ $server->node() != null ? $server->node()->name : '' }}}</td>
                                                        <td>{{{ $server->port }}}</td>
                                                        <td>Button</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="servertypes{{ $network->id }}">
                                @if(Session::has('errorAddServerType'.$network->id))
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <ul>
                                                    @foreach(Session::get('errorAddServerType'.$network->id)->all() as $errorMessage)
                                                        <li>{{ $errorMessage  }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                    @if(Session::has('errorUpdateServerType'.$network->id))
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="alert alert-danger alert-dismissible">
                                                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                    <ul>
                                                        @foreach(Session::get('errorUpdateServerType'.$network->id)->all() as $errorMessage)
                                                            <li>{{ $errorMessage  }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                {{ Form::open(array('action' => array('NetworkController@putServerType', $network->id), 'class' => 'form-horizontal', 'method' => 'PUT')) }}
                                    <table style="margin-top: 10px" class="table table-bordered">
                                        <thread>
                                            <tr>
                                                <th>Server Type Name</th>
                                                <th>Amount</th>
                                                <th>Default</th>
                                                <th>Manual Start</th>
                                                <th>Remove</th>
                                            </tr>
                                        </thread>
                                        <tbody>
                                            @foreach($network->servertypes()->get() as $servertype)
                                                <tr>
                                                    <td>{{ Form::text($network->id.'name'.$servertype->id, $servertype->servertype()->name, array('class'=>'form-control', 'disabled')) }}</td>
                                                    <td>{{ Form::number($network->id.'amount'.$servertype->id, $servertype->amount, array('class'=>'form-control', 'min'=>0)) }}</td>
                                                    <td>{{ Form::checkbox($network->id.'default'.$servertype->id, '1', $servertype->defaultServerType, array('class'=>'form-control')) }}</td>
                                                    <td>{{ Form::checkbox($network->id.'manual'.$servertype->id, '1', $servertype->manualStart, array('class'=>'form-control')) }}</td>
                                                    <td><a href="{{ action("NetworkController@deleteServerType", [$network->id, $servertype->id]) }}" class="btn btn-danger" onclick="return ConfirmDeleteServerType('{{ $servertype->servertype()->name }}')"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div style="margin-top:10px" class="form-group">
                                        <div class="col-md-12">
                                            {{ Form::submit('Save Server Types', array('class'=>'btn btn-success')) }}
                                        </div>
                                    </div>
                                {{ Form::close() }}
                                {{ Form::open(array('action' => array('NetworkController@postServerType', $network->id), 'class' => 'form-horizontal', 'method' => 'POST')) }}
                                    <div style="margin-bottom: 25px" class="input-group">
                                        {{ Form::label('servertype-label', 'Server Type') }}
                                        <select name='servertype' class="form-control" id="servertypeList">
                                            <option selected value="-1">Please select a server type</option>
                                            @foreach(ServerType::all() as $servertype)
                                                <option value="{{ $servertype->id }}">{{{ $servertype->name }}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="margin-top:10px" class="form-group">
                                        <div class="col-md-12">
                                            {{ Form::submit('Add Server Type', array('class'=>'btn btn-primary')) }}
                                        </div>
                                    </div>
                                {{ Form::close() }}
                                    <table style="margin-top: 10px" class="table table-bordered">
                                        <thread>
                                            <tr>
                                                <th>Manual Server Type Name</th>
                                                <th>Host Address</th>
                                                <th>Host Port</th>
                                                <th>Remove</th>
                                            </tr>
                                        </thread>
                                        <tbody>
                                        @foreach($network->manualservertypes()->get() as $servertype)
                                            <tr>
                                                <td>{{ Form::text($network->id.'name'.$servertype->id, $servertype->name, array('class'=>'form-control', 'disabled')) }}</td>
                                                <td>{{ Form::text($network->id.'address'.$servertype->id, $servertype->address, array('class'=>'form-control', 'disabled')) }}</td>
                                                <td>{{ Form::text($network->id.'port'.$servertype->id, $servertype->port, array('class'=>'form-control', 'disabled')) }}</td>
                                                <td><a href="{{ action("NetworkController@deleteManualServerType", [$network->id, $servertype->id]) }}" class="btn btn-danger" onclick="return ConfirmDeleteServerType('{{ $servertype->name }}')"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        </tbody>
                                    </table>
                                {{ Form::open(array('action' => array('NetworkController@postManualServerType', $network->id), 'class' => 'form-horizontal', 'method' => 'POST')) }}
                                    <div style="margin-bottom: 25px" class="input-group">
                                        {{ Form::label('name-label', 'Manual Server Type Name') }}
                                        {{ Form::text('name', '', array('class'=>'form-control')) }}
                                    </div>
                                    <div style="margin-bottom: 25px" class="input-group">
                                        {{ Form::label('address-label', 'Host Address') }}
                                        {{ Form::text('address', '', array('class'=>'form-control')) }}
                                    </div>
                                    <div style="margin-bottom: 25px" class="input-group">
                                        {{ Form::label('port-label', 'Host Port') }}
                                        {{ Form::text('port', '', array('class'=>'form-control')) }}
                                    </div>
                                    <div style="margin-top:10px" class="form-group">
                                        <div class="col-md-12">
                                            {{ Form::submit('Add Manual Server Type', array('class'=>'btn btn-primary')) }}
                                        </div>
                                    </div>
                                {{ Form::close() }}
                            </div>
                            <div class="tab-pane" id="bungeetypes{{ $network->id }}">
                                @if(Session::has('errorAddBungeeType'.$network->id))
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <ul>
                                                    @foreach(Session::get('errorAddBungeeType'.$network->id)->all() as $errorMessage)
                                                        <li>{{ $errorMessage  }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if(Session::has('errorUpdateBungeeType'.$network->id))
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <ul>
                                                    <li>{{ Session::get('errorUpdateBungeeType'.$network->id) }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                    {{ Form::open(array('action' => array('NetworkController@putBungeeType', $network->id), 'class' => 'form-horizontal', 'method' => 'PUT')) }}
                                    <table style="margin-top: 10px" class="table table-bordered">
                                        <thread>
                                            <tr>
                                                <th>Bungee Type Name</th>
                                                <th>Amount</th>
                                                <th>IP Addresses</th>
                                                <th>Remove</th>
                                            </tr>
                                        </thread>
                                        <tbody>
                                        @foreach($network->bungeetypes()->get() as $bungeetype)
                                            <tr>
                                                <td>{{ Form::text($network->id.'name'.$bungeetype->id, $bungeetype->bungeetype()->name, array('class'=>'form-control', 'disabled')) }}</td>
                                                <td>{{ Form::number($network->id.'amount'.$bungeetype->id, $bungeetype->amount, array('class'=>'form-control', 'min'=>0)) }}</td>
                                                <td>
                                                    <ul>
                                                        @foreach($bungeetype->nodes() as $nodeId)
                                                            <li>{{ Node::find($nodeId)->name }}
                                                                <ul>
                                                                    @foreach($bungeetype->addresses()->where('node_id', '=', $nodeId) as $addressInfo)
                                                                        <li>{{ $addressInfo->nodePublicAddress()->publicAddress }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                                <td><a href="{{ action("NetworkController@deleteBungeeType", [$network->id, $bungeetype->id]) }}" class="btn btn-danger" onclick="return ConfirmDeleteBungeeType('{{ $bungeetype->bungeetype()->name }}')"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <div style="margin-top:10px" class="form-group">
                                        <div class="col-md-12">
                                            {{ Form::submit('Save Bungee Types', array('class'=>'btn btn-success')) }}
                                        </div>
                                    </div>
                                    {{ Form::close() }}

                                    {{ Form::open(array('action' => array('NetworkController@postBungeeType', $network->id), 'class' => 'form-horizontal', 'method' => 'POST')) }}
                                    <div style="margin-bottom: 25px" class="input-group">
                                        {{ Form::label('bungeetype-label', 'Bungee Type Name') }}
                                        <select name='bungeetype' class="form-control" id="bungeetypeList">
                                            <option selected value="-1">Please select a bungee type</option>
                                            @foreach(BungeeType::all() as $bungeetype)
                                                <option value="{{ $bungeetype->id }}">{{{ $bungeetype->name }}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="margin-top:10px" class="form-group">
                                        <div class="col-md-12">
                                            {{ Form::submit('Add Bungee Type', array('class'=>'btn btn-primary')) }}
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                            </div>
                            <div class="tab-pane" id="forcedhosts{{ $network->id }}">
                                @if(Session::has('errorAddForcedHost'.$network->id))
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <ul>
                                                    @foreach(Session::get('errorAddForcedHost'.$network->id)->all() as $errorMessage)
                                                        <li>{{ $errorMessage  }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if(Session::has('errorUpdateForcedHost'.$network->id))
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <ul>
                                                    <li>{{ Session::get('errorUpdateForcedHost'.$network->id) }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{ Form::open(array('action' => array('NetworkController@putForcedHost', $network->id), 'class' => 'form-horizontal', 'method' => 'PUT')) }}
                                <table style="margin-top: 10px" class="table table-bordered">
                                    <thread>
                                        <tr>
                                            <th>Forced Host</th>
                                            <th>Server Type</th>
                                            <th>Remove</th>
                                        </tr>
                                    </thread>
                                    <tbody>
                                    @foreach($network->forcedhosts()->get() as $forcedhost)
                                        <tr>
                                            <td>{{ Form::text($network->id.'host'.$forcedhost->id, $forcedhost->host, array('class'=>'form-control', 'disabled')) }}</td>
                                            <td>
                                                <div style="margin-bottom: 25px" class="input-group">
                                                    <select name='{{ $network->id.'servertype'.$forcedhost->id }}' class="form-control" id="{{ $network->id.'servertype'.$forcedhost->id }}">
                                                        <option selected value="-1">Please select a server type</option>
                                                        @foreach($network->servertypes()->get() as $servertype)
                                                            @if($forcedhost->server_type_id == $servertype->servertype()->id)
                                                                <option selected value="{{ $servertype->servertype()->id }}">{{{ $servertype->servertype()->name }}}</option>
                                                            @else
                                                                <option value="{{ $servertype->servertype()->id }}">{{{ $servertype->servertype()->name }}}</option>
                                                            @endif
                                                        @endforeach
                                                        @foreach($network->manualservertypes()->get() as $servertype)
                                                            @if($forcedhost->server_type_id == $servertype->id)
                                                                <option selected value="{{ $servertype->id }}">{{{ $servertype->name }}}</option>
                                                            @else
                                                                <option value="{{ $servertype->id }}">{{{ $servertype->name }}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <td><a href="{{ action("NetworkController@deleteForcedHost", [$network->id, $forcedhost->id]) }}" class="btn btn-danger" onclick="return ConfirmDeleteForcedHost('{{ $forcedhost->host }}')"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div style="margin-top:10px" class="form-group">
                                    <div class="col-md-12">
                                        {{ Form::submit('Save Forced Hosts', array('class'=>'btn btn-success')) }}
                                    </div>
                                </div>
                                {{ Form::close() }}

                                {{ Form::open(array('action' => array('NetworkController@postForcedHost', $network->id), 'class' => 'form-horizontal', 'method' => 'POST')) }}
                                <div style="margin-bottom: 25px" class="input-group">
                                    {{ Form::label('host-label', 'Forced Host') }}
                                    {{ Form::text('host', '', array('placeholder'=>'build.example.com', 'class'=>'form-control')) }}
                                </div>
                                <div style="margin-top:10px" class="form-group">
                                    <div class="col-md-12">
                                        {{ Form::submit('Add Forced Host', array('class'=>'btn btn-primary')) }}
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                            <div class="tab-pane" id="nodes{{ $network->id }}">
                                @if(Session::has('errorAddNode'.$network->id))
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <ul>
                                                    @foreach(Session::get('errorAddNode'.$network->id)->all() as $errorMessage)
                                                        <li>{{ $errorMessage  }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <table style="margin-top: 10px" class="table table-striped table-bordered table-hover">
                                    <thread>
                                        <tr>
                                            <th>Node Name</th>
                                            <th>Remove</th>
                                        </tr>
                                    </thread>
                                    <tbody>
                                        @foreach($network->nodes()->get() as $node)
                                            <tr>
                                                <td>{{{ $node->node()->name }}}</td>
                                                <td><a href="{{ action("NetworkController@deleteNode", [$network->id, $node->id]) }}" class="btn btn-danger" onclick="return ConfirmDeleteNode('{{ $node->node()->name }}')"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ Form::open(array('action' => array('NetworkController@postNode', $network->id), 'class' => 'form-horizontal', 'method' => 'POST')) }}
                                    <div style="margin-bottom: 25px" class="input-group">
                                        {{ Form::label('node-label', 'Node Name') }}
                                        <select name='node' class="form-control nodeList" id="{{$network->id}}">
                                            <option selected value="-1">Please select a node</option>
                                            @foreach(Node::all() as $node)
                                                <option value="{{ $node->id }}">{{{ $node->name }}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div style="margin-top:10px" class="form-group">
                                        <div class="col-md-12">
                                            {{ Form::submit('Add Node', array('class'=>'btn btn-primary')) }}
                                        </div>
                                    </div>
                                {{ Form::close() }}
                            </div>
                            <div class="tab-pane" id="edit{{ $network->id }}">
                                @if(Session::has('errorEdit'.$network->id))
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <ul>
                                                    @foreach(Session::get('errorEdit'.$network->id)->all() as $errorMessage)
                                                        <li>{{ $errorMessage  }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                {{ Form::open(array('action' => array('NetworkController@putNetwork', $network->id), 'class' => 'form-horizontal', 'method' => 'PUT')) }}

                                    <div style="margin-top: 15px; margin-bottom: 25px" class="input-group {{ Session::has('errorEdit'.$network->id) && Session::get('errorEdit'.$network->id)->get('name') != null ? 'has-error' : '' }}">
                                        {{ Form::label('name-label', 'Network Name') }}
                                        {{ Form::text('name', $network->name, array('class'=>'form-control', 'placeholder' => 'i.e My Network', 'maxlength' => '100')) }}
                                    </div>

                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorEdit'.$network->id) && Session::get('errorEdit'.$network->id)->get('description') != null ? 'has-error' : '' }}">
                                        {{ Form::label('description-label', 'Network Description') }}
                                        {{ Form::text('description', $network->description, array('class'=>'form-control', 'placeholder' => 'i.e This is my network', 'maxlength' => '255')) }}
                                    </div>

                                    @if(Auth::user()->can('update_network'))
                                        <div style="margin-top:10px" class="form-group">
                                            <div class="col-md-12">
                                                {{ Form::submit('Save Network', array('class'=>'btn btn-primary')) }}
                                            </div>
                                        </div>
                                    @endif

                                {{ Form::close() }}
                                <script>
                                    function ConfirmDelete(){
                                        return confirm("Are you sure you want to delete the network {{{ $network->name }}}?");
                                    }
                                </script>
                                @if(Auth::user()->can('delete_network'))
                                    {{ Form::open(array('action' => array('NetworkController@deleteNetwork', $network->id), 'class' => 'form-horizontal', 'method'=>'DELETE', 'onsubmit' => 'return ConfirmDelete()')) }}
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