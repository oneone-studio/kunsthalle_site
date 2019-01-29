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

                        <div class="swiper-slide slide_d_{{$slideNo}}">
                            <div class="calendar-no-data container">
                                <div class="alert alert-default text-center">
                                    <strong>Hinweis</strong> 
                                    <p>Zu Ihrer Auswahl finden in diesem Monat keine Veranstaltungen (mehr) statt.</p>
                                </div>
                            </div>
                       <?php foreach($cal['days'] as $day => $events): ?>
                            <div class="day">{{$day}}</div>
                            <div class="day-wrapper">
    
                             @foreach($events as $evts)    
                              @foreach($evts as $evt)    
                                <?php $MONTH = strftime("%B", strtotime($evt['start_date'])); ?>

                                <article class="filter-guidances filter-members event_no_{{$evt['index']}}">
                                    <div>
                                        <header class="pt-20 pb-20">
                                            <div class="time">
                                                <span class="icon icon-clock icon-white"></span>
                                                  {{substr($evt['start_time'], 0, 5)}} 
                                                  @if(strlen(trim($evt['end_time'])) > 4)
                                                      - {{substr($evt['end_time'], 0, 5)}}
                                                  @endif
                                            </div>
                                            <h2>{{$evt['title_de']}} ID:{{$evt['id']}}</h2>
                                            <h3>{{$evt['subtitle_de']}}</h3>
                                            <div class="opener opener-close">
                                                <a href="#" class="opener-close-link close-detail">
                                                    <span class="icon icon-up icon-up-{{$evt['id']}}"></span>
                                                </a>
                                                <a href="#" class="opener-open-link open-detail toggle-icon-{{$evt['id']}}">
                                                    <span class="icon icon-down" onclick="setEventId({{$evt['id']}})"></span>
                                                </a>
                                            </div>
                                        </header>
                                        <div class="detail-wrapper panel-collapse collapse container pb-30">
                                            <h3 class="detail-header">{{$evt['guide_name']}}</h3>
                                            {{html_entity_decode($evt['detail_de'])}}
                                            @if(isset($evt['event_image']) && strlen($evt['event_image']) > 0)
                                            <figure>
                                                <img class="img-responsive" src="{{$DOMAIN}}/{{$evt['event_image']}}" alt="#" title="#" />
                                                <figcaption>
                                                    {{$evt['caption_de']}}
                                                </figcaption>
                                            </figure>
                                            @endif

                                            @if(isset($evt['exb_titles']) && count($evt['exb_titles']))
                                                <h4>Im Rahmen der Ausstellung</h4>
                                                <ul class="list-unstyled links-underlined">
                                                    @foreach($evt['exb_titles'] as $link => $title)
                                                    <li>
                                                        <span class="icon icon-arrow icon-s icon-inline"></span>
                                                        <a href="/view/exhibitions/exb-page/{{$link}}"
                                                        >{{$title}}</a>
                                                    </li>
                                                @endforeach
                                                </ul>
                                            @endif

                                            @if(isset($evt['event_dates']) && count($evt['event_dates']) > 1)
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
                                              @if($evt['hasCost'])

                                                <?php $ep = $evt['event_cost']; ?>
                                                <dt>Kosten</dt>
                                                <dd>

                                                @if(isset($ep['regular_adult_price']) && strlen($ep['regular_adult_price']) && (is_numeric($ep['regular_adult_price']) 
                                                        && $ep['regular_adult_price']) >= 0)
                                                    {{ $ep['regular_adult_price'] }} € Erwachsene    (zzgl. Eintritt in die Kunsthalle Bremen) <br>
                                                @endif                                                    

                                                @if(isset($ep['regular_child_price']) && strlen($ep['regular_child_price']) && (is_numeric($ep['regular_child_price']) && $ep['regular_child_price']) >= 0)
                                                    {{ str_replace('.00', '', $ep['regular_child_price'])}} € Kinder <br>
                                                @endif                                                    

                                                @if(isset($ep['member_adult_price']) && strlen($ep['member_adult_price']) && (is_numeric($ep['member_adult_price']) && $ep['member_adult_price']) >= 0)
                                                    {{ str_replace('.00', '', $ep['member_adult_price'])}} € Mitglieder <br>
                                                @endif                                                    

                                                @if(isset($ep['member_child_price']) && strlen($ep['member_child_price']) && (is_numeric($ep['member_child_price']) && $ep['member_child_price']) >= 0)
                                                    {{ str_replace('.00', '', $ep['member_child_price'])}} € Kinder / Mitglied <br>
                                                @endif                                                    

                                                @if(isset($ep['sibling_child_price']) && strlen($ep['sibling_child_price']) && (is_numeric($ep['sibling_child_price']) && $ep['sibling_child_price']) >= 0)
                                                    {{str_replace('.00', '', $ep['sibling_child_price'])}} € Geschwisterkinder <br>
                                                @endif                                                    

                                                @if(isset($ep['sibling_member_price']) && strlen($ep['sibling_member_price']) && (is_numeric($ep['sibling_member_price']) 
                                                && $ep['sibling_member_price']) >= 0)
                                                    {{str_replace('.00', '', $ep['sibling_member_price'])}} € Geschwisterkinder / Mitglied <br>
                                                @endif                                                    

                                                @if(isset($ep['reduced_price']) && strlen($ep['reduced_price']) && (is_numeric($ep['reduced_price']) 
                                                   && $ep['reduced_price']) >= 0)
                                                    {{ str_replace('.00', '', $ep['reduced_price'])}} € ermäßigt   (zzgl. Eintritt in die Kunsthalle Bremen)<br>
                                                @endif                                                    

                                              @endif
                                              
                                                @if($evt['entrance']['free'] == 1) <p>Eintritt frei </p> @endif
                                                @if($evt['entrance']['included'] == 1) <p>inklusive Eintritt in die Kunsthalle Bremen </p> @endif
                                                <!-- @if($evt['entrance']['excluded'] == 1) <p>zzgl. Eintritt in die Kunsthalle Bremen </p> @endif -->
                                                @if($evt['entrance']['entry_fee'] == 1) <p>Eintritt in die Kunsthalle Bremen </p> @endif

                                                </dd>
                                                @if(strlen($evt['registration_detail']) > 0)
                                                    <dt>Anmeldung</dt>
                                                    <dd><p>{{$evt['registration_detail']}}</p></dd>
                                                @endif                                                

                                                @if(strlen($evt['remarks']) > 0)
                                                    <dt>Hinweis</dt>
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
                                            @if($evt['registration'] == 1)
                                            <!-- Registration start-->
                                            <div class="registration-opener">
                                                <a href="#" class="open-registration text-red">Jetzt anmelden</a>
                                                <div class="opener">
                                                    <a href="#" class="opener-close-link close-registration">
                                                        <span class="icon icon-up icon-grey icon-up-{{$evt['id']}}"></span>
                                                    </a>
                                                    <a href="#" class="opener-open-link open-registration">
                                                        <span class="icon icon-down icon-red"></span>
                                                    </a>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="registration-wrapper panel-collapse collapse">
                                                <form id="reg_form_{{$evt['id']}}" method="POST">
                                                @if($evt['hasCost'])                                                    
                                                    <fieldset class="registration-number-of-persons">
                                                        <legend><h4>Anmeldung</h4></legend>
                                                        <p>Hiermit melde ich folgende Anzahl Personen verbindlich zu oben stehender Veranstaltung an:</p>

                                                        @if(is_numeric($evt['event_cost']['regular_adult_price']) && floatval($evt['event_cost']['regular_adult_price']) >= 0)    
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
                                                                        data-price="{{$evt['event_cost']['regular_adult_price']}}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-xs-4 col-sm-6 col3">
                                                                    <label for="regular_adult_price">Erwachsene </label>
                                                                </div>
                                                                <div class="col-xs-3 col-sm-2 text-right col4">
                                                                    <span class="price">0,00 €</span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(is_numeric($evt['event_cost']['regular_child_price']) && floatval($evt['event_cost']['regular_child_price']) >= 0)    
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
                                                        @endif
                                                        @if(is_numeric($evt['event_cost']['member_adult_price']) && floatval($evt['event_cost']['member_adult_price']) >= 0)    
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
                                                        @endif
                                                        @if(is_numeric($evt['event_cost']['member_child_price']) && floatval($evt['event_cost']['member_child_price']) >= 0)    
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
                                                        @endif
                                                        @if(is_numeric($evt['event_cost']['sibling_child_price']) && floatval($evt['event_cost']['sibling_child_price']) >= 0)    
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
                                                        @endif
                                                        @if(is_numeric($evt['event_cost']['sibling_member_price']) && floatval($evt['event_cost']['sibling_member_price']) >= 0)    
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
                                                        @endif
                                                        @if(is_numeric($evt['event_cost']['reduced_price']) && floatval($evt['event_cost']['reduced_price']) >= 0)    
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
                                                        @endif

                                                        <div class="row registration-count-total reg-inp">
                                                            <div class="col-xs-9 col-md-10 text-right">
                                                                Summe:
                                                            </div>
                                                            <div class="col-xs-3 col-md-2 text-right">
                                                                <span class="price total_price_{{$evt['id']}}">0,00 €</span>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    @endif
                                                    <fieldset>
                                                        <legend class="reg-inp"><h4>Persönliche Angaben *</h4></legend>
                                                        <div class="form-group label-placeholder">
                                                            <label for="first_name" class="control-label">Vorname</label>
                                                            <input type="text" class="form-control" name="first_name" id="first_name_{{$evt['id']}}" required />
                                                        </div>
                                                        <div class="form-group label-placeholder reg-inp">
                                                            <label for="last_name" class="control-label">Nachname</label>
                                                            <input type="text" class="form-control" name="last_name" id="last_name_{{$evt['id']}}" required />
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
                                                                    <label for="city" class="control-label">Ort</label>
                                                                    <input type="text" class="form-control" name="city" id="city" required />
                                                                </div>  
                                                            </div>
                                                        </div>
                                                        <div class="form-group label-placeholder reg-inp">
                                                            <label for="phon" class="control-label">Telefonnummer</label>
                                                            <input type="text" class="form-control" name="phone" id="phone" required />
                                                        </div>
                                                        <div class="form-group label-placeholder reg-inp">
                                                            <label for="email" class="control-label">E-Mail</label>
                                                            <input type="email" class="form-control" name="email" id="email" required />
                                                        </div>
                                                    </fieldset>

                                                    <fieldset>
                                                        <div class="form-group">
                                                            <div class="checkbox">
                                                                <label>
                                                                    <input type="checkbox" name="member_chk" id="{{$evt['id']}}" /> Mitglied im Kunstverein in Bremen
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
                                                        <p>Die Zahlung erfolgt per Bankeinzug. Diese Einzugsermächtigung gilt nur für die hier aufge- führten Veranstaltungen. Danach erlischt sie. Den ggf. gesondert anfallenden Eintritt bezahle ich an der Kasse der Kunsthalle Bremen.</p>
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
                                                                    <input type="checkbox" name="conditions_of_participation" required /> Mit den <a href="http://www.kunsthalle-bremen.de/view/static/page/terms-of-conditions" target="_blank">Anmelde- und Teilnahmebedingungen</a> bin ich einverstanden.
                                                                </label>
                                                            </div>
                                                            <div class="checkbox" style="margin-top:25px;">
                                                                <label class="links-underlined">
                                                                    <input type="checkbox" name="newsletter" id="newsletter_chk_{{$evt['id']}}" /> Ich m&ouml;chte den E-Mail-Newsletter der Kunsthalle Bremen erhalten. Der Erhalt des Newsletters kann jederzeit durch einen telefonischen Hinweis oder &uuml;ber eine E-Mail an <a href="mailto:info@kunsthalle-bremen.de">info@kunsthalle-bremen.de</a> widerrufen werden.
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
                                                        <button type="submit" class="btn btn-raised btn-default" onclick="registerForEvent({{$evt['id']}}, event)">Jetzt zahlungspflichtig anmelden</button>
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

