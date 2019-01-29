<!doctype html>
<html lang="de">
<html>
<head>
    @include('includes.head')
</head>
<?php setlocale(LC_ALL,"de-DE"); ?>
<body class="scrolled">

    @include('includes.header')

    <section id="content" class="logo-hidden">

            @yield('content')

    </section>

    <div id="footer">
        @include('includes.footer')
    </div>    
</body>
</html>