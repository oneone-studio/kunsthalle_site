@extends('layouts.default')
@section('content')
    @if($page->banner)
         <header>
            <div class="bg-ct picture-container" style="background-image: url('{{$DOMAIN}}/files/exhibition_pages/{{$page->banner->image}}');">
                <h1 class="text-center title-middle">
                @if($page->banner && $page->banner->banner_text)    
                   @foreach($page->banner->banner_text as $t)
                    <?php $size = 's';
                        if($t->size) { $size = strtolower($t->size); }
                    ?>
                       <span class="text-{{$size}}">{{$t->line}}</span>
                   @endforeach
                @endif    
                </h1>
            </div>  
          </header>                  
    @endif    

    <div class="ce ce-menu container-fluid">
        <ul class="list-inline">
            <li><a href="/exhibitions/list/current" class="btn btn-default btn-raised">Ausstellungen</a></li>
        </ul>
    </div>


    <div class="ce ce-anchors container">
        <ul class="list-unstyled list-inline" />
    </div>

    <!-- content ende -->
    @foreach($pg_sections as $ps)
        
        @if($ps->type == 'h2text')
            <div class="ce ce-headline container">
                <h2 class="anchor" data-anchortext="Special text">{{$ps->headline_de}}</h2>
                <p>{{$ps->intro_de}}
                </p>
            </div>                    
        @endif

        @if($ps->type == 'content')
            <div class="ce ce-text container">{{$ps->content_de}}</div>
        @endif


        @if($ps->type == 'slider')
            <!-- gallery start -->
            <div class="ce ce-gallery container-fluid">
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                    @foreach($ps->page_slider_images as $img)
                        <div class="swiper-slide">
                            <figure>
                                <div class="bg-white">
                                    <img src="{{$DOMAIN}}/files/sliders/{{$img->filename}}" class="img-responsive" />
                                </div>
                                <figcaption>
                                {{$img->detail}}
                                </figcaption>
                            </figure>
                        </div>
                    @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="next"><span class="icon icon-right icon-l" /></div>
                    <div class="prev"><span class="icon icon-left icon-l" /></div>
                </div>
            </div>
            <!-- gallery end -->
        @endif    

        @if($ps->type == 'image')
            <div class="ce ce-picture container">
                <figure>
                    <img src="{{$DOMAIN}}/files/image/{{$ps->filename}}" class="img-responsive" />
                    <figcaption>
                        {{$ps->caption_de}}
                    </figcaption>
                </figure>
            </div>
        @endif

    @endforeach

    @if(isset($calendar))    
        <!-- Event calender as module start -->
        <div id="calender-wrapper">
            <section id="calendar">
                <div class="swiper-container control">
                    <div class="swiper-wrapper">
                    <?php $slideNo = 0; ?>    
                    @foreach($calendar as $k => $cal)
                       <?php ++$slideNo; ?>
                        <div class="swiper-slide">
                            <div class="date">
                                <span class="month">{{utf8_encode($cal['month'])}}</span>
                                <span class="year">{{$cal['year']}}</span>
                            </div>
                        </div>
                    @endforeach
                    </div>
                    <div class="month-prev"><span class="icon icon-left icon-l" /></div>
                    <div class="month-next"><span class="icon icon-right icon-l" /></div>
                </div>

                <div class="swiper-container detail swiper-no-swiping">
                    <div class="swiper-wrapper">
                     
                     <?php $_MONTH = ''; ?>
                     <?php foreach($calendar as $mnth => $cal): ?>

                        <div class="swiper-slide slide_d_{{$slideNo}}"">
                            <div class="calendar-no-data container">
                                <div class="alert alert-default text-center">
                                    <strong>Hinweis</strong> 
                                    <p>Keine Veranstaltungen in dieser Kategorie vorhanden.</p>
                                </div>
                            </div>
                       <?php foreach($cal['days'] as $day => $events): ?>
                            <div class="day">{{$day}}</div>
                            <div class="day-wrapper">
    
                             @foreach($events as $evts)    
                              @foreach($evts as $evt)    
                                <?php $MONTH = strftime("%B", strtotime($evt['start_date'])); ?>

                            <article class="filter-guidances filter-members">
                                    <div>
                                        <header class="pt-20 pb-20">
                                            <div class="time">
                                                <span class="icon icon-clock icon-white"></span>
                                                  {{substr($evt['start_time'], 0, 5)}} - {{substr($evt['end_time'], 0, 5)}}
                                                  __id: {{$evt['id']}}
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
                                            @if(isset($evt['event_image']) && strlen($evt['event_image']) > 0)
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

                                                @if(isset($ep->regular_adult_price) && ($ep->regular_adult_price) > 0)
                                                    <p>{{$ep->regular_adult_price}} € für Erwachsene </p>
                                                @endif                                                    
                                                @if(isset($ep->regular_child_price) && ($ep->regular_child_price) > 0)
                                                    <p>{{$ep->regular_child_price}} € für Kinder </p>
                                                @endif                                                    
                                                @if(isset($ep->member_adult_price) && ($ep->member_adult_price) > 0)
                                                    <p>{{$ep->member_adult_price}} € für Mitglieder </p>
                                                @endif                                                    
                                                @if(isset($ep->member_child_price) && ($ep->member_child_price) > 0)
                                                    <p>{{$ep->member_child_price}} € für Kinder / Mitglied </p>
                                                @endif                                                    
                                                @if(isset($ep->siblings_child_price) && ($ep->siblings_child_price) > 0)
                                                    <p>{{$ep->siblings_child_price}} € für Geschwisterkinder </p>
                                                @endif                                                    
                                                @if(isset($ep->reduced_price) && ($ep->reduced_price) > 0)
                                                    <p>{{$ep->reduced_price}} € für ermäßigt </p>
                                                @endif                                                    
                                                @if($evt['entrance']['free'] == 1) <p>Eintritt frei </p> @endif
                                                @if($evt['entrance']['included'] == 1) <p>inklusive Eintritt in die Kunsthalle Bremen </p> @endif
                                                @if($evt['entrance']['excluded'] == 1) <p>exklusive Eintritt in die Kunsthalle Bremen </p> @endif
                                                @if($evt['entrance']['entry_fee'] == 1) <p>Eintritt in die Kunsthalle Bremen </p> @endif
                                                </dd>
                                                @if($evt['registration'] == 1)
                                                    <dt>Anmeldung</dt>
                                                    <dd><p>{{$evt['registration_detail']}}</p></dd>
                                                @endif                                                

                                                @if(strlen($evt['remarks']) > 0)
                                                    <dt>Anmerkung</dt>
                                                    <dd><p>{{$evt['remarks']}}</p></dd>
                                                @endif
                                                            
                                                @if(strlen($evt['place']) > 0)
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
                                                @endif
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
                                                <form id="reg_form_{{$evt['id']}}" method="POST">
                                                    <fieldset class="registration-number-of-persons">
                                                        <legend><h4>Anmeldung</h4></legend>
                                                        <p>Hiermit melde ich folgende Anzahl Personen verbindlich zu oben stehender Veranstaltung an:</p>

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
                                                                    data-price="{{{ isset($evt['event_cost']) ? $evt['event_cost']->regular_adult_price : ''}}}">
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
                                                                    data-price="{{{ isset($evt['event_cost']) ? $evt['event_cost']->regular_child_price : ''}}}">
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
                                                                    data-price="{{{ isset($evt['event_cost']) ? $evt['event_cost']->member_adult_price : ''}}}">
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
                                                                    data-price="{{{ isset($evt['event_cost']) ? $evt['event_cost']->member_child_price : ''}}}">
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
                                                                    data-price="{{{ isset($evt['event_cost']) ? $evt['event_cost']->sibling_child_price : ''}}}">
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
                                                                    data-price="{{{ isset($evt['event_cost']) ? $evt['event_cost']->sibling_member_price : '' }}}">
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
                                                                    data-price="{{{ isset($evt['event_cost']) ? $evt['event_cost']->reduced_price : '' }}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-4 col-sm-6 col3">
                                                                <label for="siblings_price_child">ermäßigt</label>
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
                                                                <span class="price total_price_{{$evt['id']}}">0,00 €</span>
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

                                                    <fieldset>
                                                        <div class="form-group">
                                                            <div class="checkbox">
                                                                <label>
                                                                    <input type="checkbox" name="member_chk" /> Ich bin Mitglied im Kunstverein Zz
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group label-placeholder disabled">
                                                            <label for="artclubnumber" class="control-label">Kunstverein-Mitgliedsnummer</label>
                                                            <input type="text" class="form-control" name="member_no" id="member_no_{{$evt['id']}}" required disabled />
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
                                                        <button type="button" class="btn btn-raised btn-default" onclick="registerForEvent({{$evt['id']}})">Jetzt zahlungspflichtig anmelden</button>
                                                    </div>
                                                </form>
                                            </div>
                                            <!-- Registration end -->
                                        </div>
                                    </div>
                                </article>
                                <!-- Calendar item 1 end -->
                            <?php endforeach;?>
                            <?php endforeach;?>
                              </div>  

                    @endforeach
                      </div>  
                  @endforeach
                        </div>
                </div>
            </section>
        </div>
        <!-- Event calender as module end -->       
    @endif    


    @if($page->downloads && count($page->downloads))
        <!-- download start -->
        <div class="ce ce-download container-fluid ce-download-termsofuse">
            <h3 class="anchor">Downloads</h3>
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    @foreach($page->downloads as $dl)
                    <div class="swiper-slide ce-download-element" data-name="{{$dl->id}}">
                        <div class="ce-download-element-overlay"></div>
                        <div>
                            <div>
                                <img src="{{$DOMAIN}}/files/downloads/{{$dl->thumb_image}}" alt="#" title="#" />
                            </div>
                        </div>
                        <div>
                            {{$dl->link_title}}
                        </div>
                    </div>
                    @endforeach  
                </div>
                <div class="swiper-pagination"></div>
                <div class="next"><span class="icon icon-right icon-l" /></div>
                <div class="prev"><span class="icon icon-left icon-l" /></div>
            </div>
            <button type="button" class="btn btn-default btn-raised active ce-download-mark">Alle markieren</button>
            <button type="button" class="btn btn-default btn-raised active ce-download-load">Auswahl downloaden</button>

        </div>
        <!-- download end -->
    @endif    
    
    @if($page->contacts)
        <!-- contact start -->
        <div class="ce ce-contact container">
            <h4 class="anchor">Ansprechpartner/innen:</h4>
            
            @foreach($page->contacts as $c)
                <div>
                    <a href="mailto:{{$c->email}}">
                        <span class="icon icon-mail icon-m"></span> 
                        {{$c->title}} {{$c->first_name}} {{$c->last_name}}
                    </a>
                </div>
                <div>
                    <a href="tel:{{$c->phone}}">
                        <span class="icon icon-phone icon-m"></span> 
                        {{$c->phone}}
                    </a>
                </div>
            @endforeach
        </div>
        <!-- contact end -->
    @endif    

    @if($sponsors && count($sponsors))
        <!-- sponsors start -->
        <div class="ce ce-sponsors bg-white">
            <div class="container">
                <div>
                    <h4>Diese Ausstellung wurde ermöglicht durch:</h4>
                    <p>Karin und Uwe Hollweg / Stiftung</p>
                </div>
                <div>
                    <h4>Mit freundlicher Unterstützung von:</h4>
                    <ul class="list-inline">
                        <li><a href="#" title="#"><img src="/images/dummy/beispiel_sponsor1.png" alt="#" /></a></li>
                        <li><a href="#" title="#"><img src="/images/dummy/beispiel_sponsor2.png" alt="#" /></a></li>
                        <li><a href="#" title="#"><img src="/images/dummy/beispiel_sponsor3.png" alt="#" /></a></li>
                    </ul>
                </div>
            </div>
        </div>  
        <!-- sponsors end -->
    @endif    

    <!-- Modal -->
     <div id="modals">
        <div id="modal_confirm_event_registration">
            <div class="container">
                <div class="header">
                    <div class="text-center">
                        <a href="#"><img class="logo" src="/images/kunsthalle_bremen_logo_w.svg" alt="Kunsthalle Bremen" title="Kunsthalle Bremen" /></a>
                    </div>
                    <a href="javascript:kunsthalle.hideModal()" class="pull-right"><span class="icon icon-close icon-white"></span></a>
                </div>
                <div class="content">
                    <div class="ce-confirmation">
                        <h4>Vielen Dank für Ihre Anmeldung</h4>
                        <p>Vielen Dank für Ihre Anmeldung, die wir schnellstmöglich bearbeiten werden.
                        Eine Bestätigung Ihrer Teilnahme erhalten Sie nach Prüfung Ihrer Anmeldung.</p>
                        <p>Ihre Kunsthalle Bremen</p>
                        <a href="javascript:kunsthalle.hideModal()" class="btn btn-default btn-raised active">OK</a>
                    </div>
                </div>
            </div>
        </div>
        <div id="modal_termsofuse">
            <div class="container">
                <div class="header">
                    <div class="text-center">
                        <a href="#"><img class="logo" src="/images/kunsthalle_bremen_logo_w.svg" alt="Kunsthalle Bremen" title="Kunsthalle Bremen" /></a>
                    </div>
                    <a href="javascript:kunsthalle.hideModal()" class="pull-right"><span class="icon icon-close icon-white"></span></a>
                </div>
                <div class="content">
                    <div class="ce-termsofuse">
                <div class="content">
                    <div class="ce-termsofuse">
                        <h4>Download von Daten mit besonderen Nutzungsbedingungen</h4>
                        <p>Die von Ihnen angefragten Abbildungen unterliegen besonderen Nutzungsbedingungen 
                            und dürfen nur zur aktuellen redaktionellen und themengebundenen Berichterstattung 
                            unter Angabe der Bildcredits verwendet werden. Die Bilder dürfen nicht beschnitten 
                            und es darf kein Text o.ä. über die Bilder gelegt werden. Online dürfen die Bilder 
                            nur in einer Auflösung von 72dpi veröffentlicht werden. Die genauen 
                            Nutzungsbedingungen werden zusammen mit den Bildern heruntergeladen. 
                            Im Falle einer Veröffentlichung erklären Sie sich mit diesen Bedingungen 
                            einverstanden.</p>
                        <p>Für den Download benötigen Sie ein Passwort. Falls Sie noch keine Zugangsdaten besitzen, können Sie diese <a href="mailto:pressebereich@kunsthalle-bremen.de" class="link-mail"><span class="icon icon-arrow icon-s icon-inline icon-white"></span> hier per Mail</a> anfragen.</p>
                        <form method="POST" novalidate="novalidate">
                            <div class="form-group label-placeholder">
                                <label for="termsOfUseName" class="control-label">Name, Vorname</label>
                                <input type="text" class="form-control" name="termsofuse_name" id="termsOfUseName" required />
                            </div>
                            <div class="form-group label-placeholder">
                                <label for="termsOfUseFirm" class="control-label">Firma / Redaktion</label>
                                <input type="text" class="form-control" name="termsofuse_firm" id="termsOfUseFirm" required />
                            </div>
                            <div class="form-group label-placeholder">
                                <label for="dateOfPublication" class="control-label">Veröffentlichungsdatum</label>
                                <input type="text" class="form-control" name="date_of_publication" id="dateOfPublication" />
                            </div>
                            <div class="form-group label-placeholder">
                                <label for="termsOfUsePassword" class="control-label">Passwort</label>
                                <input type="password" class="form-control" name="termsofuse_password" id="termsOfUsePassword" required />
                            </div>
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="termsofuse_tou" required />
                                        Mit dem Download erkläre ich mich mit den entsprechenden Abbildungs- und Nutzungsbedingungen 
                                        einverstanden. Ich bin mir bewusst, dass jede andere Nutzung des Bildmaterials eine 
                                        Urheberrechtsverletzung darstellt und rechtlich verfolgt werden kann. Die Bilder werden nur 
                                        im Rahmen des von mir angegebenen Mediums verwendet.<br />
                                        Die Nutzungsbedingungen werden diesem Download als PDF beigefügt.
                                    </label>
                                </div>
                            </div>
                            <input type="hidden" name="termsofuse_files" id="termsofuse_files">
                            <button type="button" class="btn btn-default btn-raised active dl-btn" onclick="handleDownload()">Download</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="page_id" value="{{$page->id}}">

    <input type="hidden" id="dl_protected" value="{{$page->dl_protected}}">
    <input type="text" id="dl_password" value="{{$settings->dl_password}}">
    <div id="zip"></div>

