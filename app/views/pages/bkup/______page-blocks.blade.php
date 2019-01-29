    @if(isset($pg_links))
        <div class="ce ce-submenu container-fluid">
            <div class="ce-submenu-title text-center">
                {{$page->title_de}}
            </div>
            <div class="opener">
                <a href="#" class="opener-close-link">
                    <span class="icon icon-up icon-red"></span>
                </a>
                <a href="#" class="opener-open-link">
                    <span class="icon icon-down icon-red"></span>
                </a>
            </div>
            <ul>
            @foreach($pg_links as $pl)
                <?php $link = "/".$menu_item.'/'.$pl->link;
                $has_calendar = false;
                $is_calendar = false;
                if(strtolower($pl->title_en) == 'calendar' || strtolower($pl->title_de) == 'kalender') {  $link = '/'.$pl->link .'/'. $menu_item; $is_calendar = true; 
                    if(count($calendar) > 0) { $has_calendar = true; }
                }
                ?>
                <li><a href="{{$link}}" class="btn btn-default btn-raised @if($pl->current_link) active @endif">{{$pl->title_de}}</a></li>
            @endforeach    
                <li><a href="#" class="btn btn-default btn-raised close-link"><span class="icon icon-up icon-white"></span></a></li>
            </ul>
        </div>
    @endif    

    <div class="ce ce-anchors container">
        <ul class="list-unstyled list-inline" />
    </div>

    <!-- content ende -->
    @foreach($pg_sections as $ps)
        
        @if($ps->type == 'h2text')
            <div class="ce ce-headline container">
                <h2 class="anchor" @if(isset($ps->anchor_title_de) && strlen($ps->anchor_title_de)) data-anchortext="{{$ps->anchor_title_de}}" @endif onclick="copySectionLink('{{$ps->anchor_title_de}}')">{{$ps->headline_de}}</h2>
                <p>{{$ps->intro_de}}</p>
            </div>                    
        @endif

        @if($ps->type == 'content')
            <div class="ce ce-text container anchor"  @if(isset($ps->anchor_title_de) && strlen($ps->anchor_title_de)) data-anchortext="{{$ps->anchor_title_de}}" @endif>{{$ps->content_de}}</div>
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

        @if($ps->type == 'youtube')
            <!-- video start -->
            <div class="ce ce-video container">
                <div class="video-wrapper">
                    <iframe src="https://www.youtube.com/embed/{{$ps->url}}" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
            <!-- video end -->
        @endif    

        @if($ps->type == 'audio')
            <div class="ce ce-video container">
                <div class="vrp-wrapper">
                    <iframe src="https://voicerepublic.com/embed/talks/{{$ps->url}}" frameborder='0' scrolling='no' allowfullscreen></iframe>
                </div>
            </div>
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

        @if($ps->type == 'image_grid' && (isset($ps->grid_images) && count($ps->grid_images)))
        <div class="ce ce-headline container">
            <h4></h4>
        </div>  
        <div class="ce ce-thumbnail-grid container">
        @foreach($ps->grid_images as $img)
            <a href="{{$img->url}}" title="#" class="ce-thumbnail">
                <img src="{{$DOMAIN}}/files/grid_image/{{$img->filename}}">
            </a>
        @endforeach    
        </div>
        <!-- thumbnail grid end -->
        @endif

    @endforeach

    @if($page->contacts && count($page->contacts))
        <!-- contact start -->
        <div class="ce ce-contact container">
            <h4 class="anchor">Ansprechpartner/innen:</h4>
            
            @foreach($page->contacts as $c)
                <div>
                    <a href="javascript:showContactForm('{{$c->email}}')">
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

    <!-- Sponsors -->
    @if(isset($sponsors) && count($sponsors))
        <!-- sponsors start -->
        <div class="ce ce-sponsors bg-white">
            <div class="container">
              @foreach($sponsors as $grp => $sps)    
                    <div>
                        <h4>{{$grp}}:</h4>
                        <ul class="list-inline">
                            @foreach($sps as $sp)
                                <li><a href="{{$sp->url}}" target="_blank" title="#"><img src="{{$DOMAIN}}/files/sponsors/{{$sp->logo}}" alt="#" /></a></li>
                            @endforeach
                        </ul>
                    </div>
            @endforeach    
            </div>
        </di v>  
        <!-- sponsors end -->
    @endif    

    <!-- Calendar -->
    @if( (isset($calendar) && count($calendar)) || (isset($show_calendar) && $show_calendar == 1))    
        <!-- Event calender as module start -->
        @include('pages.calendar-section')
        <!-- Event calender as module end -->       
    @endif    

    @if($show_membership_form)

        <div class="ce ce-membership bg-white with-bottom-line">    
            <div class="container">
                <form method="POST" action="/members/register-member">
                    <fieldset>
                        <legend><h3>Ich/wir möchte/n dem Kunstverein in Bremen beitreten</h3></legend>
                        <div class="form-group">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="membership" value="single" checked />
                                    Einzelmitglied (jährlich € 60,–)
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="membership" value="couple" />
                                    Ehepaar/Lebensgemeinschaft (jährlich € 90,–)
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="membership" value="family" />
                                    Familie mit Kindern bis zum 20. Lebensjahr im gemeinsamen Haushalt (jährlich € 90,–)
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="membership" value="pupil" />
                                    Schüler/Schülerin bis zum 20. Lebensjahr (jährlich € 22,–)
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="membership" value="study" />
                                    Studierende/Auszubildende bis zum 27. Lebensjahr (jährlich € 22,–)
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="membership" value="lifetime" />
                                    auf Lebenszeit (1.250,- Einzelmitglied bzw. 1.750,- Paar)
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="membership_as_gift" /> 
                                    Auswahl als Geschenkmitgliedschaft<br />
                                    <small>(Bitte unter Bemerkungen Name und Anschrift des Schenkenden eintragen.
                                    In diesem Fall ist nur die Zahlung per Lastschrift möglich, nicht per Rechnung.)</small>
                                </label>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend><h4>Persönliche Angaben</h4></legend>
                        <div class="form-group label-placeholder">
                            <label for="first_name" class="control-label">Vorname</label>
                            <input type="text" class="form-control" name="first_name" id="first_name" required />
                        </div>
                        <div class="form-group label-placeholder">
                            <label for="last_name" class="control-label">Nachname</label>
                            <input type="text" class="form-control" name="last_name" id="last_name" required />
                        </div>
                        <div class="form-group label-placeholder">
                            <label for="birthday" class="control-label">Geburtsdatum <small>(TT.MM.JJJJ)</small></label>
                            <input type="text" class="form-control" name="birthday" id="birthday" required />
                        </div>
                        <div class="form-group label-placeholder">
                            <label for="job" class="control-label">Beruf</label>
                            <input type="text" class="form-control" name="job" id="job" />
                        </div>
                        <div class="row">
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
                        <div class="row">
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
                        <div class="form-group label-placeholder">
                            <label for="phone" class="control-label">Telefonnummer</label>
                            <input type="text" class="form-control" name="phone" id="phone" />
                        </div>
                        <div class="form-group label-placeholder">
                            <label for="email" class="control-label">E-Mail-Adresse</label>
                            <input type="email" class="form-control" name="email" id="email" required />
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="age"/>
                                    Ich bin / wir sind zwischen 20 und 40 Jahren und möchten per E-Mail-Adresse zu den
                                    Veranstaltungen für junge Mitglieder eingeladen werden.<br />
                                    <small>(Bitte Geburtsdatum und E-Mail-Adresse-Adresse angeben!)</small>
                                </label>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend><h4><label for="comment">Bemerkung</label></h4></legend>
                        <div class="form-group">
                            <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                        </div>
                    </fieldset>
                    <fieldset class="membership-couple">
                        <legend><h4>Zusätzliche Angaben zu Ihrem Ehe- oder Lebenspartner</h4></legend>
                        <p>Bitte geben Sie hier den Namen und Geburtsdatum Ihres Ehe- oder Lebenspartners an:</p>
                        <div class="form-group label-placeholder">
                            <label for="partner_first_name" class="control-label">Vorname</label>
                            <input type="text" class="form-control" name="partner_first_name" id="partner_first_name" required />
                        </div>
                        <div class="form-group label-placeholder">
                            <label for="partner_last_name" class="control-label">Nachname</label>
                            <input type="text" class="form-control" name="partner_last_name" id="partner_last_name" required />
                        </div>
                        <div class="form-group label-placeholder">
                            <label for="partner_birthday" class="control-label">Geburtsdatum <small>(TT.MM.YYYY)</small></label>
                            <input type="text" class="form-control" name="partner_birthday" id="partner_birthday" required />
                        </div>
                    </fieldset>
                    <fieldset class="membership-family">
                        <legend><h4>Zusätzliche Angaben zur Familienmitgliedschaft</h4></legend>
                        <p>Bitte geben Sie hier die Namen und Geburtsdatum Ihres Ehe- oder Lebenspartners an:</p>
                        <div class="form-group label-placeholder">
                            <label for="family_first_name" class="control-label">Vorname</label>
                            <input type="text" class="form-control" name="family_first_name" id="family_first_name" required />
                        </div>
                        <div class="form-group label-placeholder">
                            <label for="family_last_name" class="control-label">Nachname</label>
                            <input type="text" class="form-control" name="family_last_name" id="family_last_name" required />
                        </div>
                        <div class="form-group label-placeholder">
                            <label for="family_birthday" class="control-label">Geburtsdatum <small>(TT.MM.JJJJ)</small></label>
                            <input type="text" class="form-control" name="family_birthday" id="family_birthday" required />
                        </div>
                        <p class="mt-30">Bitte geben Sie hier die Namen und Geburtsdaten Ihrer Kinder an:</p>
                        <div class="membership-family-children">
                            <div class="membership-family-child-dummy">
                                <div class="form-group label-placeholder">
                                    <label for="child_name" class="control-label">Vor- und Nachname des Kindes</label>
                                    <input type="text" class="form-control" name="children_names[]" id="child_name" required />
                                </div>
                                <div class="form-group label-placeholder">
                                    <label for="child_birthday" class="control-label">Geburtsdatum <small>(TT.MM.JJJJ)</small></label>
                                    <input type="text" class="form-control" name="children_birthdays[]" id="child_birthday" required />
                                </div>
                            </div>
                            <div class="membership-family-child">
                                <div class="form-group label-placeholder">
                                    <label for="child_name" class="control-label">Vor- und Nachname des Kindes</small></label>
                                    <input type="text" class="form-control" name="children_names[]" id="child_name" required />
                                </div>
                                <div class="form-group label-placeholder">
                                    <label for="child_birthday" class="control-label">Geburtsdatum <small>(TT.MM.JJJJ)</small></label>
                                    <input type="text" class="form-control" name="children_birthdays[]" id="child_birthday" required />
                                </div>
                            </div>
                        </div>
                        <div id="membership-family-child-configurator">
                            <div class="pt-15">
                                <a href="#" class="membership-family-child-add"><span class="icon icon-button-plus icon-red"></span> Kind hinzufügen</a>
                            </div>
                            <div class="pt-15">
                                <a href="#" class="membership-family-child-remove"><span class="icon icon-button-minus icon-red"></span> Kind entfernen</a>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend><h4>Zahlung</h4></legend>
                        <div class="radio mt-30">
                            <label>
                                <input type="radio" name="payment" value="bill" checked />
                                Per Rechnung 
                            </label>
                            <label>
                                <input type="radio" name="payment" value="debit" />
                                Per Lastschrift
                            </label>
                        </div>
                        <div class="form-group label-placeholder disabled">
                            <label for="iban" class="control-label">IBAN</label>
                            <input type="iban" class="form-control" name="iban" id="iban" disabled required />
                        </div>
                        <div class="form-group label-placeholder disabled">
                            <label for="bic" class="control-label">BIC</label>
                            <input type="text" class="form-control" name="bic" id="bic" disabled />
                        </div>
                        <div class="form-group label-placeholder disabled">
                            <label for="depositor" class="control-label">Kontoinhaber</label>
                            <input type="text" class="form-control" name="depositor" id="depositor" disabled required />
                        </div>
                        <div class="form-group label-placeholder disabled">
                            <label for="bank" class="control-label">Name der Bank</label>
                            <input type="text" class="form-control" name="bank" id="bank" disabled required />
                        </div>
                        <div class="membership-payment-debit" style="display: none;">
                            <h4>SEPA-Lastschriftmandat</h4>
                            <p>Gläubiger-Identifikationsnummer DE93KHB00000078029<br />
                            Mandatsreferenz (entnehmen Sie bitte dem Kontoauszug der ersten Abbuchung)</p>
                            <p>Ich ermächtige den Kunstverein in Bremen, Zahlungen von meinem Konto mittels Lastschrift
                            einzuziehen. Zugleich weise ich mein Kreditinstitut an, die vom Kunstverein auf
                            mein Konto gezogenen Lastschriften einzulösen.</p>
                            <p>Hinweis: Ich kann innerhalb von acht Wochen, beginnend mit dem Belastungsdatum, die
                            Erstattung des belasteten Betrages verlangen. Es gelten dabei die mit meinem Kreditinstitut
                            vereinbarten Bedingungen. Zahlungsart: Wiederkehrende Zahlung</p>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend><h4>Bitte senden Sie meine Mitgliederpost</h4></legend>
                        <div class="radio">
                            <label>
                                <input type="radio" name="mailing" value="email" checked />
                                Per E-Mail-Adresse
                            </label>
                            <label>
                                <input type="radio" name="mailing" value="post" />
                                Per Brief
                            </label>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend><h4>Newsletter</h4></legend>
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="newsletter"/>
                                    Ich möchte zusätzlich den E-Mail-Newsletter der Kunsthalle Bremen erhalten
                                </label>
                            </div>
                        </div>
                    </fieldset>
                    <button type="submit" class="btn btn-default btn-raised mt-30 mb-30">Antrag absenden</button>
                </form>
            </div>
        </div>

    @endif

    @if($dl_found)
        <!-- download start -->
        <div class="ce ce-download container-fluid ce-download-termsofuse">
            <h3 class="anchor">Downloads</h3>
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    @foreach($downloads as $dl)
                    <div class="swiper-slide ce-download-element" data-name="{{$dl['id']}}">
                        <div class="ce-download-element-overlay"></div>
                        <div>
                            <div>
                                <img src="{{$DOMAIN}}/files/downloads/{{$dl['thumb_image']}}" alt="{{$dl['link_title']}}" title="{{$dl['link_title']}}" />
                            </div>
                        </div>
                        <div>
                            {{$dl['link_title']}}
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

    <!-- Modal -->
    <div id="modals">
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
                        <h4>Download von Pressebildern mit besonderen Nutzungsbedingungen</h4>
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
                                <label for="termsOfUseName" class="control-label">Vorname, Name</label>
                                <input type="text" class="form-control" name="name" id="termsOfUseName" required />
                            </div>
                            <div class="form-group label-placeholder">
                                <label for="termsOfUseFirm" class="control-label">Medium / Redaktion</label>
                                <input type="text" class="form-control" name="firm" id="termsOfUseFirm" required />
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
                                        Die Nutzungsbedingungen und Bildcredits werden diesem Download als PDF beigefügt.

                                    </label>
                                </div>
                            </div>
                            <input type="hidden" name="termsofuse_files" id="termsofuse_files">
                            <button type="button" onclick="handleDownload()" class="btn btn-default btn-raised active dl-btn">Download</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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
        <div id="modal_confirm_member_registration">
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
                        <p>Wir haben Ihren Mitgliedsantrag für den Kunstverein in Bremen erhalten. Vielen Dank!
                        Den Mitgliedsausweis/die Mitgliedsausweise senden wir Ihnen innerhalb von 10 Tagen zu.</p>
                        <p>Ihr Kunstverein in Bremen</p>
                        <a href="javascript:kunsthalle.hideModal()" class="btn btn-default btn-raised active">OK</a>
                    </div>
                </div>
            </div>
        </div>
        <div id="modal_email_request">
            <div class="container">
                <div class="header">
                    <div class="text-center">
                        <a href="#"><img class="logo" src="/images/kunsthalle_bremen_logo_w.svg" alt="Kunsthalle Bremen" title="Kunsthalle Bremen" /></a>
                    </div>
                    <a href="javascript:kunsthalle.hideModal()" class="pull-right"><span class="icon icon-close icon-white"></span></a>
                </div>
                <div class="content">
                    <div class="ce-emailrequest">
                        <h4>Ihre E-Mail an die Kunsthalle Bremen:</h4>
                        <form id="contact_form" method="POST">
                            <div class="form-group label-placeholder">
                                <label for="emailrequestEmail" class="control-label">Ihre E-Mail</label>
                                <input type="email" class="form-control" name="email" id="emailrequestEmail" required />
                            </div>
                            <div class="form-group label-placeholder">
                                <label for="emailrequestName" class="control-label">Ihr Name, Vorname</label>
                                <input type="text" class="form-control" name="name" id="emailrequestName" required>
                            </div>
                            <div class="form-group label-placeholder">
                                <label for="emailrequestComment" class="control-label">Ihre Nachricht</label>
                                <textarea class="form-control" name="comment" id="emailrequestComment" rows="5" required></textarea>
                            </div>
                            <div class="mt-30">
                                <button type="button" class="btn btn-default btn-raised active" onclick="sendMessage()">Jetzt abschicken</button>
                            </div>
                            <input name="receiver_email" id="receiver_email" type="hidden">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="modal_confirm_email_request">
            <div class="container">
                <div class="header">
                    <div class="text-center">
                        <a href="#"><img class="logo" src="/images/kunsthalle_bremen_logo_w.svg" alt="Kunsthalle Bremen" title="Kunsthalle Bremen" /></a>
                    </div>
                    <a href="javascript:kunsthalle.hideModal()" class="pull-right"><span class="icon icon-close icon-white"></span></a>
                </div>
                <div class="content">
                    <div class="ce-confirmation">
                        <h4>Vielen Dank für Ihre Nachricht</h4>
                        <p>Wir werden Ihre E-Mail so schnell wie möglich beantworten.</p>
                        <a href="javascript:kunsthalle.hideModal()" class="btn btn-default btn-raised active">OK</a>
                    </div>
                </div>
            </div>
        </div>
        @include('includes.search_modal')
    </div>

    <input type="hidden" id="page_id" value="{{$page->id}}">
    <input type="hidden" id="dl_protected" value="{{$page->dl_protected}}">
    @if(isset($settings))
        <input type="hidden" id="dl_password" value="{{$settings->dl_password}}">
    @endif

    <div id="zip"></div>

