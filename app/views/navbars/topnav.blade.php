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
            @if($topNavPage == "home")
                <li class="active"><a href="{{ URL::to('/')  }}">Home</a></li>
            @else
                <li><a href="{{ URL::to('/')  }}">Home</a></li>
            @endif

            @if($topNavPage == "gnodes")
                <li class="active"><a>Global Nodes</a></li>
            @else
                <li><a>Global Nodes</a></li>
            @endif

            @if(Auth::check())
                @if($topNavPage == "logout")
                    <li class="active"><a href="{{ URL::to('/logout')  }}">Sign Out</a></li>
                @else
                    <li><a href="{{ URL::to('/logout')  }}">Sign Out</a></li>
                @endif
            @else
                @if($topNavPage == "login")
                    <li class="active"><a href="{{ URL::to('/login')  }}">Sign In</a></li>
                @else
                    <li><a href="{{ URL::to('/login')  }}">Sign In</a></li>
                @endif
            @endif

        </ul>
    </div>
</nav>