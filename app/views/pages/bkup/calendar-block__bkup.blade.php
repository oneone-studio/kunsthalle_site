<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1">
		<meta name="msapplication-square70x70logo" content="/images/smalltile.png" />
		<meta name="msapplication-square150x150logo" content="/images/mediumtile.png" />
		<meta name="msapplication-wide310x150logo" content="/images/widetile.png" />
		<meta name="msapplication-square310x310logo" content="/images/largetile.png" />
    <link rel="shortcut icon" href="/images/favicon.png" type="image/x-icon">
    <link rel="apple-touch-icon" href="/images/apple-touch-icon-precomposed.png">
    <link rel="shortcut icon" size="196x196" href="/images/android-icon.png">
    <title>Kunsthalle Bremen</title>
    <!-- fonts -->
    <!--<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto:300,400,500,700">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/icon?family=Material+Icons">-->
    <link rel="stylesheet" type="text/css" href="/fonts/ll_circular_bold_web/css/stylesheet.css" media="all" />
    <link rel="stylesheet" type="text/css" href="/fonts/ll_circular_book_web/css/stylesheet.css" media="all" />
    <link rel="stylesheet" type="text/css" href="/fonts/PFDekkaProWeb_Bold/PFDekkaPro-Bold.css" media="all" />
    <!-- bootstrap -->
    <link rel="stylesheet" type="text/css" href="/bower_components/bootstrap/dist/css/bootstrap.min.css" media="all" />
    <!-- material-design -->
    <link rel="stylesheet" type="text/css" href="/bower_components/bootstrap-material-design/dist/css/bootstrap-material-design.css" media="all">
    <link rel="stylesheet" type="text/css" href="/bower_components/bootstrap-material-design/dist/css/ripples.min.css" media="all">
    <!-- swiper -->
    <link rel="stylesheet" type="text/css" href="/bower_components/swiper/dist/css/swiper.min.css" media="all">
    <!-- custom -->
    <link rel="stylesheet" type="text/css" href="/css/main.css" media="all" />
</head>