<script>
var curEventId = 0;
function setEventId(id) {
    curEventId = id;
}

function showEvent(index, slideNo) {
    var swiperDetails = new Swiper ('#calendar .swiper-container.detail', {
        loop: true,
    });
    var swiperControl = new Swiper ('#calendar .swiper-container.control', {
        loop: true,
        // control: swiperDetails,
        nextButton: '.month-next',
        prevButton: '.month-prev',
    });
    if($('.icon-up-'+curEventId).length) { $('.icon-up-'+curEventId)[0].click(); }
    swiperDetails.slideTo(slideNo); //, 300, true, true);
    swiperControl.slideTo(slideNo); //, 300, true, true);
    var scrollPos = $(".event_no_"+index).offset().top - 150;
    $('html, body').animate({
      scrollTop: scrollPos
    }, 700);
}

function registerForEvent(id, event) {
    event.preventDefault();
    console.log("registerForEvent("+id+") called");
    var $inputs = $('#reg_form_'+id+' :input');
    var frm = document.getElementById('reg_form_'+id);
    var formData = new FormData(frm);
    console.log("Form inputs for event id: "+id);
    
    var data = {};
    for(var i in $inputs) { 
        data[$inputs[i].name] = $inputs[i].value;
        if($inputs[i].name == 'newsletter') {
            console.log('Newsletter:--- '+ $inputs[i].checked);
            if($inputs[i].checked) {
                $inputs[i].value = 'on';
            } else {
                $inputs[i].value = 'off'
            }
            data[$inputs[i].name] = $inputs[i].value;
            break;
        }
        console.log("Inp: "+ $inputs[i].name + " : "+ $inputs[i].value); 
        console.log("T: " + typeof $inputs[i]);
    }
// return;    
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
        data: {ds},
        dataType: 'json',
        success:function(data) { 
                    var modal = kunsthalle.showModal('confirm_event_registration');
                    console.log('registerForEvent success..');
                    console.log(data);
                    $('.icon-up-'+id)[0].click();
                    return false;
                },
        error:  function(jqXHR, textStatus, errorThrown) {
                    console.log('registerForEvent failed.. ');
                    return false;
                }
    }); 
    return false;
}

function checkForm(form) {
    console.log('checkForm()..');
    var id = form.id;
    id = id.substr(id.lastIndexOf('_')+1, id.length);
    registerForEvent(id, event);
    $('.icon-up-'+id)[0].click();
    // console.log(form);
    return false;
}

</script>