<script>
var dl_password = '{{$settings->dl_password}}';

function handleDownload() {
    if($('#dl_protected').val() == '1') {
        if($('#dl_password').val() != $('#termsOfUsePassword').val()) {
            $('#termsOfUsePassword').val('');
            return false;
        }
    }
    var list = $('#termsofuse_files').val();
    list = list.split(', ').join(',');
    var items = list.split(',');
    console.log(items)
    kunsthalle.hideModal('termsofuse');
    if(items.length > 0) {
        $.ajax({
            type: 'POST',
            url: '/handle-downloads',
            data: { 'ids': items, 'page_id': $('#page_id').val() },
            dataType: 'json',
            success:function(data) { 
                        console.log('handleDownload success..');
                        console.log(data);
                        if(data.item != undefined) {
                            $('#zip').html('<iframe width="1" height="1" frameborder="0" src="' + data.item + '"></iframe>');
                        }
                        return false;
                    },
            error:  function(jqXHR, textStatus, errorThrown) {
                        console.log('handleDownload failed.. ');
                        return false;
                    }
        }); 
    }
    return false;
}

function getDLPW() {
    var dlpw = prompt('Enter password: ', '');

    return dlpw;
}

/*
function toggleRegForm(chk) {
    if(chk.checked) {
        $('.reg-inp').hide();
    } else {
        $('.reg-inp').show();
    }
}/**/   