<body>
    <!-- header start -->
    <div id="header">
        <div class="center">
            <div id="menu-wrapper">
                <div id="logo-wrapper">
                    <a href="#"><img class="logo" src="/images/kunsthalle_bremen_logo.svg" alt="Kunsthalle Bremen" title="Kunsthalle Bremen" /></a>
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
                    <li><a href="#">Besuch planen</a></li>
                    <li><a href="#">Ausstellungen</a></li>
                    <li><a href="#">Sammlung</a></li>
                    <li><a href="#">Kunsthalle Bremen</a></li>
                    <li><a href="#">Mitmachen im Kunstverein</a></li>
                    <li><a href="#">Blog</a></li>
                </ul>
            </div>
        </div>
        <div class="left hidden-xs hidden-sm">
			<span class="icon icon-clock"></span>
            <span class="text-green">noch 5 Stunden offen</span>
        </div>
        <div class="right">
            <span class="link search">
                <a href="#">
                    <span class="icon icon-search"></span>
                </a>
            </span>
            <span class="link tickets text-white"><a href="#">Tickets</a></span>
            <span class="link language text-white"><a href="#">DE</a></span>
        </div>
    </div>
    <!-- header ende -->

    <!-- content start -->
    <div id="content">
        <div id="calender-wrapper">
            <div class="container-fluid">
                <ul class="list-inline">
                    <li><a href="#" class="btn btn-default btn-raised btn-lg active">Button large</a></li>
                    <li><a href="#" class="btn btn-default btn-raised">Button</a></li>
                    <li><a href="#" class="btn btn-default btn-raised btn-sm">Button small</a></li>
                </ul>
            </div>
            <section id="calendar">
                <div id="filter">
                    <div class="container-fluid mb-15">
                        <a href="#" class="open-filter">
                            <span class="icon icon-filter"></span>
                            Filter
                            <span class="icon icon-arrow icon-s icon-inline"></span>
                            <span class="filter-name">Alle</span>
                        </a>
                    </div>
                    <div class="menu panel-collapse collapse">
                        <div>
                            <div class="triangle"></div>
                            <ul class="list-unstyled">
                                <li><span class="icon icon-inline icon-check"></span> <a href="#" data-filter="all">Alle</a></li>
                                <li><span class="icon icon-inline"></span> <a href="#" data-filter=".filter-guidances">Führungen</a></li>
                                <li><span class="icon icon-inline"></span> <a href="#" data-filter=".filter-members">Mitglieder</a></li>
                                <li><span class="icon icon-inline"></span> <a href="#" data-filter=".filter-youngsters">Kinder und Jugendliche</a></li>
                                <li><span class="icon icon-inline"></span> <a href="#" data-filter=".filter-holiday-courses">Ferienkurse</a></li>
                                <li><span class="icon icon-inline"></span> <a href="#" data-filter=".filter-seminars">Seminare</a></li>
                                <li><span class="icon icon-inline"></span> <a href="#" data-filter=".filter-movies">Filme</a></li>
                                <li><span class="icon icon-inline"></span> <a href="#" data-filter=".filter-lectures">Vorträge</a></li>
                            </ul>
                            <div class="text-center">
                                <a href="#" class="close-filter">
                                    <span class="icon icon-close icon-red"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Month-Swiper start -->
                <div class="swiper-container control">
                    <div class="swiper-wrapper">
                    <?php $slideNo = 0; ?>    
                    @foreach($calendar as $k => $cal)
                       <?php ++$slideNo; ?>
                        <div class="swiper-slide slide_t_{{$slideNo}}">
                            <div class="date">
                                <span class="month">{{$cal['month']}}</span>
                                <span class="year">{{$cal['year']}}</span>
                            </div>
                        </div>

                    @endforeach
                    </div>
                    <div class="month-prev"><span class="icon icon-left icon-l" /></div>
                    <div class="month-next"><span class="icon icon-right icon-l" /></div>
                </div>

                <!-- Month-Swiper ende -->
                <div class="swiper-container detail swiper-no-swiping">
                    <div class="swiper-wrapper">
                     
                 @foreach($calendar as $k => $cal)
                   @foreach($cal['days'] as $day => $events)
                        <div class="swiper-slide slide_d_{{$slideNo}}">
                            <div class="calendar-no-data container">
                                <div class="alert alert-default text-center">
                                    <strong>Hinweis</strong> 
                                    <p>Keine Veranstaltungen in dieser Kategorie vorhanden.</p>
                                </div>
                            </div>
                            <div class="day">{{$day}}</div>
                            <div class="day-wrapper">

                             @foreach($events as $evts)    
                              @foreach($evts as $evt)    
                                <!-- Calendar item  1 start -->
                                <article class="filter-guidances filter-members event_no_{{$evt['index']}}">
                                    <div>
                                        <header class="pt-20 pb-20">
                                            <div class="time">
                                                <span class="icon icon-clock icon-white"></span>
                                                <!-- @if(array_key_exists('start_time', $evt)) -->
                                                  {{substr($evt['start_time'], 0, 5)}} - {{substr($evt['end_time'], 0, 5)}}
                                                <!-- @endif   -->
                                            </div>
                                            <h2>{{$evt['title_de']}}</h2>
                                            <h3>{{$evt['subtitle_de']}}</h3>
                                            <div class="opener opener-close">
                                                <a href="#" class="opener-close-link close-detail">
                                                    <span class="icon icon-up"></span>
                                                </a>
                                                <a href="#" class="opener-open-link open-detail">
                                                    <span class="icon icon-down"></span>
                                                </a>
                                            </div>
                                        </header>
                                        <div class="detail-wrapper panel-collapse collapse container pb-30">
                                            <h3 class="detail-header">{{$evt['guide_name']}}</h3>
                                            {{html_entity_decode($evt['detail_de'])}}
                                            @if(isset($evt['event_image']))
                                                <figure>
                                                    <img class="img-responsive" src="http://kh-cms-test.byethost8.com/{{$evt['event_image']}}" alt="#" title="#" />
                                                </figure>
                                            @endif

                                            @if(isset($evt['clusters']) && count($evt['clusters']))
                                                <h4>Im Rahmen der Ausstellung</h4>
                                                <ul class="list-unstyled links-underlined">
                                                @foreach($evt['clusters'] as $cl)
                                                    <li>
                                                        <span class="icon icon-arrow icon-s icon-inline"></span>
                                                        <a href="javascript:showEvent('', '', '0')"
                                                        >{{$cl['title_de']}}</a>
                                                    </li>
                                                @endforeach
                                                </ul>
                                            @endif

                                            @if(isset($evt['event_dates']) && count($evt['event_dates']))

                                            <h4>Weitere Termine in dieser Reihe</h4>
                                            <div>
                                             @foreach($evt['event_dates'] as $ed)
                                               <?php $arr = explode('-', $ed['event_date']);?>
                                                <a href="javascript:showEvent({{$ed['index']}}, {{$ed['slideNo']}})">
                                                    <span class="calendar">
                                                        {{$arr[0]}}<br />
                                                        {{$arr[1]}}<br />
                                                        {{$arr[2]}}
                                                    </span>
                                                </a>
                                             @endforeach   
                                            </div>
                                            @endif

                                            <dl class="dl-horizontal">
                                                <dt>Kosten</dt>
                                                <dd>
                                                <?php $ep = $evt['event_cost']; ?>
                                                @if(isset($ep->regular_price_adult) && ($ep->regular_price_adult) > 0)
                                                    <p>{{$ep->regular_price_adult}} € für Erwachsene <br />
                                                @endif                                                    
                                                @if(isset($ep->regular_price_child) && ($ep->regular_price_child) > 0)
                                                    <p>{{$ep->regular_price_child}} € für Kinder <br />
                                                @endif                                                    
                                                @if(isset($ep->member_price_adult) && ($ep->member_price_adult) > 0)
                                                    <p>{{$ep->member_price_adult}} € für Mitglieder <br />
                                                @endif                                                    
                                                @if(isset($ep->member_price_child) && ($ep->member_price_child) > 0)
                                                    <p>{{$ep->member_price_child}} € für Kinder/Mitglied <br />
                                                @endif                                                    
                                                @if(isset($ep->siblings_price_child) && ($ep->siblings_price_child) > 0)
                                                    <p>{{$ep->siblings_price_child}} € für ermäßigt <br />
                                                @endif                                                    
                                                @if(isset($ep->regular_adult_rp) && ($ep->regular_adult_rp) > 0)
                                                    <p>{{$ep->regular_adult_rp}} € für Geschwisterkinder <br />
                                                @endif                                                    
                                                    <!-- 70,00 € für Kinder / Familienmitgliedschaft -->
                                                    </p>
                                                </dd>
                                                <dt>Anmeldung</dt>
                                                <dd><p>Die Teilnehmerzahl ist begrenzt, bitte melden Sie Ihr Kind rechtzeitig an.Anmeldung bis 5.10.</p></dd>
                                                <dt>Anmerkung</dt>
                                                <dd><p>Der Atelierkurs beginnt am Mi, 26. Oktober und findet täglich statt.</p></dd>
                                                                                                    <dt>Ort</dt>
                                                <dd>
                                                    <p>{{$evt['place']}}</p>
                                                @if(isset($evt['google_map_url']) && strlen($evt['google_map_url']) > 0)
                                                    <ul class="list-unstyled links-underlined">
                                                        <li>
                                                            <span class="icon icon-arrow icon-s icon-inline"></span>
                                                            <a href="{{$evt['google_map_url']}}">Google Maps</a>
                                                        </li>
                                                    </ul>
                                                @endif    
                                                </dd>
                                            </dl>
                                            <!-- Registration start-->
                                            <div class="registration-opener">
                                                <a href="#" class="open-registration text-red">Jetzt anmelden</a>
                                                <div class="opener">
                                                    <a href="#" class="opener-close-link close-registration">
                                                        <span class="icon icon-up icon-grey"></span>
                                                    </a>
                                                    <a href="#" class="opener-open-link open-registration">
                                                        <span class="icon icon-down icon-red"></span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="registration-wrapper panel-collapse collapse">
                                                <form method="POST">
                                                    <fieldset class="registration-number-of-persons">
                                                        <legend><h4>Anmeldung</h4></legend>
                                                        <p>Hiermit melde ich folgende Anzahl Personen verbindlich zu oben stehender Veranstaltung an:</p>


                                                @if($evt['is_free'] == 0)    

                                                        <div class="row registration-count-item reg-inp">
                                                            <div class="col-xs-3 col-sm-2 col1">
                                                                <a href="#" class="registration-count-increment">
                                                                    <span class="icon icon-button-plus icon-red"></span>
                                                                </a>
                                                                    <a href="#" class="registration-count-decrement">
                                                                    <span class="icon icon-button-minus icon-red"></span>
                                                                </a>
                                                            </div>
                                                            <div class="col-xs-2 col-sm-2 col2">
                                                                <div class="form-group inline">
                                                                    <input type="text" class="form-control" name="regular_adult_price" id="regular_adult_price" placeholder="0" 
                                                                    data-price="{{$evt['event_cost']->regular_adult_price}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-4 col-sm-6 col3">
                                                                <label for="regular_adult_price">Erwachsene </label>
                                                            </div>
                                                            <div class="col-xs-3 col-sm-2 text-right col4">
                                                                <span class="price">0,00 €</span>
                                                            </div>
                                                        </div>
                                                        <div class="row registration-count-item children reg-inp">
                                                            <div class="col-xs-3 col-sm-2 col1">
                                                                <a href="#" class="registration-count-increment">
                                                                    <span class="icon icon-button-plus icon-red"></span>
                                                                </a>
                                                                    <a href="#" class="registration-count-decrement">
                                                                    <span class="icon icon-button-minus icon-red"></span>
                                                                </a>
                                                            </div>
                                                            <div class="col-xs-2 col-sm-2 col2">
                                                                <div class="form-group inline">
                                                                    <input type="text" class="form-control" name="regular_child_price" id="regular_child_price" placeholder="0" 
                                                                    data-price="{{$evt['event_cost']->regular_child_price}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-4 col-sm-6 col3">
                                                                <label for="regular_child_price">Kinder</label>
                                                            </div>
                                                            <div class="col-xs-3 col-sm-2 text-right col4">
                                                                <span class="price">0,00 €</span>
                                                            </div>
                                                        </div>
                                                        <div class="row registration-count-item children reg-inp">
                                                            <div class="col-xs-3 col-sm-2 col1">
                                                                <a href="#" class="registration-count-increment">
                                                                    <span class="icon icon-button-plus icon-red"></span>
                                                                </a>
                                                                    <a href="#" class="registration-count-decrement">
                                                                    <span class="icon icon-button-minus icon-red"></span>
                                                                </a>
                                                            </div>
                                                            <div class="col-xs-2 col-sm-2 col2">
                                                                <div class="form-group inline">
                                                                    <input type="text" class="form-control" name="member_adult_price" id="member_adult_price" placeholder="0" 
                                                                    data-price="{{$evt['event_cost']->member_adult_price}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-4 col-sm-6 col3">
                                                                <label for="member_adult_price">Mitglieder</label>
                                                            </div>
                                                            <div class="col-xs-3 col-sm-2 text-right col4">
                                                                <span class="price">0,00 €</span>
                                                            </div>
                                                        </div>

                                                        <div class="row registration-count-item children reg-inp">
                                                            <div class="col-xs-3 col-sm-2 col1">
                                                                <a href="#" class="registration-count-increment">
                                                                    <span class="icon icon-button-plus icon-red"></span>
                                                                </a>
                                                                    <a href="#" class="registration-count-decrement">
                                                                    <span class="icon icon-button-minus icon-red"></span>
                                                                </a>
                                                            </div>
                                                            <div class="col-xs-2 col-sm-2 col2">
                                                                <div class="form-group inline">
                                                                    <input type="text" class="form-control" name="member_child_price" id="member_child_price" placeholder="0" 
                                                                    data-price="{{$evt['event_cost']->member_child_price}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-4 col-sm-6 col3">
                                                                <label for="member_child_price">Kinder/ Mitglied</label>
                                                            </div>
                                                            <div class="col-xs-3 col-sm-2 text-right col4">
                                                                <span class="price">0,00 €</span>
                                                            </div>
                                                        </div>

                                                        <div class="row registration-count-item children reg-inp">
                                                            <div class="col-xs-3 col-sm-2 col1">
                                                                <a href="#" class="registration-count-increment">
                                                                    <span class="icon icon-button-plus icon-red"></span>
                                                                </a>
                                                                    <a href="#" class="registration-count-decrement">
                                                                    <span class="icon icon-button-minus icon-red"></span>
                                                                </a>
                                                            </div>
                                                            <div class="col-xs-2 col-sm-2 col2">
                                                                <div class="form-group inline">
                                                                    <input type="text" class="form-control" name="sibling_child_price" id="sibling_child_price" placeholder="0" 
                                                                    data-price="{{$evt['event_cost']->sibling_child_price}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-4 col-sm-6 col3">
                                                                <label for="regular_adult_rp">Geschwisterkinder</label>
                                                            </div>
                                                            <div class="col-xs-3 col-sm-2 text-right col4">
                                                                <span class="price">0,00 €</span>
                                                            </div>
                                                        </div>
                                                        <div class="row registration-count-item children reg-inp">
                                                            <div class="col-xs-3 col-sm-2 col1">
                                                                <a href="#" class="registration-count-increment">
                                                                    <span class="icon icon-button-plus icon-red"></span>
                                                                </a>
                                                                    <a href="#" class="registration-count-decrement">
                                                                    <span class="icon icon-button-minus icon-red"></span>
                                                                </a>
                                                            </div>
                                                            <div class="col-xs-2 col-sm-2 col2">
                                                                <div class="form-group inline">
                                                                    <input type="text" class="form-control" name="sibling_member_price" id="sibling_member_price" placeholder="0" 
                                                                    data-price="{{$evt['event_cost']->sibling_member_price}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-4 col-sm-6 col3">
                                                                <label for="sibling_member_price">Geschwisterkinder / Mitglied</label>
                                                            </div>
                                                            <div class="col-xs-3 col-sm-2 text-right col4">
                                                                <span class="price">0,00 €</span>
                                                            </div>
                                                        </div>
                                                        <div class="row registration-count-item children reg-inp">
                                                            <div class="col-xs-3 col-sm-2 col1">
                                                                <a href="#" class="registration-count-increment">
                                                                    <span class="icon icon-button-plus icon-red"></span>
                                                                </a>
                                                                    <a href="#" class="registration-count-decrement">
                                                                    <span class="icon icon-button-minus icon-red"></span>
                                                                </a>
                                                            </div>
                                                            <div class="col-xs-2 col-sm-2 col2">
                                                                <div class="form-group inline">
                                                                    <input type="text" class="form-control" name="reduced_price" id="reduced_price" placeholder="0" 
                                                                    data-price="{{$evt['event_cost']->reduced_price}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-4 col-sm-6 col3">
                                                                <label for="siblings_price_child">ermäßigt</label>
                                                            </div>
                                                            <div class="col-xs-3 col-sm-2 text-right col4">
                                                                <span class="price">0,00 €</span>
                                                            </div>
                                                        </div>

                                                    @endif    

                                                        <div class="row registration-count-item children reg-inp">
                                                            <div class="col-xs-3 col-sm-2 col1">
                                                                <a href="#" class="registration-count-increment">
                                                                    <span class="icon icon-button-plus icon-red"></span>
                                                                </a>
                                                                    <a href="#" class="registration-count-decrement">
                                                                    <span class="icon icon-button-minus icon-red"></span>
                                                                </a>
                                                            </div>
                                                            <div class="col-xs-2 col-sm-2 col2">
                                                                <div class="form-group inline">
                                                                    <input type="text" class="form-control" name="persons" id="persons" placeholder="0" 
                                                                    data-price="0">
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-4 col-sm-6 col3">
                                                                <label for="persons">Personen</label>
                                                            </div>
                                                            <div class="col-xs-3 col-sm-2 text-right col4">
                                                                <span class="price">0,00 €</span>
                                                            </div>
                                                        </div>


                                                        <div class="row registration-count-total reg-inp">
                                                            <div class="col-xs-9 col-md-10 text-right">
                                                                Summe:
                                                            </div>
                                                            <div class="col-xs-3 col-md-2 text-right">
                                                                <span class="price">0,00 €</span>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset>
                                                        <legend class="reg-inp"><h4>Persönliche Angaben *</h4></legend>
                                                        <div class="form-group label-placeholder reg-inp">
                                                            <label for="first_name" class="control-label">Name</label>
                                                            <input type="text" class="form-control" name="first_name" id="first_name" required />
                                                        </div>
                                                        <div class="form-group label-placeholder reg-inp">
                                                            <label for="last_name" class="control-label">Nachname</label>
                                                            <input type="text" class="form-control" name="last_name" id="last_name" required />
                                                        </div>
                                                        <div class="form-group label-placeholder reg-inp">
                                                            <label for="email" class="control-label">Email</label>
                                                            <input type="email" class="form-control" name="email" id="email" required />
                                                        </div>
                                                        <div class="row reg-inp">
                                                            <div class="col-md-8">
                                                                <div class="form-group label-placeholder">
                                                                    <label for="street" class="control-label">Straße</label>
                                                                    <input type="text" class="form-control" name="street" id="street" required />
                                                                </div>  
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group label-placeholder">
                                                                    <label for="streetno" class="control-label">Hausnummer</label>
                                                                    <input type="text" class="form-control" name="streetno" id="streetno" required />
                                                                </div>  
                                                            </div>
                                                        </div>
                                                        <div class="row reg-inp">
                                                            <div class="col-md-4">
                                                                <div class="form-group label-placeholder">
                                                                    <label for="zip" class="control-label">PLZ</label>
                                                                    <input type="text" class="form-control" name="zip" id="zip" required />
                                                                </div>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <div class="form-group label-placeholder">
                                                                    <label for="city" class="control-label">Stadt</label>
                                                                    <input type="text" class="form-control" name="city" id="city" required />
                                                                </div>  
                                                            </div>
                                                        </div>
                                                        <div class="form-group label-placeholder reg-inp">
                                                            <label for="phon" class="control-label">Telefonnummer</label>
                                                            <input type="text" class="form-control" name="phone" id="phone" required />
                                                        </div>
                                                    </fieldset>

                                                @if($evt['is_free'] == 0)    

                                                    <fieldset>
                                                        <div class="form-group">
                                                            <div class="checkbox">
                                                                <label>
                                                                    <input type="checkbox" name="artclubmember" onclick="toggleRegForm(this)" /> Ich bin Mitglied im Kunstverein
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group label-placeholder disabled">
                                                            <label for="artclubnumber" class="control-label">Kunstverein-Mitgliedsnummer</label>
                                                            <input type="text" class="form-control" name="artclubnumber" id="artclubnumber" required disabled />
                                                        </div>
                                                    </fieldset>
                                                    <fieldset class="registration-children-info">
                                                        <legend><h4>Angaben zu den Kindern *</h4></legend>
                                                        <div class="row registration-children-info-dummy">
                                                            <div class="col-sm-8 col-md-10">
                                                                <div class="form-group label-placeholder">
                                                                    <label class="control-label">Name</label>
                                                                    <input type="text" class="form-control" name="children_names[placeholder]" required />
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-4 col-md-2">
                                                                <div class="form-group label-placeholder">
                                                                    <label class="control-label">Alter</label>
                                                                    <input type="number" min="0" max="21" class="form-control" name="children_ages[placeholder]" required />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset class="reg-inp">
                                                        <legend><h4>Zahlung *</h4></legend>
                                                        <p>Wir bieten als Zahlungsmöglichkeit Bankzeinzug an.
                                                            Diese Einzugsermächtigung gilt nur für die hier aufgeführten Veranstaltungen.
                                                            Danach erlischt sie.</p>
                                                        <p>Den ggf. gesondert anfallenden Eintritt bezahle ich an der Kasse der Kunsthalle Bremen.</p>
                                                        <div class="form-group label-placeholder">
                                                            <label for="iban" class="control-label">IBAN</label>
                                                            <input type="text" class="form-control" name="iban" id="iban" required />
                                                        </div>
                                                        <div class="form-group label-placeholder">
                                                            <label for="depositor" class="control-label">Kontoinhaber</label>
                                                            <input type="text" class="form-control" name="depositor" id="depositor" required />
                                                        </div>
                                                        <div class="form-group label-placeholder">
                                                            <label for="bank" class="control-label">Name der Bank</label>
                                                            <input type="text" class="form-control" name="bank" id="bank" required />
                                                        </div>

                                                    @endif    

                                                        <div class="form-group">
                                                            <div class="checkbox">
                                                                <label class="links-underlined">
                                                                    <input type="checkbox" name="conditions_of_participation" required /> Mit den <a href="#">Anmelde- und Teilnahmebedingungen</a> bin ich einverstanden.
                                                                </label>
                                                            </div>
                                                            <div class="checkbox">
                                                                <label class="links-underlined">
                                                                    <input type="checkbox" name="newsletter" /> Ich möchte gerne den Newsletter der Kunsthalle Bremen empfangen. Sie können jederzeit den Erhalt des Newsletters durch einen telefonischen Hinweis oder über eine E-Mail an <a href="#">info@kunsthalle-bremen.de</a> wiederufen.
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <div class="text-center mt-15 mb-15">
                                                        <a href="#" class="close-registration" title="#">
                                                            <span class="icon icon-close icon-red"></span>
                                                        </a>
                                                    </div>
                                                    <div>
                                                        <button type="submit" class="btn btn-raised btn-default">Jetzt zahlungspflichtig anmelden</button>
                                                    </div>
                                                </form>
                                            </div>
                                            <!-- Registration end -->
                                        </div>
                                    </div>
                                </article>
                                <!-- Calendar item 1 end -->
                            @endforeach
                            @endforeach


                    @endforeach
                  @endforeach