@if(isset($settings))
    <script type="text/javascript"> var dl_password = '{{$settings->dl_password}}'; </script>
@endif

<script type="text/javascript">
function handleDownload() {
    console.log('Protected ? '+ $('#dl_protected').val());
    if($('#dl_protected').val() == '1') {
        console.log('Verifying password..');
        if($('#dl_password').val() != $('#termsOfUsePassword').val()) {
            $('#termsOfUsePassword').val('');
            return false;
        }
    }
    console.log('Continuing..');
    var list = $('#termsofuse_files').val();
    list = list.split(', ').join(',');
    var items = list.split(',');
    console.log(items)
    kunsthalle.hideModal('termsofuse');
    if(items.length > 0) {
        $.ajax({
            type: 'POST',
            url: '/handle-downloads',
            data: { 'ids': items, 'page_id': $('#page_id').val(), 'name': $('#termsOfUseName').val(), 'firm': $('#termsOfUseFirm').val(), 'publication_date':$('#dateOfPublication').val() },
            dataType: 'json',
            success:function(data) { 
                        console.log('handleDownload success....');
                        console.log(data);
                        if(data.item != undefined) {
                            window.location.href = data.item;
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
</script>
<!-- jquery -->
<script type="text/javascript">
function showConfirmation() {
    kunsthalle.showModal('confirm_member_registration');
}

var confirm = false;
@if(isset($action) && ($action == 'confirmation' || $action == 'bestaetigung'))
   confirm = true;    
@endif

$(function() {
    if(confirm) {
        console.log('showing confirmation');
        showConfirmation();
    }
});


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
        data: {ds},
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
<style>
.ce-headline p {
  font-family: Georgia, Times, Times New Roman, serif;
  font-size: 24.5px; 
  line-height:24px;
}
.ce-headline p {
  font-family: Georgia, Times, Times New Roman, serif;
  font-size: 20px; }
p a { text-decoration: underline; }
</style>

<script type="text/javascript">
$(function() {
    $('a').click(function(event) {
        $(this).blur();
    });
});

function showContactForm(email) {
    $('#receiver_email').val(email);
    kunsthalle.showModal('email_request');
}

function sendMessage() {
    // var formData = new FormData($('#contact_form')[0]);
    $.ajax({
        type: 'POST',
        url: '/send-message',
        data: { 'receiver_email': $('#receiver_email').val(), 'name': $('#emailrequestName').val(), 'email': $('#emailrequestEmail').val(), 'comment': $('#emailrequestComment').val() },
        dataType: 'json',
        success:function(data) { 
                    console.log('sendMessage success..');
                    console.log(data);
                    kunsthalle.hideModal('email_request');
                    showConfirmation();
                },
        error:  function(jqXHR, textStatus, errorThrown) {
                    console.log('sendMessage failed.. ');
                    return false;
                }
    });     
}

function showConfirmation() {
    kunsthalle.showModal('confirm_email_request');
}

function copySectionLink(section) {
    var url = document.URL;
    if(clipboard != undefined) {        
        if(url.indexOf('?section=') > -1) {
            url = url.substr(0, url.indexOf('?section='));
        }
        url += '?section='+section;
        console.log('Copying URL: '+ url);
        var isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;

        if(window.ffclipboard) {
            window.ffclipboard.setText(url);
            console.log('Copying for firefox');
        } else {
            clipboard.copy(url);
            console.log('Copying for Chrome');
        }
        console.log('Copy supported? '+ document.queryCommandSupported('copy'));
    }
}
</script>