function registerForEvent(id, event) {
    console.log("registerForEvent("+id+") called");
    var $inputs = $('#reg_form_'+id+' :input');
    var frm = document.getElementById('reg_form_'+id);
    var formData = new FormData(frm);
    console.log("Form inputs for event id: "+id);
    // return;
    //console.log($inputs);
    
    var data = {};
    for(var i in $inputs) { 
        console.log("Inp: "+ $inputs[i].name + " : "+ $inputs[i].value); 
        console.log("T: " + typeof $inputs[i]);
        data[$inputs[i].name] = $inputs[i].value;
        if($inputs[i].name == 'newsletter') {
            break;
        }
    }
    data['id'] = id;
    var total = $('.total_price_'+id).html();
    total = total.substr(0, total.indexOf(' '));
    total = total.replace(',', '.', total.replace(' €', ''));
    data['total'] = total;


    var ds = JSON.stringify(data);    
    console.log("JSON -> "); console.log(ds);
    $.ajax({
        type: 'POST',
        url: '/register-for-event',
        data: {ds}, // 'id': id, 'regular_adult_price': $('#regular_adult_price_'+id).val() }, //  formData,
        dataType: 'json',
        success:function(data) { 
                    var modal = kunsthalle.showModal('confirm_event_registration');
                    console.log('registerForEvent success..');
                    console.log(data);
                    return false;
                },
        error:  function(jqXHR, textStatus, errorThrown) {
                    console.log('registerForEvent failed.. ');
                    return false;
                }
    }); 
}

</script>

@stop