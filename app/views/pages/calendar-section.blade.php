<?php 
date_default_timezone_set('Europe/Berlin'); 
setlocale(LC_TIME, "de_DE");
?>
<script type="text/javascript">
var curEventId = 0;
var curEventIndex = 0;
var hasCost = false;
var eventSrc = '';
var hasEventImage = false;
var pCount = 0;

function setEventId(id, reg_event_date, index, slideNo, hasImage) {
    pCount = 0;
    hasEventImage = hasImage;
    console.log("setEventId("+id+", "+reg_event_date+", "+index+", "+slideNo+", "+hasImage+") called");
    if(curEventId > 0) { 
        // $('opener_'+curEventIndex).removeClass('opener-open');
        $('.up_'+curEventIndex).trigger('click'); 
        $('#event_block_'+curEventIndex).html('');
        refreshSwiper();
        console.log('Closed previous event..'+ curEventId +' _ '+ curEventIndex); 
    }
    curEventId = id;
    curEventIndex = index;
    
    // Get event data and embed form
    $.ajax({
        type: 'GET',
        url: '/get-event-data',
        data: {'id': id, 'index': index},
        dataType: 'json',
        success:function(data) { 
            console.log('Get event data success..');
            console.log(data);
            if(data.event != undefined && $('#event_block_'+index).length) {
                if(data.event.hasCost != undefined) {
                    hasCost = data.event.hasCost;
                }
                // inject form HTML
                var html = getFormHTML(data.event, reg_event_date, index, slideNo);
                $(':not(.swiper-slide-duplicate) #event_block_'+index).html(html);
                var scrollPos = $('.event_no_'+index).offset().top - 68;
                $('html, body').animate({ scrollTop: scrollPos }, 500);
                // update slider
                refreshSwiper();
            }
        },
        error:  function(jqXHR, textStatus, errorThrown) {
                    console.log('Get event data failed..');
        }
    }); 
}

function setClickTarget(type) {
    eventSrc = type;
}

var cal_filter_date = '';
function reloadCalendarFilter(filter, resetCurDate) {
    // alert(resetCurDate);
    $.ajax({
        type: 'GET',
        url: '/get-filtered-dates',
        data: {'filter': filter},
        dataType: 'json',
        success:function(data) { 
            console.log('reloadCalendarFilter("'+filter+'") success..');
            console.log(data);
            if(filter.toLowerCase() != 'all' && filter != '*') {
                if(data.event_dates != undefined) {
                    for(var i in data.event_dates) {
                        if($('.cal_filter_date_'+data.event_dates[i]).length) {
                           $('.cal_filter_date_'+data.event_dates[i]).addClass('date-selector-nodates');//.removeClass('date-selector-currentdate'); 
                        }
                    }
                }
                if(data.filtered_dates != undefined) {
                    var fdates = data.filtered_dates;
                    for(var i in fdates) {
                        if($('.cal_filter_date_'+fdates[i]).length) {
                           $('.cal_filter_date_'+fdates[i]).removeClass('date-selector-nodates'); 
                        }
                    }
                }
            }
            if(filter == '*') {
                $('.cal_filter_date_').addClass('date-selector-nodates');
                if(data.event_dates != undefined) {
                    for(var i in data.event_dates) {
                        if($('.cal_filter_date_'+data.event_dates[i]).length) {
                           $('.cal_filter_date_'+data.event_dates[i]).removeClass('date-selector-nodates'); 
                        }
                    }
                }
            }
            if(data.current_date != undefined && resetCurDate) {
                // if($('.cal_filter_date_'+cal_filter_date).length) {
                    $('.cal_filter_date_'+cal_filter_date).removeClass('date-selector-currentdate'); 
                // }
                $('.cal_filter_date_'+data.current_date).addClass('date-selector-currentdate');
                cal_filter_date = data.current_date;
            }
            refreshSwiper();
        },
        error:  function(jqXHR, textStatus, errorThrown) {
                    console.log('reloadCalendarFilter("'+filter+'") failed..');
        }
    }); 
}

