<!DOCTYPE html>
<html>
    <head>
        @section('title')<title>JapanTravel</title>@show
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0" />
        @section('head')
            @include('partials.head')

            <link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic,700italic" media="screen" rel="stylesheet" type="text/css" >
            <link rel="stylesheet" type="text/css" href="/css/portal.css">
        @show
    </head>

    <body>
        <header>
            <nav id="topnav" class="navbar navbar-inverse">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="http://en.japantravel.com" title="JapanTravel">
                            <img class="logo hidden-xs" src="http://a3.cdn.japantravel.com/images/jt-logo.png">
                            <img class="title hidden-xs" src="http://a3.cdn.japantravel.com/images/jt-text.png">
                            <img class="logo-xs visible-xs" src="http://a1.cdn.japantravel.com/images/jt-logo-small.png">
                        </a>
                    </div>
                </div>
            </nav>
            <nav id="mainnav" class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
                            <span class="sr-only">{{ translate('toggle_navigation') }}</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div class="collapse navbar-collapse" id="navbar-collapse">
                        <ul class="nav navbar-nav navbar-right">
                            @if (isset($current_user))
                                <form id="deleteSession" method="post" action="/session">
                                    <input type="hidden" name="_METHOD" value="DELETE">
                                    <ul class="nav navbar-nav navbar-right">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ $current_user->name }} <span class="caret"></span></a>
                                            <ul class="dropdown-menu">
                                                <li><a href="#" onclick="$('form#deleteSession').submit(); return false;">{{ translate('logout_button') }}</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </form>
                            @else
                                <li class="dropdown">
                                    <a href="/accounts">{{ translate('register_button') }}</a></li>
                                </li>
                                <li class="dropdown">
                                    <a href="/session">{{ translate('login_button') }}</a></li>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <div class="container">
            @if (isset($flash_message['success']))
                <div class="alert alert-success">
                    {{ $flash_message['success'] }}
                </div>
            @endif

            @if (isset($flash_message['errors']))
                <div class="alert alert-danger alert-important">
                    <ul>
                        @foreach ($flash_message['errors'] as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>

        <footer>
            ...
        </footer>

        @section('scripts')
            <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
            <script src="/js/app.js" type="text/javascript"></script>
        @show
    </body>
</html>
