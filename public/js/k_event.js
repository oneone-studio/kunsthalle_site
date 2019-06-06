var showErr = false;

function getFormHTML(evt, reg_event_date, index, slideNo) {
    console.log('getFormHTML() called for '+ index);console.log(evt);
    var h = html = '';
    var isIE = detectIE();
    var disabled = (isIE == true) ? " disabled": '';

    if(evt.guide_name != undefined) {
        h += '<h3 class="detail-header">'+evt.guide_name+'</h3>';
        if(evt.detail_de != undefined) {
            h += decodeURIComponent(evt.detail_de);
        }
    }

    if(evt.event_image != undefined && evt.event_image.length > 0) {
        h += '<figure id="evt_image_'+evt.index+'" class="evt-image">'+
                '<img class="img-responsive" src="'+cms_domain+'/'+evt.event_image+'" alt="" title="" />'+
                '<figcaption>'+ evt.caption_de+ '</figcaption>'+
            '</figure>';
    }

    if(evt.exb_titles != undefined && evt.exb_titles.length > 0) {
        h += '<h4>Im Rahmen der Ausstellung</h4>' +
                '<ul class="list-unstyled links-underlined">';
                    for(var link in evt.exb_titles) {
                        h += '<li><span class="icon icon-arrow icon-s icon-inline"></span>'+
                               '<a href="/view/exhibitions/exb-page/$link">'+evt.exb_titles[i] +'</a></li>';
                    }
        h += '</ul>';
    }
    var boxes_included = false;
    if(evt.show_event_box_html && evt.event_box_html != undefined && evt.event_box_html.length > 0 && evt.as_series == 0) {
        h += '<h4>Weitere Termine</h4>'+'<div>';
    }    

    if(evt.show_event_box_html && evt.event_box_html != undefined && evt.event_box_html.length > 0) {
        h += evt.event_box_html;
        h += '</div>';
        boxes_included = true;
    } else if(evt.show_rep_dates_box_html && evt.has_rep_dates == true && evt.rep_dates_box_html.length > 0) {
        h += '<h4>Weitere Termine</h4>'+'<div>';
        h += evt.rep_dates_box_html;
        h += '</div>';
    }

    // Recurring event boxes
    if(evt.rec_evt_boxes != undefined && evt.rec_evt_boxes.length > 0 && !boxes_included) {
        for(var i in evt.rec_evt_boxes) {
            if(evt.rec_evt_boxes[i].indexOf(index) == -1) {
               h += evt.rec_evt_boxes[i]; 
            }
        }
    }

    console.log('Costs:-');console.log(evt.event_cost);
    h += '<dl class="dl-horizontal participants_'+evt.index+'">';
    if(evt.show_cost_label == true || evt.show_cost_label == 1) {
        var ep = evt.event_cost;
        // if(evt.hasCost == true) {
            h += '<dt>Kosten</dt>'+'<dd>';
        // }

        if(ep.regular_adult_price != undefined && ep.regular_adult_price != '' && (!isNaN(ep.regular_adult_price) && parseInt(ep.regular_adult_price) >= 0)) {
            h += ep.regular_adult_price + ' € Erwachsene(r)';
            if(evt.entrance.excluded == 1) { h += ' (zzgl. Eintritt)'; } h += '<br>';
        }

        if(ep.regular_child_price != undefined && ep.regular_child_price.length > 0 && !isNaN(ep.regular_child_price) && parseInt(ep.regular_child_price) >= 0) {
             h += ep.regular_child_price.replace('.00', '') + ' € Kind(er) <br>';
        }

        if(ep.member_adult_price != undefined && ep.member_adult_price.length > 0 && !isNaN(ep.member_adult_price) && parseInt(ep.member_adult_price) >= 0) {
             h += ep.member_adult_price.replace('.00', '') + ' € Mitglied(er) <br>';
        }

        if(ep.member_child_price != undefined && ep.member_child_price.length > 0 && !isNaN(ep.member_child_price) && parseInt(ep.member_child_price) >= 0) {
             h += ep.member_child_price.replace('.00', '') + ' € Kind(er) / Mitglied <br>';
        }

        if(ep.sibling_child_price != undefined && ep.sibling_child_price.length > 0 && !isNaN(ep.sibling_child_price) && parseInt(ep.sibling_child_price) >= 0) {
             h += ep.sibling_child_price.replace('.00', '') + ' € Geschwisterkind(er) <br>';
        }

        if(ep.sibling_member_price != undefined && ep.sibling_member_price.length > 0 && !isNaN(ep.sibling_member_price) && parseInt(ep.sibling_member_price) >= 0) {
             h += ep.sibling_member_price.replace('.00', '') + ' € Geschwisterkind(er) / Mitglied <br>';
        }

        if(ep.reduced_price != undefined && ep.reduced_price.length > 0 && !isNaN(ep.reduced_price) && parseInt(ep.reduced_price) >= 0) {
             h += ep.reduced_price.replace('.00', '') + " € ermäßigt <br>";
        }
        if(ep.reduced_price != undefined && ep.reduced_price.length > 0 && (!isNaN(ep.reduced_price) && ep.reduced_price) >= 0) { 
             ep.reduced_price.replace('.00', '') + " € ermäßigt ";
            if(evt.entrance.excluded == 1) { h += '(zzgl. Eintritt)'; } h += '<br>';
        }

        // Include list of package prices if applicable
        if(ep.pkg_regular_adult_price != undefined && ep.pkg_regular_adult_price != '' && (!isNaN(ep.pkg_regular_adult_price) && parseInt(ep.pkg_regular_adult_price) >= 0)) {
            h += ep.pkg_regular_adult_price + ' € Erwachsene';
            if(evt.entrance.excluded == 1) { h += ' (zzgl. Eintritt)'; } 
            h += ' / alle Termine<br>';
        }

        if(ep.pkg_regular_child_price != undefined && ep.pkg_regular_child_price.length > 0 && !isNaN(ep.pkg_regular_child_price) && parseInt(ep.pkg_regular_child_price) >= 0) {
             h += ep.pkg_regular_child_price.replace('.00', '') + ' € Kind(er) / alle Termine <br>';
        }

        if(ep.pkg_member_adult_price != undefined && ep.pkg_member_adult_price.length > 0 && !isNaN(ep.pkg_member_adult_price) && parseInt(ep.pkg_member_adult_price) >= 0) {
             h += ep.pkg_member_adult_price.replace('.00', '') + ' € Mitglied(er) / alle Termine <br>';
        }

        if(ep.pkg_member_child_price != undefined && ep.pkg_member_child_price.length > 0 && !isNaN(ep.pkg_member_child_price) && parseInt(ep.pkg_member_child_price) >= 0) {
             h += ep.pkg_member_child_price.replace('.00', '') + ' € Kind(er) / Mitglied / alle Termine <br>';
        }

        if(ep.pkg_sibling_child_price != undefined && ep.pkg_sibling_child_price.length > 0 && !isNaN(ep.pkg_sibling_child_price) && parseInt(ep.pkg_sibling_child_price) >= 0) {
             h += ep.pkg_sibling_child_price.replace('.00', '') + ' € Geschwisterkind(er) / alle Termine <br>';
        }

        if(ep.pkg_sibling_member_price != undefined && ep.pkg_sibling_member_price.length > 0 && !isNaN(ep.pkg_sibling_member_price) && parseInt(ep.pkg_sibling_member_price) >= 0) {
             h += ep.pkg_sibling_member_price.replace('.00', '') + ' € Geschwisterkind(er) / Mitglied / alle Termine <br>';
        }

        if(ep.pkg_reduced_price != undefined && ep.pkg_reduced_price.length > 0 && !isNaN(ep.pkg_reduced_price) && parseInt(ep.pkg_reduced_price) >= 0) {
             h += ep.pkg_reduced_price.replace('.00', '') + " € ermäßigt <br>";
        }
        if(ep.pkg_reduced_price != undefined && ep.pkg_reduced_price.length > 0 && (!isNaN(ep.pkg_reduced_price) 
           && ep.pkg_reduced_price) >= 0) { 
             ep.pkg_reduced_price.replace('.00', '') + " € ermäßigt ";
            if(evt.entrance.excluded == 1) { h += '(zzgl. Eintritt)'; } h += '<br>';
        }

        if(evt.entrance != null && evt.entrance != undefined) {
            if(evt.entrance.free == 1){ h += 'Eintritt frei'; }
            if(evt.entrance.included == 1) { h += 'inklusive Eintritt in die Kunsthalle Bremen'; }
            if(evt.entrance.entry_fee == 1) { h+= 'Eintritt in die Kunsthalle Bremen'; }
        }

        h += '<br><br></dd>';
    }      

    if(evt.registration_detail.length > 0) {
        h += '<dt>Anmeldung</dt>'+
             '<dd><p>'+decodeURIComponent(evt.registration_detail)+'</p></dd>';
    }

    if(evt.remarks.length > 0) {
        h += '<dt>Hinweis</dt>'+'<dd><p>'+decodeURIComponent(evt.remarks)+'</p></dd>';
    }
                    
    if(evt.place.length > 0) {
        h += '<dt>Ort</dt>'+'<dd>'+'<p>'+decodeURIComponent(evt.place)+'</p>';
        if(evt.google_map_url != undefined && evt.google_map_url.length > 0) {
            h += '<ul class="list-unstyled links-underlined">'+'<li>'+
                    '<span class="icon icon-arrow icon-s icon-inline"></span>'+
                    '<a href="'+evt.google_map_url+'">Google Maps</a>'+'</li>'+'</ul>';
        }
        h += '</dd>';
    }

    // Page link for more details
    if(evt.page_link != '' && evt.page_link != undefined && evt.page_link.length > 0 && evt.page_link_title != undefined && evt.page_link_title.length > 0) {
        var p_link = evt.page_link;
        if(p_link.indexOf('http://') > -1) { p_link = p_link.replace('http://', 'https://'); }
        if(p_link.indexOf('.de/de/') == -1) { p_link = p_link.replace('.de/', '.de/de/'); } 
        h += '<dt>Mehr Informationen</dt>'+'<dd><p>';
        if(evt.page_link_text != undefined && evt.page_link_text.length > 0) {
            h += decodeURIComponent(evt.page_link_text) + ' ';
        }
        h += '<a href="'+p_link+'" style="text-decoration:underline;">'+decodeURIComponent(evt.page_link_title)+'</a></p></dd>';
    }

    h += '</dl>';

    var jetzt_class = 'open-registration text-red';
    var opener_class = '';
    var reg_collapse_class = '';
    if(showErr != undefined && showErr) {
        jetzt_class = 'close-registration text-grey';
        opener_class = ' opener-open';
        reg_collapse_class = ' style="display:block;"';
    }

    if(evt.registration == 1) {
        h += '<div class="registration-opener">'+
                '<a id="open_reg_'+evt.index+'" href="#" class="'+jetzt_class+'">Jetzt anmelden</a>'+
                '<div class="opener'+opener_class+'">'+
                    '<a href="#" class="opener-close-link close-registration">'+
                        '<span class="icon icon-up icon-grey icon-up-'+evt.index+'"></span>'+
                    '</a>'+
                    '<a href="#" class="opener-open-link open-registration">'+
                        '<span class="icon icon-down icon-red"></span>'+
                    '</a>'+
                '</div>'+
            '</div>';
    }

    if(evt.registration == 1) {    

        var regular_adult_price = '';
        var regular_child_price = '';
        var member_adult_price = '';
        var member_child_price = '';
        var sibling_child_price = '';
        var sibling_member_price = '';
        var reduced_price = '';
        var pkg_regular_adult_price = '';
        var pkg_regular_child_price = '';
        var pkg_member_adult_price = '';
        var pkg_member_child_price = '';
        var pkg_sibling_child_price = '';
        var pkg_sibling_member_price = '';
        var pkg_reduced_price = '';

        h += '<div class="registration-wrapper panel-collapse collapse" '+reg_collapse_class+'>'+
            '<form method="POST" id="reg_form_'+evt.index+'" action="/register-for-event">';
            if(evt.show_cost_label == true) {                                                   
                h += '<fieldset class="registration-number-of-persons">'+
                    '<legend><h4>Anmeldung</h4></legend>'+
                    '<p>Hiermit melde ich folgende Anzahl Personen verbindlich zu oben stehender Veranstaltung an:</p>';
                    
                    if(evt.package != undefined && evt.package == 1) {
                        h += '<div class="form-group"><div class="checkbox"><label>';
                        
                        regular_adult_price = (evt.regular_adult_price > 0) ? evt.regular_adult_price : 0;
                        regular_child_price = (evt.regular_child_price > 0) ? evt.regular_child_price : 0;
                        member_adult_price = (evt.member_adult_price > 0) ? evt.member_adult_price : 0;
                        member_child_price = (evt.member_child_price > 0) ? evt.member_child_price : 0;
                        sibling_child_price = (evt.sibling_child_price > 0) ? evt.sibling_child_price : 0;
                        sibling_member_price = (evt.sibling_member_price > 0) ? evt.sibling_member_price : 0;
                        reduced_price = (evt.reduced_price > 0) ? evt.reduced_price : 0;
                        pkg_regular_adult_price = (evt.pkg_regular_adult_price > 0) ? evt.pkg_regular_adult_price : 0;
                        pkg_regular_child_price = (evt.pkg_regular_child_price > 0) ? evt.pkg_regular_child_price : 0;
                        pkg_member_adult_price = (evt.pkg_member_adult_price > 0) ? evt.pkg_member_adult_price : 0;
                        pkg_member_child_price = (evt.pkg_member_child_price > 0) ? evt.pkg_member_child_price : 0;
                        pkg_sibling_child_price = (evt.pkg_sibling_child_price > 0) ? evt.pkg_sibling_child_price : 0;
                        pkg_sibling_member_price = (evt.pkg_sibling_member_price > 0) ? evt.pkg_sibling_member_price : 0;
                        pkg_reduced_price = (evt.pkg_reduced_price > 0) ? evt.pkg_reduced_price : 0;

                        h += '<input type="checkbox" id="'+evt.index+'_package_check" name="pay_as_package" ' +
                              ' onclick="applyPackagePrice('+evt.index+','+
                                regular_adult_price+','+regular_child_price+','+member_adult_price+','+member_child_price+','+sibling_child_price+','+
                                sibling_member_price+','+reduced_price+','+pkg_regular_adult_price+','+pkg_regular_child_price+','+pkg_member_adult_price+','+
                                pkg_member_child_price+','+pkg_sibling_child_price+','+pkg_sibling_member_price+','+pkg_reduced_price+')" />'+
                                '<span class="checkbox-material"><span class="check"></span></span>'+
                                ' Alle Veranstaltungen als Paket buchen.</label></div></div>';
                    }
                    
                    h += '<div id="participant_err_msg_'+evt.index+'" class="row prt_err" style="display:none;"><div help-block" style="margin-left:20px;font-size:12px;color:red;">Dieses Feld ist ein Pflichtfeld.</div></div>';
                    
                    console.log("\nChecking price input..");
                    if(!isNaN(evt.event_cost.regular_adult_price) && parseInt(evt.event_cost.regular_adult_price) > -1) {    
                        console.log("\nShow price input..");
                        h += '<div class="row registration-count-item reg-inp">'+
                                '<div class="col-xs-3 col-sm-2 col1">'+
                                    '<a href="#" class="registration-count-increment">'+
                                        '<span class="icon icon-button-plus icon-red"></span>'+
                                    '</a>'+
                                        '<a href="#" class="registration-count-decrement">'+
                                        '<span class="icon icon-button-minus icon-red"></span>'+
                                    '</a>'+
                                '</div>'+
                                '<div class="col-xs-2 col-sm-2 col2">'+
                                    '<div class="form-group inline">'+
                                        '<input type="text" class="form-control participant price-inp" name="regular_adult_price" autocomplete="off" '+
                                        ' id="'+evt.index+'_regular_adult_price" placeholder="0" onkeydown="this.blur()" '+
                                        'data-price="'+evt.event_cost.regular_adult_price+'" '+disabled+'>'+
                                    '</div>'+
                                '</div>'+
                                '<div class="col-xs-4 col-sm-6 col3">'+
                                    '<label for="regular_adult_price">Erwachsene(r) </label>'+
                                '</div>'+
                                '<div class="col-xs-3 col-sm-2 text-right col4">'+
                                    '<span class="price">0,00 €</span>'+
                                '</div>'+
                            '</div>';
                    }

                    if(!isNaN(evt.event_cost.regular_child_price) && parseFloat(evt.event_cost.regular_child_price) >= 0) {    
                        h += '<div class="row registration-count-item children reg-inp">'+
                                '<div class="col-xs-3 col-sm-2 col1">'+
                                    '<a href="#" class="registration-count-increment">'+
                                        '<span class="icon icon-button-plus icon-red"></span>'+
                                    '</a>'+
                                        '<a href="#" class="registration-count-decrement">'+
                                        '<span class="icon icon-button-minus icon-red"></span>'+
                                    '</a>'+
                                '</div>'+
                                '<div class="col-xs-2 col-sm-2 col2">'+
                                    '<div class="form-group inline">'+
                                        '<input type="text" class="form-control participant price-inp" name="regular_child_price" autocomplete="off" '+
                                        ' id="'+evt.index+'_regular_child_price" placeholder="0" onkeydown="this.blur()" '+
                                        ' data-price="';
                        if(evt.event_cost != undefined) { h += evt.event_cost.regular_child_price; }
                        h += '" '+disabled+'></div>'+
                                '</div>'+
                                '<div class="col-xs-4 col-sm-6 col3">'+
                                    '<label for="regular_child_price">Kind(er)</label>'+
                                '</div>'+
                                '<div class="col-xs-3 col-sm-2 text-right col4">'+
                                    '<span class="price">0,00 €</span>'+
                                '</div>'+
                            '</div>';
                    }

                    if(!isNaN(evt.event_cost.member_adult_price) && parseFloat(evt.event_cost.member_adult_price) >= 0) {   
                        h += '<div class="row registration-count-item reg-inp">'+
                                '<div class="col-xs-3 col-sm-2 col1">'+
                                    '<a href="#" class="registration-count-increment">'+
                                        '<span class="icon icon-button-plus icon-red"></span>'+
                                    '</a>'+
                                        '<a href="#" class="registration-count-decrement">'+
                                        '<span class="icon icon-button-minus icon-red"></span>'+
                                    '</a>'+
                                '</div>'+
                                '<div class="col-xs-2 col-sm-2 col2">'+
                                    '<div class="form-group inline">'+
                                        '<input type="text" class="form-control participant price-inp" name="member_adult_price" autocomplete="off" '+
                                        ' id="'+evt.index+'_member_adult_price" placeholder="0" onkeydown="this.blur()" '+
                                        ' data-price="';
                        if(evt.event_cost != undefined) { h += evt.event_cost.member_adult_price; }
                        h += '" '+disabled+'></div>'+
                                '</div>'+
                                '<div class="col-xs-4 col-sm-6 col3">'+
                                    '<label for="member_adult_price">Mitglied(er)</label>'+
                                '</div>'+
                                '<div class="col-xs-3 col-sm-2 text-right col4">'+
                                    '<span class="price">0,00 €</span>'+
                                '</div>'+
                            '</div>';
                    }

                    if(!isNaN(evt.event_cost.member_child_price) && parseFloat(evt.event_cost.member_child_price) >= 0) { 
                        h += '<div class="row registration-count-item children reg-inp">'+
                                '<div class="col-xs-3 col-sm-2 col1">'+
                                    '<a href="#" class="registration-count-increment">'+
                                        '<span class="icon icon-button-plus icon-red"></span>'+
                                    '</a>'+
                                        '<a href="#" class="registration-count-decrement">'+
                                        '<span class="icon icon-button-minus icon-red"></span>'+
                                    '</a>'+
                                '</div>'+
                                '<div class="col-xs-2 col-sm-2 col2">'+
                                    '<div class="form-group inline">'+
                                        '<input type="text" class="form-control participant price-inp" name="member_child_price" autocomplete="off" '+
                                        ' id="'+evt.index+'_member_child_price" placeholder="0" onkeydown="this.blur()" '+
                                            ' data-price="';
                        if(evt.event_cost != undefined) { h += evt.event_cost.member_child_price; }
                        h += '" '+disabled+'></div>'+
                                '</div>'+
                            '<div class="col-xs-4 col-sm-6 col3">'+
                                '<label for="member_child_price">Kind(er)/ Mitglied</label>'+
                            '</div>'+
                            '<div class="col-xs-3 col-sm-2 text-right col4">'+
                                '<span class="price">0,00 €</span>'+
                            '</div>'+
                        '</div>';
                    }

                    if(!isNaN(evt.event_cost.sibling_child_price) && parseFloat(evt.event_cost.sibling_child_price) >= 0) {    
                        h += '<div class="row registration-count-item children reg-inp">'+
                                '<div class="col-xs-3 col-sm-2 col1">'+
                                    '<a href="#" class="registration-count-increment">'+
                                        '<span class="icon icon-button-plus icon-red"></span>'+
                                    '</a>'+
                                        '<a href="#" class="registration-count-decrement">'+
                                        '<span class="icon icon-button-minus icon-red"></span>'+
                                    '</a>'+
                                '</div>'+
                                '<div class="col-xs-2 col-sm-2 col2">'+
                                    '<div class="form-group inline">'+
                                        '<input type="text" class="form-control participant price-inp" name="sibling_child_price" autocomplete="off" '+
                                        ' id="'+evt.index+'_sibling_child_price" placeholder="0" onkeydown="this.blur()" '+
                                            ' data-price="';
                        if(evt.event_cost != undefined) { h += evt.event_cost.sibling_child_price; }
                        h += '" '+disabled+'></div>'+
                            '</div>'+
                            '<div class="col-xs-4 col-sm-6 col3">'+
                                '<label for="regular_adult_rp">Geschwisterkind(er)</label>'+
                            '</div>'+
                            '<div class="col-xs-3 col-sm-2 text-right col4">'+
                                '<span class="price">0,00 €</span>'+
                            '</div>'+
                        '</div>';
                    }

                    if(!isNaN(evt.event_cost.sibling_member_price) && parseFloat(evt.event_cost.sibling_member_price) >= 0) {   
                        h += '<div class="row registration-count-item children reg-inp">'+
                                '<div class="col-xs-3 col-sm-2 col1">'+
                                    '<a href="#" class="registration-count-increment">'+
                                        '<span class="icon icon-button-plus icon-red"></span>'+
                                    '</a>'+
                                        '<a href="#" class="registration-count-decrement">'+
                                        '<span class="icon icon-button-minus icon-red"></span>'+
                                    '</a>'+
                                '</div>'+
                                '<div class="col-xs-2 col-sm-2 col2">'+
                                    '<div class="form-group inline">'+
                                       '<input type="text" class="form-control participant price-inp" name="sibling_member_price" autocomplete="off" '+
                                       ' id="'+evt.index+'_sibling_member_price" placeholder="0" onkeydown="this.blur()" '+
                                            ' data-price="';
                        if(evt.event_cost != undefined) { h += evt.event_cost.sibling_member_price; }
                        h += '" '+disabled+'></div>'+
                            '</div>'+
                            '<div class="col-xs-4 col-sm-6 col3">'+
                                '<label for="sibling_member_price">Geschwisterkind(er) / Mitglied</label>'+
                            '</div>'+
                            '<div class="col-xs-3 col-sm-2 text-right col4">'+
                                '<span class="price">0,00 €</span>'+
                            '</div>'+
                        '</div>';
                    }

                    if(!isNaN(evt.event_cost.reduced_price) && parseFloat(evt.event_cost.reduced_price) >= 0) {  
                        h += '<div class="row registration-count-item reg-inp">'+
                                '<div class="col-xs-3 col-sm-2 col1">'+
                                    '<a href="#" class="registration-count-increment">'+
                                        '<span class="icon icon-button-plus icon-red"></span>'+
                                    '</a>'+
                                        '<a href="#" class="registration-count-decrement">'+
                                        '<span class="icon icon-button-minus icon-red"></span>'+
                                    '</a>'+
                                '</div>'+
                                '<div class="col-xs-2 col-sm-2 col2">'+
                                    '<div class="form-group inline">'+
                                        '<input type="text" class="form-control participant" name="reduced_price" autocomplete="off" '+
                                        ' id="'+evt.index+'_reduced_price" placeholder="0" onkeydown="this.blur()" '+
                                            ' data-price="';
                        if(evt.event_cost != undefined) { h += evt.event_cost.reduced_price; }
                        h += '" '+disabled+'></div>'+
                            '</div>'+
                            '<div class="col-xs-4 col-sm-6 col3">'+
                                '<label for="reduced_price">ermäßigt</label>'+
                            '</div>'+
                            '<div class="col-xs-3 col-sm-2 text-right col4">'+
                                '<span class="price">0,00 €</span>'+
                            '</div>'+
                        '</div>';
                    }

                    h += '<div class="row registration-count-total reg-inp">'+
                            '<div class="col-xs-9 col-md-10 text-right">'+
                                'Summe:'+
                            '</div>'+
                            '<div class="col-xs-3 col-md-2 text-right">'+
                                '<span class="price total_price_'+evt.index+'" onkeydown="this.blur()">0,00 €</span>'+
                            '</div>'+
                        '</div>'+
                    '</fieldset>';
                }
                h += '<fieldset>'+
                    '<legend class="reg-inp"><h4>Persönliche Angaben *</h4></legend>'+
                    '<div class="form-group label-placeholder is-empty">'+
                        '<label for="first_name" class="control-label">Vorname</label>'+
                        '<input type="text" class="form-control" name="first_name" id="first_name_'+evt.index+'" required />'+
                    '</div>'+
                    '<div class="form-group label-placeholder is-empty reg-inp">'+
                        '<label for="last_name" class="control-label">Nachname</label>'+
                        '<input type="text" class="form-control" name="last_name" id="last_name_'+evt.index+'" required />'+
                    '</div>'+
                    '<div class="row reg-inp">'+
                        '<div class="col-md-8">'+
                            '<div class="form-group label-placeholder is-empty">'+
                                '<label for="street" class="control-label">Straße</label>'+
                                '<input type="text" class="form-control" name="street" id="street_'+evt.index+'" required />'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-md-4">'+
                            '<div class="form-group label-placeholder is-empty">'+
                                '<label for="streetno" class="control-label">Hausnummer</label>'+
                                '<input type="text" class="form-control" name="streetno" id="streetno_'+evt.index+'" required />'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="row reg-inp">'+
                        '<div class="col-md-4">'+
                            '<div class="form-group label-placeholder is-empty">'+
                                '<label for="zip" class="control-label">PLZ</label>'+
                                '<input type="text" class="form-control" name="zip" id="zip_'+evt.index+'" required />'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-md-8">'+
                            '<div class="form-group label-placeholder is-empty">'+
                                '<label for="city" class="control-label">Ort</label>'+
                                '<input type="text" class="form-control" name="city" id="city_'+evt.index+'" required />'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="form-group label-placeholder is-empty reg-inp">'+
                        '<label for="phon" class="control-label">Telefonnummer</label>'+
                        '<input type="text" class="form-control" name="phone" id="phone_'+evt.index+'" required />'+
                    '</div>'+
                    '<div class="form-group label-placeholder is-empty reg-inp email-inp">'+
                        '<label for="email" class="control-label">E-Mail</label>'+
                        '<input type="email" class="form-control" name="email" id="email_'+evt.index+'" required />'+
                        '<div style="clear:both;"></div>'+
                    '</div>'+
                '</fieldset>'+
                '<fieldset>'+
                    '<div class="form-group">'+
                        '<div class="checkbox">'+
                            '<label>'+
                                '<input type="checkbox" name="member_chk" id="member_chk'+evt.index+'" /><span class="checkbox-material"><span class="check"></span></span> Mitglied im Kunstverein in Bremen'+
                            '</label>'+
                        '</div>'+
                    '</div>'+
                    '<div class="form-group label-placeholder is-empty disabled">'+
                        '<label for="artclubnumber" class="control-label">Kunstverein-Mitgliedsnummer</label>'+
                        '<input type="text" class="form-control" name="member_no" id="member_no_'+evt.index+'" required disabled />'+
                    '</div>'+
                '</fieldset>'+
                '<fieldset class="registration-children-info" data-for="'+evt.index+'_regular_child_price">'+
                    '<legend><h4>Angaben zu den Kindern *</h4></legend>'+
                    '<div class="row registration-children-info-dummy">'+
                        '<div class="col-sm-8 col-md-10">'+
                            '<div class="form-group label-placeholder is-empty">'+
                                '<label class="control-label">Name</label>'+
                                '<input type="text" class="form-control" name="children_names[placeholder]" />'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-sm-4 col-md-2">'+
                            '<div class="form-group label-placeholder is-empty">'+
                                '<label class="control-label">Alter</label>'+
                                '<input type="number" min="1" max="21" class="form-control" name="children_ages[placeholder]" required />'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</fieldset>'+
                '<fieldset class="registration-children-info" data-for="'+evt.index+'_member_child_price">'+
                    '<legend><h4>Angaben zu den Kindern / Mitglied *</h4></legend>'+
                    '<div class="row registration-children-info-dummy">'+
                        '<div class="col-sm-8 col-md-10">'+
                            '<div class="form-group label-placeholder is-empty">'+
                                '<label class="control-label">Name</label>'+
                                '<input type="text" class="form-control" name="children_member_names[placeholder]" />'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-sm-4 col-md-2">'+
                            '<div class="form-group label-placeholder is-empty">'+
                                '<label class="control-label">Alter</label>'+
                                '<input type="number" min="1" max="21" class="form-control" name="children_member_ages[placeholder]" required />'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</fieldset>'+
                '<fieldset class="registration-children-info" data-for="'+evt.index+'_sibling_child_price">'+
                    '<legend><h4>Angaben zu den Geschwisterkindern *</h4></legend>'+
                    '<div class="row registration-children-info-dummy">'+
                        '<div class="col-sm-8 col-md-10">'+
                            '<div class="form-group label-placeholder is-empty">'+
                                '<label class="control-label">Name</label>'+
                                '<input type="text" class="form-control" name="children_sibling_names[placeholder]" />'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-sm-4 col-md-2">'+
                            '<div class="form-group label-placeholder is-empty">'+
                                '<label class="control-label">Alter</label>'+
                                '<input type="number" min="1" max="21" class="form-control" name="children_sibling_ages[placeholder]" required />'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</fieldset>'+
                '<fieldset class="registration-children-info" data-for="'+evt.index+'_sibling_member_price">'+
                    '<legend><h4>Angaben zu den Geschwisterkindern / Mitglied *</h4></legend>'+
                    '<div class="row registration-children-info-dummy">'+
                        '<div class="col-sm-8 col-md-10">'+
                            '<div class="form-group label-placeholder is-empty">'+
                                '<label class="control-label">Name</label>'+
                                '<input type="text" class="form-control" name="member_sibling_names[placeholder]" />'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-sm-4 col-md-2">'+
                            '<div class="form-group label-placeholder is-empty">'+
                                '<label class="control-label">Alter</label>'+
                                '<input type="number" min="1" max="21" class="form-control" name="member_sibling_ages[placeholder]" required />'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</fieldset>'+

                '<fieldset class="reg-inp">';

            if(!evt.hasZeroPrice) {
                h += '<legend><h4>Zahlung *</h4></legend>'+
                        '<p>Die Zahlung erfolgt per Bankeinzug. Diese Einzugsermächtigung gilt nur für die hier aufgeführten Veranstaltungen. Danach erlischt sie. Den ggf. gesondert anfallenden Eintritt bezahle ich an der Kasse der Kunsthalle Bremen.</p>'+
                        '<div class="form-group label-placeholder is-empty">'+
                            '<label for="iban" class="control-label">IBAN</label>'+
                            '<input type="text" class="form-control" name="iban" id="iban_'+evt.index+'" required />'+
                        '</div>'+
                        '<div class="form-group label-placeholder is-empty">'+
                            '<label for="depositor" class="control-label">Kontoinhaber</label>'+
                            '<input type="text" class="form-control" name="depositor" id="depositor_'+evt.index+'" required />'+
                        '</div>'+
                        '<div class="form-group label-placeholder is-empty">'+
                            '<label for="bank" class="control-label">Kreditinstitut</label>'+
                            '<input type="text" class="form-control" name="bank" id="bank_'+evt.index+'" required />'+
                        '</div><input type="hidden" name="iban_required" value="1">';
            }        

            h += '<div class="form-group">'+
                        '<div class="checkbox">'+
                            '<label class="links-underlined">'+
                                '<input type="checkbox" name="conditions_of_participation" id="conditions_of_participation_'+evt.index+'" required /><span class="checkbox-material"><span class="check"></span></span> Mit den <a href="https://www.kunsthalle-bremen.de/de/view/static/page/anmeldebedingungen" target="_blank">Anmelde- und Teilnahmebedingungen</a> bin ich einverstanden.'+
                            '</label>'+
                        '</div>'+
                    '</div>'+
                    '<div class="form-group">'+
                        '<div class="checkbox" style="margin-top:25px;">'+
                            '<label class="links-underlined">'+
                                '<input type="checkbox" name="newsletter" id="newsletter_chk_evt.id" /><span class="checkbox-material"><span class="check"></span></span> Ich m&ouml;chte den E-Mail-Newsletter der Kunsthalle Bremen erhalten. Der Erhalt des Newsletters kann jederzeit durch einen telefonischen Hinweis oder &uuml;ber eine E-Mail an <a href="mailto:info@kunsthalle-bremen.de">info@kunsthalle-bremen.de</a> widerrufen werden.'+
                            '</label>'+
                        '</div>'+
                    '</div>'+
                '</fieldset>'+
                '<div class="text-center mt-15 mb-15">'+
                    '<a href="#" class="close-registration" title="#">'+
                        '<span class="icon icon-close icon-red"></span>'+
                    '</a>'+
                '</div>'+
                '<div>'+
                    '<button id="submit_'+evt.index+'" type="submit" class="btn btn-raised btn-default"'+
                    '>Jetzt zahlungspflichtig anmelden</button>'+
                '</div>'+
                '<input name="menu_item" type="hidden" value="">'+
                '<input name="link" type="hidden" value="">'+
                '<input name="page_type" type="hidden" value="">'+
                '<input name="section" type="hidden" value="">'+
                '<input name="page_title" type="hidden" value="">'+
                '<input name="id" type="hidden" value="'+evt.id+'">'+
                '<input name="reg_event_date" id="reg_event_date" type="hidden" value="'+reg_event_date+'">'+
            '</form>'+
        '</div>';
    }

    // console.log("final HTML\n"+h);
    return h;
}

