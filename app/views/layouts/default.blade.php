<!doctype html>
<html lang="de">
<head>
    @include('includes.head')
</head>
<?php setlocale(LC_ALL,"de-DE"); ?>
<body class="scrolled">

    @include('includes.header')

    <section id="content">

            @yield('content')

    </section>

    <div id="footer">
        @include('includes.footer')
    </div>    
</body>
</html>