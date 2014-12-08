<nav class="navbar navbar-default" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="{{ URL::to('/')  }}">Minestack</a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav">

            @if(Auth::check())
                @if(Auth::user()->can('read_network'))
                    <li class="{{ $navBarPage == "networks" ? 'active' : '' }}"><a href="{{ URL::to('/')  }}">Networks</a></li>
                @endif

                @if(Auth::user()->can('read_bungeetype'))
                    <li class="{{ $navBarPage == "bungeetypes" ? 'active' : '' }}"><a href="{{ URL::to('/bungeetypes')  }}">Bungee Types</a></li>
                @endif

                @if(Auth::user()->can('read_servertype'))
                    <li class="{{ $navBarPage == "servertypes" ? 'active' : '' }}"><a href="{{ URL::to('/servertypes')  }}">Server Types</a></li>
                @endif

                @if(Auth::user()->can('read_world'))
                    <li class="{{ $navBarPage == "worlds" ? 'active' : '' }}"><a href="{{ URL::to('/worlds')  }}">Worlds</a></li>
                @endif

                @if(Auth::user()->can('read_plugin'))
                    <li class="{{ $navBarPage == "plugins" ? 'active' : '' }}"><a href="{{ URL::to('/plugins')  }}">Plugins</a></li>
                @endif

                @if(Auth::user()->can('read_node'))
                    <li class="{{ $navBarPage == "nodes" ? 'active' : '' }}"><a href="{{ URL::to('/nodes')  }}">Nodes</a></li>
                @endif

                @if(Auth::user()->can('read_group') || Auth::user()->can('read_user'))
                    <li class="dropdown {{ $navBarPage == "groups" || $navBarPage == "users" ? 'active' : '' }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Permissions<span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            @if(Auth::user()->can('read_group'))
                                <li class="{{ $navBarPage == "groups" ? 'active' : '' }}"><a href="{{ URL::to('/groups') }}">Groups</a></li>
                            @endif
                            @if(Auth::user()->can('read_user'))
                                <li class="{{ $navBarPage == "users" ? 'active' : '' }}"><a href="{{ URL::to('/users')  }}">Users</a></li>
                            @endif
                        </ul>
                    </li>
                @endif
            @else
                <li class="{{ $navBarPage == "login" ? 'active' : '' }}"><a href="{{ URL::to('/login')  }}">Sign In</a></li>
            @endif

        </ul>
        @if(Auth::check())
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown {{ $navBarPage == "user" || $navBarPage == "logout" ? 'active' : '' }}">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Welcome back, {{ Auth::user()->username }}.<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li class="{{ $navBarPage == "user" ? 'active' : '' }}"><a href="{{ URL::to('/users/'.Auth::user()->id) }}">Edit Account</a></li>
                        <li class="{{ $navBarPage == "logout" ? 'active' : '' }}"><a href="{{ URL::to('/logout')  }}">Sign Out</a></li>
                    </ul>
                </li>
            </ul>
        @endif
    </div>
</nav>
