<!DOCTYPE html>
<html lang="en">
    <head>
        @include( 'views.layout.head')
    </head>
    <body data-spy="scroll" data-target="#navbar" data-offset="100">
        @include ('views.layout.header')
            @yield('content')
        @include('views.layout.footer')
    </body>

</html>
