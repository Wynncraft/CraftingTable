<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
    	<title>Minestack | Crafting Table</title>
    	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
    	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
        <style>
        body {
          padding-top: 20px;
          padding-bottom: 20px;
        }

        .navbar {
          margin-bottom: 20px;
        }
        </style>
    </head>
    <body>
        <div class="container">
            @if(App::environment() =='demo')
                <div class="row">
                    <div class="col-sm-12">
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <p>You are currently using Minestack in demo mode. Some functionality will be limited.</p>
                        </div>
                    </div>
                </div>
            @endif
            @yield('content')
        </div>

        <div class="footer">
            <div class="container">
            <p class="text-muted">&copy; Jodie Belgrave 2014 <a href="https://github.com/Minestack" target="_blank">GitHub Source</a></p>
            </div>
        </div>
    </body>

</html>