// Get participant count
function getParticipantCount() {
    console.log('getParticipantCount() called' +"\ncurEventIndex: "+curEventIndex);
    var count = 0;
    if(curEventIndex != undefined && curEventIndex.length > 0) {
        console.log('getParticipantCount().. '+ curEventIndex);
        console.log('pCount-['+curEventIndex+']: '+ pCount);

        if($('#'+curEventIndex+'_regular_adult_price').length && $('#'+curEventIndex+'_regular_adult_price').val() != '') { 
            count += parseInt($('#'+curEventIndex+'_regular_adult_price').val());
            console.log('Check-[regular_adult_price]: '+ $('#'+curEventIndex+'_regular_adult_price').val());
        }
        if($('#'+curEventIndex+'_regular_child_price').length && $('#'+curEventIndex+'_regular_child_price').val() != '') { 
            count += parseInt($('#'+curEventIndex+'_regular_child_price').val());
            console.log('Check-[regular_child_price]: '+ $('#'+curEventIndex+'_regular_child_price').val());
        }
        if($('#'+curEventIndex+'_member_adult_price').length && $('#'+curEventIndex+'_member_adult_price').val() != '') { 
            count += parseInt($('#'+curEventIndex+'_member_adult_price').val()); 
            console.log('Check-[member_adult_price]: '+ $('#'+curEventIndex+'_member_adult_price').val());
        }
        if($('#'+curEventIndex+'_member_child_price').length && $('#'+curEventIndex+'_member_child_price').val() != '') { 
            count += parseInt($('#'+curEventIndex+'_member_child_price').val()); 
            console.log('Check-[member_child_price]: '+ $('#'+curEventIndex+'_member_child_price').val());
        }
        if($('#'+curEventIndex+'_sibling_child_price').length && $('#'+curEventIndex+'_sibling_child_price').val() != '') { 
            count += parseInt($('#'+curEventIndex+'_sibling_child_price').val()); 
            console.log('Check-[sibling_child_price]: '+ $('#'+curEventIndex+'_sibling_child_price').val());
        }
        if($('#'+curEventIndex+'_sibling_member_price').length && $('#'+curEventIndex+'_sibling_member_price').val() != '') { 
            count += parseInt($('#'+curEventIndex+'_sibling_member_price').val()); 
            console.log('Check-[sibling_member_price]: '+ $('#'+curEventIndex+'_sibling_member_price').val());
        }
        if($('#'+curEventIndex+'_reduced_price').length && $('#'+curEventIndex+'_reduced_price').val() != '') { 
            count += parseInt($('#'+curEventIndex+'_reduced_price').val()); 
            console.log('Check-[reduced_price]: '+ $('#'+curEventIndex+'_reduced_price').val());
        }
    }
    console.log('Count: '+count);

    if(pCount != undefined && !isNaN(pCount)) { return pCount; }

    return count;
}

// Check participants - disallow 0
function checkParticipants() {
    participant_count = getParticipantCount();
    console.log("\nParticipant count[c-2]: "+participant_count);
    if(participant_count < 1) {
        $('.prt_err').show();
        $('.participant').first().trigger('focus');
        if($(".participants_"+curEventIndex).length) {
            var scrollPos = $(".participants_"+curEventIndex).offset().top + 40;
            $('html, body').animate({
              scrollTop: scrollPos
            }, 700);
        }

        return false;
    }
    // return true;
}

// detect IE
function detectIE() {
  var ua = window.navigator.userAgent;
  var msie = ua.indexOf('MSIE ');
  if (msie > 0) {
    // IE 10 or older => return version number
    return true;
  }
  var trident = ua.indexOf('Trident/');
  if (trident > 0) {
    // IE 11 => return version number
    var rv = ua.indexOf('rv:');
    return true;
  }
  var edge = ua.indexOf('Edge/');
  if (edge > 0) {
    // Edge (IE 12+) => return version number
    return true;
  }
  // other browser
  return false;
}

function showCfmMsg(modal) {
    kunsthalle.showModal(modal);
}