<!--                             <div class="day">30 Freitag</div>
                            <div class="day-wrapper">
                                <article>
                                    <div>
                                        <header class="pt-20 pb-20">
                                            <div class="time">
                                                <span class="icon icon-clock icon-white"></span>
                                                16:30 - 18:30
                                            </div>
                                            <h2>Moment mal</h2>
                                            <h3>Atelierkurs für alle ab 6</h3>
                                            <div class="opener opener-close">
                                                <a href="#" class="opener-close-link close-detail">
                                                    <span class="icon icon-up"></span>
                                                </a>
                                                <a href="#" class="opener-open-link open-detail">
                                                    <span class="icon icon-down"></span>
                                                </a>
                                            </div>
                                        </header>
                                        <div class="detail-wrapper panel-collapse collapse container pb-30">
                                            <h3 class="detail-header">Mit Moja Pohlan</h3>
                                            <p>In diesem Kurs dreht sich alles um Max Liebermann. Mit lockeren Pinselstrichen und kräftigen
                                                Farben hat dieser Maler Momente bei Freizeitvergnügungen und sportliche Aktivitäten seiner
                                                Zeit dargestellt. Strichelnd, tupfend, flirrend – wie Max Liebermann bringst Du im Kunsthallen-Atelier
                                                Deine eigenen Lieblingsaktivitäten auf Papier und Leinwand!</p>
                                            <figure>
                                                <img class="img-responsive" src="/images/dummy/beispiel_quer.jpg" alt="#" title="#" />
                                            </figure>
                                            <h4>Im Rahmen der Ausstellung</h4>
                                            <ul class="list-unstyled links-underlined">
                                                <li>
                                                    <span class="icon icon-arrow icon-s icon-inline"></span>
                                                    <a href="#">Max Liebermann</a>
                                                </li>
                                                <li>
                                                    <span class="icon icon-arrow icon-s icon-inline"></span>
                                                    <a href="#">Another Link</a>
                                                </li>
                                                <li>
                                                    <span class="icon icon-arrow icon-s icon-inline"></span>
                                                    <a href="#">Another Link</a>
                                                </li>
                                            </ul>
                                            <h4>Weitere Termine in dieser Reihe</h4>
                                            <div>
                                                <a href="#">
                                                    <span class="calendar">
                                                        Mi<br />
                                                        14<br />
                                                        Dez
                                                    </span>
                                                </a>
                                                <a href="#">
                                                    <span class="calendar">
                                                        Mi<br />
                                                        14<br />
                                                        Dez
                                                    </span>
                                                </a>
                                                <a href="#">
                                                    <span class="calendar">
                                                        Mi<br />
                                                        14<br />
                                                        Dez
                                                    </span>
                                                </a>
                                            </div>
                                            <dl class="dl-horizontal">
                                                <dt>Kosten</dt>
                                                <dd>
                                                    <p>80,00 € für Kinder <br />
                                                    70,00 € für Kinder / Familienmitgliedschaft</p>
                                                </dd>
                                                <dt>Anmeldung</dt>
                                                <dd><p>Die Teilnehmerzahl ist begrenzt, bitte melden Sie Ihr Kind rechtzeitig an.Anmeldung bis 5.10.</p></dd>
                                                <dt>Anmerkung</dt>
                                                <dd><p>Der Atelierkurs beginnt am Mi, 26. Oktober und findet täglich statt.</p></dd>
                                                                                                    <dt>Ort</dt>
                                                <dd>
                                                    <p>Kino 46 / ABC Straße / 28203 Bremen</p>
                                                    <ul class="list-unstyled links-underlined">
                                                        <li>
                                                            <span class="icon icon-arrow icon-s icon-inline"></span>
                                                            <a href="#">Google Maps</a>
                                                        </li>
                                                    </ul>
                                                </dd>
                                            </dl> -->
                                            <!-- Registration start-->
                                                <!-- no Registration -->
                                            <!-- Registration end-->
                                        </div>
                                    </div>
                                </article>
                                <!-- Calendar item 3 end -->
                            </div>
                        </div>
                        <!-- <div class="swiper-slide">Slide 2</div> -->
                        <!-- <div class="swiper-slide">Slide 3</div> -->

                    <?php //} ?>    

                    </div>
                </div>
            </section>
        </div>
    </div>
    <!-- content ende -->

    <!-- footer start -->
    <div id="footer">
        <ul id="footermenu" class="list-unstyled">
            <li><a href="#" title="#">Kontakt</a></li>
            <li><a href="#" title="#">Nutzungsbedingungen</a></li>
            <li><a href="#" title="#">Datenschutz</a></li>
            <li><a href="#" title="#">Sitemap</a></li>
            <li><a href="#" title="#">Impressum</a></li>
        </ul>
        <ul class="list-inline">
            <li>
                <a href="#" title="#">
                    <span class="icon icon-facebook icon-l"></span>
                </a>
            </li>
            <li>
                <a href="#" title="#">
                    <span class="icon icon-twitter icon-l"></span>
                </a>
            </li>
            <li>
                <a href="#" title="#">
                    <span class="icon icon-youtube icon-l"></span>
                </a>
            </li>
        </ul>
        <div class="container">
            <div class="row">
                <div class="col-sm-offset-2 col-sm-8 col-md-offset-3 col-md-6">
                    <form method="POST">
                        <p>Wollen Sie immer als erstes über Ausstellungen und Aktivitäten der Kunsthalle informiert sein?
                        Dann tragen Sie sich in den Newsletter ein:</p>
                        <div class="form-group label-placeholder">
                            <label for="newsletterEmail" class="control-label">E-Mail-Adresse</label>
                            <input type="email" class="form-control" name="email" id="newsletterEmail">
                        </div>
                        <button type="submit" class="btn btn-link">OK</button>
                    </form>
                </div>  
            </div>
        </div>
    </div>
    <!-- footer ende -->

    <!-- jquery -->
    <script src="/bower_components/jquery/dist/jquery.min.js" type="text/javascript"></script>
    <!-- bootstrap -->
    <script src="/bower_components/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- material-design -->
    <script src="/bower_components/bootstrap-material-design/dist/js/material.min.js" type="text/javascript"></script>
    <script src="/bower_components/bootstrap-material-design/dist/js/ripples.min.js" type="text/javascript"></script>
    <!-- swiper -->
    <script src="/bower_components/swiper/dist/js/swiper.jquery.min.js" type="text/javascript"></script>
    <!-- validate -->
    <script src="/bower_components/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
    <script src="/bower_components/jquery-validation/dist/additional-methods.min.js" type="text/javascript"></script>
    <!-- german validation messages -->
    <script src="/js/messages_de.js" type="text/javascript"></script>
    <!-- custom -->
    <script src="/js/main.js" type="text/javascript"></script>

<script>
function showEvent(index, slideNo) {
    // alert(index + "\n"+slideNo);
    var swiperDetails = new Swiper ('#calendar .swiper-container.detail', {
        loop: true,
    });
    var swiperControl = new Swiper ('#calendar .swiper-container.control', {
        loop: true,
        // control: swiperDetails,
        nextButton: '.month-next',
        prevButton: '.month-prev',
    });

    swiperDetails.slideTo(slideNo); //, 300, true, true);
    swiperControl.slideTo(slideNo); //, 300, true, true);
    var scrollPos = $(".event_no_"+index).offset().top - 150;
    $('html, body').animate({
      scrollTop: scrollPos
    }, 700);
}

function toggleRegForm(chk) {
    if(chk.checked) {
        $('.reg-inp').hide();
    } else {
        $('.reg-inp').show();
    }
}    

</script>    
</body>

</html>