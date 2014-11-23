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
            @if($navBarPage == "home")
                <li class="active"><a href="{{ URL::to('/')  }}">Home</a></li>
            @else
                <li><a href="{{ URL::to('/')  }}">Home</a></li>
            @endif

            @if(Auth::check())
                @if(Auth::user()->can('see_gnodes'))
                    @if($navBarPage == "gnodes")
                        <li class="active"><a>Global Nodes</a></li>
                    @else
                        <li><a>Global Nodes</a></li>
                    @endif
                @endif

                @if(Auth::user()->can('see_users'))
                    @if($navBarPage == "users")
                        <li class="active"><a href="{{ URL::to('/users') }}">Users</a></li>
                    @else
                        <li><a href="{{ URL::to('/users') }}">Users</a></li>
                    @endif
                @endif

                @if(Auth::user()->can('see_groups'))
                    @if($navBarPage == "groups")
                        <li class="active"><a href="{{ URL::to('/groups') }}">Groups</a></li>
                    @else
                        <li><a href="{{ URL::to('/groups') }}">Groups</a></li>
                     @endif
                @endif

                @if(Auth::user()->can('see_options'))
                    @if($navBarPage == "groups")
                        <li class="active"><a href="{{ URL::to('/options') }}">Options</a></li>
                    @else
                        <li><a href="{{ URL::to('/options') }}">Options</a></li>
                    @endif
                @endif

                @if($navBarPage == "logout")
                    <li class="active"><a href="{{ URL::to('/logout')  }}">Sign Out</a></li>
                @else
                    <li><a href="{{ URL::to('/logout')  }}">Sign Out</a></li>
                @endif
            @else
                @if($navBarPage == "login")
                    <li class="active"><a href="{{ URL::to('/login')  }}">Sign In</a></li>
                @else
                    <li><a href="{{ URL::to('/login')  }}">Sign In</a></li>
                @endif
            @endif

        </ul>
        @if(Auth::check())
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Welcome back {{ Auth::user()->username }}<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        @if($navBarPage == "user")
                            <li class="active"><a href="{{ URL::to('/users/'.Auth::user()->id) }}">Edit Account</a></li>
                        @else
                            <li><a href="{{ URL::to('/users/'.Auth::user()->id) }}">Edit Account</a></li>
                        @endif
                    </ul>
                </li>
            </ul>
        @endif
    </div>
</nav>