var cal_clicked = false;
function setCalEvents(date_ch, from_date, is_active) {
    setClickTarget('cal');
    cal_clicked = true;
    // if(cal_filter_date == from_date) {
    //     if($('.cal_filter_date_'+from_date).length) {
    //         $('.cal_filter_date_'+from_date).removeClass('date-selector-currentdate');
    //     }
    //     cal_filter_date = '';
    //     showAllCalendarEvents();
    // } else {
        // Continue only if valid event date is selected
        if(is_active) {

            if($('.cal_filter_date_'+cal_filter_date).length) {
                $('.cal_filter_date_'+cal_filter_date).removeClass('date-selector-currentdate'); 
            }
            $('.cal_filter_date_'+from_date).addClass('date-selector-currentdate');
            // Try to get scrollPos of valid matching <article>
            var els = $('article[class*="event_art_'+from_date+'"]');
            console.log(els);        
            var elId = null;
            var scrollPos = 0;
            var artFound = false;
            for(var i in els) {
                if(!isNaN(i)) {
                    if(els[i].style.display != 'none') {
                        elId = els[i].id;
                        artFound = true;
                        break;
                    }
                }
            }
            if(elId != null) {
                // scrollPos = $('.cal_filter_date_'+from_date).offset().top - 148;
                scrollPos = $('#'+elId).offset().top - 148;
            }
            // Scroll to clicked date
            if($('.event_art_'+from_date).length) {
                console.log('scrollPos: '+ scrollPos);
                $('html, body').animate({ scrollTop: scrollPos }, 800);
            }
        }
    // }
    cal_filter_date = from_date;
}

function refreshSwiper() {
    $('.swiper-container.detail').data('swiper').onResize(); 
    // Initiate validator
    $.material.init();
    $('#calendar form').each(function() {
        $(this).validate({
            rules: {
                iban: { iban: true },
                bic: { bic: true }
            }
        });
    });
}

