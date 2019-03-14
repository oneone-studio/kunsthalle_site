<!doctype html>
<html lang="de">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1">
	<meta name="msapplication-square70x70logo" content="/images/smalltile.png" />
	<meta name="msapplication-square150x150logo" content="/images/mediumtile.png" />
	<meta name="msapplication-wide310x150logo" content="/images/widetile.png" />
	<meta name="msapplication-square310x310logo" content="/images/largetile.png" />
	<link rel="shortcut icon" href="images/favicon.png" type="image/x-icon" />
	<link rel="apple-touch-icon" href="images/apple-touch-icon-precomposed.png" />
	<link rel="shortcut icon" size="196x196" href="images/android-icon.png" />
	<title>Kunsthalle Bremen</title>
	<!-- fonts -->
	<!--<link rel="stylesheet" type="text/css" href="/fonts.googleapis.com/css?family=Roboto:300,400,500,700">
    <link rel="stylesheet" type="text/css" href="/fonts.googleapis.com/icon?family=Material+Icons">-->
	<link rel="stylesheet" type="text/css" href="fonts/ll_circular_bold_web/css/stylesheet.css" media="all" />
	<link rel="stylesheet" type="text/css" href="fonts/ll_circular_book_web/css/stylesheet.css" media="all" />
	<link rel="stylesheet" type="text/css" href="fonts/PFDekkaProWeb_Bold/PFDekkaPro-Bold.css" media="all" />
	<!-- bootstrap -->
	<link rel="stylesheet" type="text/css" href="bower_components/bootstrap/dist/css/bootstrap.min.css" media="all" />
	<!-- material-design -->
	<link rel="stylesheet" type="text/css" href="bower_components/bootstrap-material-design/dist/css/bootstrap-material-design.css"
					media="all" />
	<link rel="stylesheet" type="text/css" href="bower_components/bootstrap-material-design/dist/css/ripples.min.css" media="all"
	/>
	<!-- swiper -->
	<link rel="stylesheet" type="text/css" href="bower_components/swiper/dist/css/swiper.min.css" media="all" />
	<!-- custom -->
	<link rel="stylesheet" type="text/css" href="css/main.css" media="all" />
</head>

<body>
	<!-- header start -->
	<div id="header">
		<div class="center">
			<div id="menu-wrapper">
				<div id="logo-wrapper">
					<a href="#"><img class="logo" src="images/kunsthalle_bremen_logo.svg" alt="Kunsthalle Bremen" title="Kunsthalle Bremen" /></a>
				</div>
				<div class="opener">
					<a href="javascript:kunsthalle.closeMenu()" class="opener-close-link">
						<span class="icon icon-up"></span>
					</a>
					<a href="javascript:kunsthalle.openMenu()" class="opener-open-link">
						<span class="icon icon-down"></span>
					</a>
				</div>
				<div>
					<span class="link"><a href="javascript:kunsthalle.toggleMenu()">Menu</a></span>
				</div>
                <ul class="list-unstyled text-white">
                  @foreach($menu as $mi)
                     <!-- <li><a href="/{{strtolower(str_replace(' ', '-', $mi->title_en))}}">{{$mi->title_de}}</a></li> -->
                     <li><a href="/{{strtolower(str_replace(' ', '-', $mi->title_en))}}">{{$mi->title_de}}</a></li>
                  @endforeach  
				</ul>
			</div>
		</div>
		<div class="left">
			<a href="/plan-your-visit/your-visit" title="Ihr Besuch">
				<span class="icon icon-visit-us icon-is"></span>
				<span class="hidden-xs">
					Ihr Besuch
				</span>
			</a>
	        <a href="/calendar/besuch-planen" title="Besuch Planen">
	            <span class="icon icon-calendar icon-is"></span>
	            <span class="hidden-xs">
	                VERANSTALTUNGEN
	            </span>
	        </a>
		</div>
		<div class="right">
			<span class="link search">
                <a href="#">
                    <span class="icon icon-search"></span>
			</a>
			</span>
			<span class="link tickets text-white">
				<a href="https://www.mus-ticket.de/new/app/Shopping?ref=shp157393406&n=KHBremen" title="Tickets kaufen">Tickets</a>
			</span>
			<span class="link language text-white"><a href="#">DE</a></span>
		</div>
	</div>
	<!-- header end -->