function copyEventLink(index, slideNo) {
    var url = 'http://kunsthalle-bremen.de/calendar/besuch-planen/'+index+'_'+slideNo;
    if(clipboard != undefined) {        
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

function applyPackagePrice(index, 
            regular_adult_price, 
            regular_child_price, 
            member_adult_price, 
            member_child_price, 
            sibling_child_price, 
            sibling_member_price, 
            reduced_price, 
            pkg_regular_adult_price, 
            pkg_regular_child_price, 
            pkg_member_adult_price, 
            pkg_member_child_price, 
            pkg_sibling_child_price, 
            pkg_sibling_member_price, 
            pkg_reduced_price
        ) {
    console.log('applyPackagePrice called..');
    console.log(index);
    if($('#'+index+'_package_check').is(':checked')) {
        $('#'+index+'_package_check').val('on');    

        if($('#'+index+'_regular_adult_price').length) { 
            $('#'+index+'_regular_adult_price').attr('data-price', pkg_regular_adult_price).data('price', pkg_regular_adult_price).parents('.registration-count-item form-control').trigger('change'); 
            $('#'+index+'_regular_adult_price').trigger('change');
        }
        if($('#'+index+'_regular_child_price').length) { 
            $('#'+index+'_regular_child_price').attr('data-price', pkg_regular_child_price).data('price', pkg_regular_child_price).parents('.registration-count-item form-control').trigger('change'); 
            $('#'+index+'_regular_child_price').trigger('change');
        }
        if($('#'+index+'_member_adult_price').length) { 
            $('#'+index+'_member_adult_price').attr('data-price', pkg_member_adult_price).data('price', pkg_member_adult_price).parents('.registration-count-item form-control').trigger('change'); 
            $('#'+index+'_member_adult_price').trigger('change');
        }
        if($('#'+index+'_member_child_price').length) { 
            $('#'+index+'_member_child_price').attr('data-price', pkg_member_child_price).data('price', pkg_member_child_price).parents('.registration-count-item form-control').trigger('change'); 
            $('#'+index+'_member_child_price').trigger('change');
        }
        if($('#'+index+'_sibling_child_price').length) { 
            $('#'+index+'_sibling_child_price').attr('data-price', pkg_sibling_child_price).data('price', pkg_sibling_child_price).parents('.registration-count-item form-control').trigger('change'); 
            $('#'+index+'_sibling_child_price').trigger('change');
        }
        if($('#'+index+'_sibling_member_price').length) { 
            $('#'+index+'_sibling_member_price').attr('data-price', pkg_sibling_member_price).data('price', pkg_sibling_member_price).parents('.registration-count-item form-control').trigger('change'); 
            $('#'+index+'_sibling_member_price').trigger('change');
        }
        if($('#'+index+'_reduced_price').length) { 
            $('#'+index+'_reduced_price').attr('data-price', pkg_reduced_price).data('price', pkg_reduced_price).parents('.registration-count-item form-control').trigger('change'); 
            $('#'+index+'_reduced_price').trigger('change');
        }
    } else {
        $('#'+index+'_package_check').val('off');    

        if($('#'+index+'_regular_adult_price').length) { 
            $('#'+index+'_regular_adult_price').attr('data-price', regular_adult_price).data('price', regular_adult_price).parents('.registration-count-item form-control').trigger('change'); 
            $('#'+index+'_regular_adult_price').trigger('change');
        }
        if($('#'+index+'_regular_child_price').length) { 
            $('#'+index+'_regular_child_price').attr('data-price', regular_child_price).data('price', regular_child_price).parents('.registration-count-item form-control').trigger('change'); 
            $('#'+index+'_regular_child_price').trigger('change');
        }
        if($('#'+index+'_member_adult_price').length) { 
            $('#'+index+'_member_adult_price').attr('data-price', member_adult_price).data('price', member_adult_price).parents('.registration-count-item form-control').trigger('change'); 
            $('#'+index+'_member_adult_price').trigger('change');
        }
        if($('#'+index+'_member_child_price').length) { 
            $('#'+index+'_member_child_price').attr('data-price', member_child_price).data('price', member_child_price).parents('.registration-count-item form-control').trigger('change'); 
            $('#'+index+'_member_child_price').trigger('change');
        }
        if($('#'+index+'_sibling_child_price').length) { 
            $('#'+index+'_sibling_child_price').attr('data-price', sibling_child_price).data('price', sibling_child_price).parents('.registration-count-item form-control').trigger('change'); 
            $('#'+index+'_sibling_child_price').trigger('change');
        }
        if($('#'+index+'_sibling_member_price').length) { 
            $('#'+index+'_sibling_member_price').attr('data-price', sibling_member_price).data('price', sibling_member_price).parents('.registration-count-item form-control').trigger('change'); 
            $('#'+index+'_sibling_member_price').trigger('change');
        }
        if($('#'+index+'_reduced_price').length) { 
            $('#'+index+'_reduced_price').attr('data-price', reduced_price).data('price', reduced_price).parents('.registration-count-item form-control').trigger('change'); 
            $('#'+index+'_reduced_price').trigger('change');
        }
    }
}
</script>
 
        <div id="calender-wrapper">
            @if(isset($pg_links) && (isset($pg_links_used) && !$pg_links_used))
                <?php $pg_links_used = true; ?>
                <div class="ce ce-submenu container-fluid">
                    <div class="ce-submenu-title text-center">
                        Besuch planen
                    </div>
                    <div class="opener">
                        <a href="#" class="opener-close-link">
                            <span class="icon icon-up icon-red"></span>
                        </a>
                        <a href="#" class="opener-open-link">
                            <span class="icon icon-down icon-red"></span>
                        </a>
                    </div>
                    <ul class="list-inline">
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
                    </ul>
                </div>
            @endif

            <section id="calendar">
            @if(isset($showFliters))
                <div id="filter">
                    <div class="container-fluid mb-15">
                        <a href="#" class="open-filter">
                            <span id="icon_filter" class="icon icon-filter icon-black"></span>
                            Angebote filtern
                            <span class="icon icon-arrow icon-s icon-inline"></span>
                            <span class="filter-name">Alle</span>
                        </a>
                        <a href="#" class="open-filter-dateselector"><!--
                            --><span class="icon icon-calendar" style="position:relative;top:0px;width:40px;background-size:50px 50px;"></span>Datum suchen<!--
                            - -><span class="icon icon-arrow icon-s icon-inline ml-5 mr-5"></span>--><!--
                            - -><span class="filter-dateselector-name" data-date="{{date('d/m/Y')}}">{{ strftime('%d. %B')}}</span>-->
                        </a>
                    </div>
                    <div class="menu panel-collapse collapse">
                        <div>
                            <div class="triangle"></div>
                            <ul class="list-unstyled">
                                <li><span class="icon icon-inline icon-check"></span> <a href="#" data-filter="*">Alle</a></li>
                                  @if($tags)
                                     @foreach($tags as $tag)
                                        @if(in_array($tag->id, $tag_ids))
                                            <li onclick="setClickTarget('tag')"><span class="icon icon-inline"></span> <a id="filter_item_{{$tag['id']}}" href="#" 
                                                    data-filter=".filter-{{strtolower(str_replace(' ', '-', $tag['tag_en']))}}">{{$tag->tag_de}}</a></li>
                                        @endif
                                     @endforeach
                                  @endif                            
                            </ul>
                            <div class="text-center">
                                <a href="#" class="close-filter">
                                    <span class="icon icon-close icon-red"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif  

                <div id="swiper-cal" class="swiper-container control">
                    <div class="swiper-wrapper">
                    <?php $slideNo = 0; ?>    
                    @foreach($calendar as $k => $cal)
                       <?php ++$slideNo; ?>
                        <div class="swiper-slide">
                            <div class="date">
                                <span class="month">{{utf8_encode($cal['month'])}}</span>
                                <span class="year">{{$cal['year']}}</span>
                            </div>
                            <div class="date-selector-wrapper collapse">
                                <div class="pa-15">
                                    <table class="date-selector">
                                        <thead>
                                            <tr>
                                                <th>Mo</th>
                                                <th>Di</th>
                                                <th>Mi</th>
                                                <th>Do</th>
                                                <th>Fr</th>
                                                <th>Sa</th>
                                                <th>So</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                    <?php 
                                        $day_count = 0;
                                        $month_cal = [];
                                        $first_dow = date('N', strtotime(date($cal['year'].'-'.$cal['month_no'].'-01')));
                                        $mo_days = cal_days_in_month(CAL_GREGORIAN, $cal['month_no'], $cal['year']);
                                        echo '<tr>';
                                        for($d=1; $d<$first_dow; $d++) {
                                            echo '<td></td>';
                                            ++$day_count;
                                        }
                                        for($dom=1; $dom<=$mo_days; $dom++) {
                                            ++$day_count;               
                                            $data_date = date('m/d/Y', 
                                                strtotime($cal['year'].'-'.$cal['month_no'].'-'.str_pad($dom, 2, '0', STR_PAD_LEFT)));
                                            $cal_date = date('Y-m-d', strtotime($cal['year'].'-'.$cal['month_no'].'-'. str_pad($dom, 2, '0', STR_PAD_LEFT)));
                                            $is_active = true;
                                            if(isset($all_event_dates)) {
                                                if(!in_array($cal_date, $all_event_dates)) {
                                                    $is_active = false;
                                                }
                                            }
                                            $td = '<td><div data-date="'.$data_date.'" 
                                                            onclick="setCalEvents(this, \''.$cal_date.'\', '.$is_active.')" ';
                                            $td .= ' id="cal_filter_date_'.$cal_date.'" class="cal_filter_date_'.$cal_date;
                                            if(!$is_active) { $td .= ' date-selector-nodates'; }
                                            if($cal_date == date('Y-m-d')) { $td .= ' date-selector-currentdate'; }
                                            $td .= '">'.str_pad($dom, 2, '0', STR_PAD_LEFT) .'</div></td>';
                                            echo $td;

                                            if($day_count > 6 || $dom == $mo_days) { 
                                                $day_count = 0; 
                                                echo '</tr>';
                                            }
                                            // if($dom < $mo_days && $day_count > 0 && $day_count <= 7) { echo '<tr>'; }
                                        }
                                    ?>
                                        </tbody>
                                    </table>
                                    <div class="text-center">
                                        <a href="#" class="close-filter-dateselector">
                                            <span class="icon icon-close icon-red"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                    @if(count($calendar) > 1)
                        <div class="month-prev"><span class="icon icon-left icon-l" ></span></div>
                        <div class="month-next"><span class="icon icon-right icon-l" ></span></div>
                    @endif
                </div>

                <div class="swiper-container detail swiper-no-swiping">
                    <div class="swiper-wrapper">
                     
                     <?php $_MONTH = ''; ?>
                     <?php foreach($calendar as $mnth => $cal): ?>

                        <div class="swiper-slide slide_d_{{$slideNo}}">
                            <div class="calendar-no-data container">
                                <div class="alert alert-default text-center">
                                    <p>Zu Ihrer Auswahl finden in diesem Monat keine Veranstaltungen (mehr) statt.</p>
                                </div>
                            </div>
                       <?php foreach($cal['days'] as $day => $events): ?>
                            <div class="day">{{$day}}</div>
                            <div class="day-wrapper">
    
                             @foreach($events as $evts)    
                              @foreach($evts as $evt)    
                                <?php $MONTH = strftime("%B", strtotime($evt['start_date'])); 
                                $tag_classes = '';
                                if($evt['tags']) {
                                    foreach($evt['tags'] as $tag) {
                                        $tag_classes .= " filter-". strtolower(str_replace(' ', '-', $tag['tag_en']));
                                    }
                                }
                                ?>
                              <?php 
                                $reg_event_date = $evt['event_date'];
                                $list_evt_date = date('j.n.', strtotime($evt['event_date']));
                                if(isset($evt['event_dates'])) {
                                    foreach($evt['event_dates'] as $dt) {
                                        if($dt['index'] == $evt['index']) {
                                            $reg_event_date = $dt['full_date'];
                                            $list_evt_date = date('j.n.', strtotime($dt['full_date']));
                                            if($evt['as_series'] == 1 && (strtotime($dt['full_date']) < time())) {
                                                $list_evt_date = date('j.n.', time());
                                            }
                                        }
                                    }
                                }
                              ?>

                                <article class="event_no_{{$evt['index']}} {{$tag_classes}} event_art_{{$reg_event_date}}" data-date="{{ date('m/d/Y', strtotime($reg_event_date)) }}" id="art_{{$evt['index']}}">
                                    <div>
                                        <header class="pt-20 pb-20">
                                            <div class="time">
                                                <span class="icon icon-clock icon-white"></span>
                                                  {{substr($evt['start_time'], 0, 5)}} 
                                                  @if(strlen(trim($evt['end_time'])) > 4)
                                                      - {{substr($evt['end_time'], 0, 5)}}
                                                  @endif

                                                  / {{ $list_evt_date }}
                                            </div>
                                            <h2 onclick="copyEventLink({{$evt['index']}}, {{$evt['slideNo']}})" style="cursor:pointer;">{{$evt['title_de']}}</h2>
                                            <h3>{{$evt['subtitle_de']}}</h3>
                                            <div class="opener opener_{{$evt['index']}} opener-close">
                                                <a id="ea_{{$evt['index']}}" href="#" class="opener-close-link close-detail">
                                                    <span class="icon icon-up up_{{$evt['index']}}"></span>
                                                </a>
                                                <a href="#" class="opener-open-link open-detail toggle-icon-{{$evt['index']}}" 
                                                   idstr="{{$evt['id']}}_{{$evt['index']}}_{{$evt['slideNo']}}">
                                                    <span id="icon_down_{{$evt['index']}}" class="icon icon-down"
                                                     onclick="setEventId('{{$evt['id']}}', '{{$reg_event_date}}', '{{$evt['index']}}', '{{$evt['slideNo']}}', '{{ (isset($evt['event_image']) && strlen($evt['event_image']) > 4) ? 1 : 0 }}')"></span>
                                                </a>
                                            </div>
                                        </header>
                                        <div id="event_block_{{$evt['index']}}" class="detail-wrapper panel-collapse collapse container pb-30">
                                            <h3 class="detail-header">{{$evt['guide_name']}}</h3>
                                            {{html_entity_decode($evt['detail_de'])}}                                            
                                        </div>
                                    </div>
                                </article>
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

    <!-- Modal -->
    @if(!isset($excl_search))
        <div id="modals">
           @include('includes.search_modal')
        </div>
    @endif

<script type="text/javascript">
var confirm = false;
</script>
@if(isset($action) && ($action == 'confirmation' || $action == 'bestaetigung'))
   <script type="text/javascript"> confirm = true;    </script>
@endif

<script type="text/javascript">

function showConfirmation() {
    kunsthalle.showModal('confirm_event_registration');
}

var url = document.URL;
if(url.indexOf('/bestaetigung') > -1 || url.indexOf('/confirmation') > -1) {
    confirm = true;
}

$(function() {
    if(confirm) {
        console.log('showing confirmation');
        showConfirmation();
    }
});

var cnt = 0;
var wait = 8000;
function _wait() {
    if(cnt > 2) { return; }
    ++cnt;
    window.setTimeout(_wait, wait);
}

function checkForm(form) {
    console.log('checkForm()..');
    var id = form.id;
    id = id.substr(id.lastIndexOf('_')+1, id.length);
    registerForEvent(id, curEventIndex, event);
    if($('.icon-up-'+curEventIndex).length) {
        $('.icon-up-'+curEventIndex)[0].click();
    }
    // console.log(form);
    return false;
}

// function checkParticipants(indx) {
//     console.log("checkParticipants() called\nhas Cost? "+ hasCost);
//     var count = 0;
//     if(hasCost) {
//         if($('#'+indx+'_regular_adult_price').length && $('#'+indx+'_regular_adult_price').val() != '') { 
//             count += parseInt($('#'+indx+'_regular_adult_price').val()); 
//         }
//         if($('#'+indx+'_regular_child_price').length && $('#'+indx+'_regular_child_price').val() != '') { 
//             count += parseInt($('#'+indx+'_regular_child_price').val()); 
//         }
//         if($('#'+indx+'_member_adult_price').length && $('#'+indx+'_member_adult_price').val() != '') { 
//             count += parseInt($('#'+indx+'_member_adult_price').val()); 
//         }
//         if($('#'+indx+'_member_child_price').length && $('#'+indx+'_member_child_price').val() != '') { 
//             count += parseInt($('#'+indx+'_member_child_price').val()); 
//         }
//         if($('#'+indx+'_sibling_child_price').length && $('#'+indx+'_sibling_child_price').val() != '') { 
//             count += parseInt($('#'+indx+'_sibling_child_price').val()); 
//         }
//         if($('#'+indx+'_sibling_member_price').length && $('#'+indx+'_sibling_member_price').val() != '') { 
//             count += parseInt($('#'+indx+'_sibling_member_price').val()); 
//         }
//         if($('#'+indx+'_reduced_price').length && $('#'+indx+'_reduced_price').val() != '') { 
//             count += parseInt($('#'+indx+'_reduced_price').val()); 
//         }

//         console.log("Participant count: "+ count);
//         console.log("_member_adult_price:- "+indx + " : " + $('#'+indx+'_member_adult_price').val());
//         if(count == 0) {
//             if($(".participants_"+indx).length) {
//                 var scrollPos = $(".participants_"+indx).offset().top + 40;
//                 $('html, body').animate({
//                   scrollTop: scrollPos
//                 }, 700);
//             }
//             return false;
//         }
//     }

//     return true;
// }

</script>
<style>
.price-inp { border:none !important; outline:none !important; color:#000000; cursor: none !important; 
  color: transparent;
  text-shadow: 0 0 0 #000;
  &:focus { outline: none; }
}
</style>