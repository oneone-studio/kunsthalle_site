<?php
setlocale(LC_ALL, "de_DE", 'German', 'german');

      $cal = $calendar;
      // $calendar_json = json_decode($calendar_json, true);
      $cal_json = json_encode($calendar);
      // echo $cal_json; 
      $cal_arr = json_decode($cal_json, true);

      $day_index = 0;
      // exit;
// echo '<pre>'; print_r($cal); exit;
      $firstSlideHeight = 500;
      if(count($cal) > 0) {
         foreach($cal as $c) {
            $firstSlideHeight = $c['slideHeight'];
            break;
         }
      }
      // $firstSlideHeight = 4500;
      // echo $firstSlideHeight; exit;
?>
  <div id="main_panel_cal" class="row" style="height:<?php echo $firstSlideHeight;?>px; display:block; clear:both;">    
    <div class="col-md-12">
      <div id="main_content_inner_cal" style="width:100%; background:#b5d6dd; clear:both; margin-top:0px; position:relative; vertical-align:middle;">
          <div style="width:97.5%; max-width:99%; float:left; margin:10px 20px; min-height:90px; padding:5px; border-bottom:1px solid #fff; clear:both;">
              <div class="nav-btn-sel is-selected"><div>ALLES ZEIGEN</div></div>
              <div class="nav-btn"><div>KINDER UND FAMILIfEN</div></div>
              <div class="nav-btn"><div>JUGENDLICHE</div></div>
              <div class="nav-btn"><div>MITGLIEDER</div></div>
              <div class="nav-btn"><div>F&Uuml;HRUNGEN</div></div>
              <div class="nav-btn"><div>KURSE UND WORKSHOPS</div></div>
              <div class="nav-btn"><div>SEMINARE</div></div>
              <div class="nav-btn"><div>REISEN</div></div>
          </div>
          <div style="width:97.5%; float:left; margin:10px 20px; height:60px; padding:5px; border-bottom:1px solid #fff; clear:both;"><div 
          style="font-size:21px;font-weight:bold; width:100%; margin-top:1px; vertical-align:middle; font-family:Dekka; text-align: center;">VERANSTALTUNGEN</div>
          </div>
          <!--- ->
          <div id="prev" class="prev" style="width:18px; height:28px; position:absolute; left:15px; top:50%; z-index:9999; border:0px solid red;" onclick="this.blur()">&nbsp;</div>
          <div id="next" class="next" style="width:18px; height:28px; position:absolute; top:50%; right:15px; z-index:9999; border:none; cursor:pointer;" onclick="this.blur()">&nbsp;</div>
          <!---->

          <div id="cal_slider" class="home_slider" style="position:relative; display:block; height:<?php echo $firstSlideHeight;?>px; padding:0;">
      <?php 
      $pfs_block_ht = 0;
      $price_fields = [ 'regular_price_adult', 'regular_price_child', 'member_price_adult', 'member_price_child',
                        'reduced_price_adult', 'siblings_price_adult', 'siblings_price_child'
                      ];  
      $pfCount = 0;                  
      $slide_cnt = 0;
      $cnt = 0;
      $slide_height = 0;
      $form_height = 0;
      $form_height = 610;

      foreach($cal as $c):
          ++$slide_cnt;
          $text_align = 'text-align:center;';
          $btn_class = 'more-btn';          
          $fields_tmp = [];
          if(array_key_exists('days', $c)) {
            foreach($c['days'] as $day_str => $events) {
              foreach($events as $evArr) {
                foreach($evArr as $ev) {
                  foreach($price_fields as $pf) {
                     if(isset($ev[$pf]) && intval($ev[$pf]) > 0) {
                        if(!in_array($pf, $fields_tmp)) {
                           $fields_tmp[] = $pf;
                        }
                     }
                  }
                }  
              }
            }
          }
          // $form_height = $pfs_block_ht + 550;
          // print_r(array_keys($c));
          $slide_height = $c['slideHeight'];
          // echo $c['slideHeight']; exit;          
    ?>      
    <div id="fs_slide_<?php echo $slide_cnt;?>" class="fs_slide" style="font-size:12px; min-height:<?php echo $slide_height;?>px;">
        <div class="cal-block" style="width:99%; height:100%; margin:0 auto; text-align:center;">
            <div style="width:200px; height:34px; margin:0 auto; background:none; clear:both;">
                <div class="cal-month-title"><?php echo strtoupper($c['month']);?></div>
            </div>
            <div style="width:390px; height:0px; margin:0 auto; background:none; clear:both;">
                <div style="width:60px; float:right; margin-top:-20px; font-size:14px; height:31px;">
                   <div id="prev" onclick="showPrev()" style="width:17px; height:31px; float:left; cursor:pointer; background:url('../images/arrowleft.png') no-repeat; background-size:17px 31px;">&nbsp;</div>
                   <div id="next" onclick="showNext()" style="width:17px; margin-left:4px; height:31px; float:right; cursor:pointer; background:url('../images/arrowright.png') no-repeat; background-size:17px 31px;">&nbsp;</div>
                </div>
            </div>
            <div class="cal-year-title"><?php echo $c['year'];?></div>

  <?php 
     foreach($c['days'] as $day => $data): 
       ++$day_index;
  ?>                  
                   
    <div id="day_<?php echo $day_index;?>" class="event-day-title"><?php echo strftime($day);?></div>
    <?php       
      foreach($data['events'] as $evt): 
         ++$cnt; 
         $cluster = [];
         if(isset($evt['cluster'])) { $cluster = (array)$evt['cluster']; } 
         $eventCount = count($data['events']);
         $block_ht = intval($evt['cl_dates_height']) + 300;
    ?>
          <h2 id="event_block_<?php echo $evt['index'];?>" style="height:26px;"></h2>
          <div id="edb_<?php echo $evt['index'];?>" class="evt-day-block white-bdr accordion" style="width:98%; height:100px; position:relative; margin:-24px auto 0px auto; color:#000; clear:both; display:block; background:none; text-align:center; vertical-align:top;"><!-- div1s -->

             <div id="event_title_bar_<?php echo $evt['index'];?>" style="width:100%; margin:5px auto 10px auto; position:relative; top:-14px;" 
              onclick="showReg('<?php echo $evt['index'];?>', '<?php echo $slide_cnt;?>')">
               <h1 id="event_title_<?php echo $evt['index'];?>" class="cal-event-title"><?php echo strtoupper($evt['title_de']);?></h1>

               <h4 id="event_subtitle_<?php echo $evt['index'];?>" class="cal-event-subtitle"><?php echo strtoupper($evt['subtitle_de']);?></h4>

               <div id="et_<?php echo $evt['index'];?>" class="event-timing">
                <?php echo substr($evt['start_time'], 0, 5) .' - '. substr($evt['end_time'], 0, 5); ?>
               </div>
             </div>
             <div id="more_btn_blk_<?php echo $evt['index'];?>" class="title" style="width:100%; clear:both; margin-top:0px; position:relative; float:left; text-align:center;"><!-- div2s -->
               
               <div id="line_top" style="width:100%; background:url(/images/line_open.png) no-repeat; background-position: center; height:11px; clear:both; margin:2px auto;">&nbsp;</div>
               
               <!--
               <div id="more_btn_< ?php echo $evt['index'];?>" onclick="showReg('< ?php echo $evt['index'];?>', '< ?php echo $slide_cnt;?>')" 
                  class='more-btn'>&nbsp;</div><!---->
               
               <div class="hide" id="hide_<?php echo $evt['index'];?>" style="width:100%; min-height:510px; background:#fff;"><!-- div3s -->
                  <div style="width:100%; float:left; position:relative; padding:0;"><!-- div4s -->
                     
                     <div id="close_btn_<?php echo $evt['index'];?>" onclick="hideCurReg()" style="position:absolute; top:-54px; right:12px;background:url(../images/close.png) no-repeat; width:20px; height:20px; cursor:pointer; border:none;">&nbsp;</div>                                         

                     <!-- Left Menu -->
                     <div style="width:32.5%; float:left; margin-left:12px; margin-right:12px;">
                        <ul style="list-style:none; padding-left:0; text-align:left;">
                        <li style="line-height:8px; margin-top:12px;"><p style="font-size:12px;line-height:14px;">Diese Veranstaltung ist Teil des Begleitprogramms<br> zu der Ausstellung
                        </p></li>
                  <?php $is_clustered = false;
                        if(is_array($cluster) && count($cluster) > 0) { 
                          $cluster = $evt['cluster'];
                          $is_clustered = true;
                        ?>
                            <li style="line-height:12px; margin-top:12px;"><p style="font-size:12px; font-weight:bold;"><?php echo $cluster['title_de'];?></h4><p style="font-size:12px;"><?php 
                            echo $cluster['subtitle_de'];?></p></li>

                        <?php if(isset($evt['clustered_dates'])) : ?>
                                <li style="line-height:8px; margin-top:12px;"><p style="line-height:12px;"><span style="font-size:12px; font-weight:bold;">Weitere Termine in dieser Reihe:</span><br>
                                <ul style="list-style:none; padding-left:0;">
                                 <?php 
                                    for($i=0; $i<count($evt['clustered_dates']); $i++): 
                                         $date = $evt['clustered_dates'][$i];
                                      ?>
                                       <li style="height:13px; margin-top:5px; background:url(/images/arrow.png) no-repeat; padding-left:15px;"><a href="javascript:showEvent('<?php echo $date['index']; ?>', '<?php echo $date['slideNo'];?>', '<?php echo $slide_height;?>')" style="color:#111;font-size:12px;">{{$date['event_date']}}</a>
                                        <?php // if($i<count($evt['clustered_dates'])-1) { echo ','; } ?>
                              <?php endfor; ?>  
                                    </ul></p></li>
                        <?php endif; ?> 

                        <?php 
                        setlocale(LC_MONETARY,"de_DE");
                        if(is_numeric($cluster['cost_all_at_once_adult'])) : ?>
                                <li style="line-height:8px; margin-top:12px;"><p style="font-size:12px;line-height:14px;"><span style="font-size:12px; font-weight:bold;">Kosten:</span><br><?php 
                                echo str_replace('EUR', '', money_format("%i", $cluster['cost_all_at_once_adult'])); ?> &euro; bzw 
                        <?php endif;
                              if(is_numeric($cluster['cost_3_month_in_advance_adult'])) : 
                        ?> 
                                <?php 
                                echo ', '. str_replace('EUR', '', money_format("%i", $cluster['cost_3_month_in_advance_adult'])); ?> &euro; pro Seminarterim </p></li>
                        <?php endif; ?> 

                  <?php } ?>   

                          <li style="line-height:8px; margin-top:-2px;"><p style="font-size:12px;line-height:14px;"><span style="font-size:12px; font-weight:bold;">Leitung:</span><br>{{$evt['guide_name']}}</p></li>
                  
                  @if(!empty($evt['meeting_place']))        
                          <li style="line-height:8px; margin-top:-2px;"><p style="font-size:12px;line-height:14px;"><span 
                            style="font-size:12px; font-weight:bold;">Treffpunkt:</span><br>
                              {{$evt['meeting_place']}}<br>
                              {{html_entity_decode($evt['street'])}} @if(!empty($evt['building_number'])) {{ $evt['building_number']}} @endif<br>
                              {{$evt['postcode']}} {{$evt['city']}}<br>
                              <a href="{{$evt['google_map_url']}}" style="side-link" target="_blank">Google Maps</a><br>
                          </p></li>
                  @endif      
                          <!-- <li style="line-height:8px; margin-top:12px;"><h4>Anmeldung</h4><p>Diese Veranstaltung ist bereits ausgebucht!</p></li> -->
                        </ul>
                     </div>
                     
                     <!--  Main Content -->

                     <div style="width:64.6%; float:left; padding-left:14px; text-align:left;"><!-- div5s -->
                       <div class="event-detail" id="event_detail_<?php echo $evt['index'];?>" style="text-align:left; width:80%;">
                       <div style="width:100%; font-size:14px; clear:both;"><?php echo html_entity_decode($evt['detail_de']); ?></div>

                        <button style="background:#E65553; font-family:Dekka; font-weight:bold; border-radius:0; width:101px; height: 23px; cursor:pointer; border:none; color:#fff; font-size:11px;" onclick="showRegForm('<?php echo $evt['index'];?>', event, this)">ANMELDEN ></button>
                       </div>

                    <form id="reg_form_<?php echo $evt['index'];?>" method="post" action="" class="reg_form">  

                    <ul id="form_pane_<?php echo $evt['index'];?>" class="form-pane-ul" style="display:none;">
                      <li style="min-height:10px;">
                      </li>
                      <li style="border-top:0px solid #eee; background:#fff;">
                         <div style="width:0%; float:left;">&nbsp;</div>
                         <div style="width:100%; float:left; text-align:left; padding-left:0px;">
                           <p class="form-text"><strong>Anmeldung</strong><br>
                           Hiermit melde ich forgende Anzahl Personen verbindlich zu oben stehender Veranstaltung an:</p>
                           <table id="form_tbl_<?php echo $evt['index'];?>" class="price-tbl" style="display:none;">
                    
                    <?php if($is_clustered && strtolower($cluster['cluster_type']) == 'exhibition'): ?>  
                             <tr>
                                <td colspan="3" style="padding:5px 0px; position:relative;">
                                <label class="myCheckbox" style="width:24px; height:24px; margin-top:-2px; float:left;">
                                    <input type="checkbox" name="book_complete_package" style="margin-right:8px; float:left;">
                                    <span></span>
                                </label>
                                <div style="display:inline; margin-top:3px; font-family:Circular-Book; font-weight:normal;">Alle Veranstaltungen dieser Serie als Gesamtpaket buchen.</div>
                                </td>
                             </tr>
                    <?php endif; ?>
    
                    @foreach($price_fields as $pf)
                        @if(isset($evt[$pf]) && floatval($evt[$pf]) > 0)
                             <?php ++$pfCount; ?>
                             <tr>
                                <td style="width:40px; height:29px; max-height: 29px; padding:0px; border-bottom:0px solid #fff;">
                                  <input type="number" name="<?php echo $pf;?>" id="<?php echo $pf;?>_<?php echo $cnt;?>_inp" value="0" class="price-inp-sel" onclick="this.select();checkNum(this);updateTotal('<?php echo $pf;?>', this, '<?php echo $cnt;?>');" onkeyup="updateTotal('<?php echo $pf;?>', this, '<?php echo $cnt;?>')" style="margin-left:0px; margin-top:0px; height:30px; border-bottom:1px solid #fff;"></td>
                                  <td style="width:360px; float:left; padding-left:10px; padding-top:1px; line-height:29px; vertical-align: middle; height:29px; border-bottom:1px solid #eee;"><?php echo ucwords(str_replace(' price', '', str_replace('_', ' ', $pf))). ' Price';?> </td>
                                  <td style="width:332px; float:left; line-height:23px; vertical-align: middle; padding-top:0px; height:29px; border-bottom:1px solid #eee;"><div id="<?php echo $pf;?>_lbl_<?php echo $cnt;?>" style="width:60px; float:left; text-align:right; margin-right:3px;">0</div> &euro;
                                   <input type="hidden" name="<?php echo $pf;?>_<?php echo $cnt;?>" id="<?php echo $pf;?>_<?php echo $cnt;?>" 
                                     value="<?php echo $evt[$pf];?>"></td>
                             </tr> 
                        @endif     
                    @endforeach    
                           </table>  
                           <input type="hidden" name="total_price" id="total_price_<?php echo $cnt;?>" value="0" style="border:1px solid #f3f3f3;">

                           <table border="0" class="form-tbl">
                             <tr>
                               <td style="width:27%; min-width:180px; background:#eee; line-height: 24px; vertical-align: middle; 
                                    border-bottom:1px solid #fff; padding-left:8px;">Name *</td>
                               <td style="width:73%; line-height: 22px; border-bottom:1px solid #fff; vertical-align: middle; background:#eee; padding:0px; height:22px;"><input type="text" name="first_name" style="width:100%; border:none; background:#eee; height:24px;">
                               </td> 
                             </tr>
                             <tr>
                               <td style="width:27%; min-width:180px; background:#eee; line-height: 24px; vertical-align: middle; 
                                    border-bottom:1px solid #fff; padding-left:8px;">Nachname *</td>
                               <td style="width:73%; line-height: 22px; border-bottom:1px solid #fff; vertical-align: middle; background:#eee; padding:0px; height:22px;"><input type="text" name="first_name" style="width:100%; border:none; background:#eee; height:24px;">
                               </td>  
                             </tr>
                             <tr>
                                <td style="width:27%; min-width:180px; background:#eee; line-height: 14px; vertical-align: top; padding-top:5px; border-bottom:1px solid #fff; padding-left:8px;">Name und Alter der Kinder<br>(Angabe freiwiltdg) </td>
                                <td style="width:520px; padding:0px; border-bottom:1px solid #fff; background:#eee; height:70px;"><textarea name="children_detail"  
                                style="border:none; background:#eee; margin-top:2px; width:99.6%; height:99%;"></textarea> 
                                </td> 
                             </tr>
                             <tr>
                                <td style="width:27%; min-width:180px; background:#eee; line-height: 25px; vertical-align: middle; 
                                    border-bottom:1px solid #fff; padding-left:8px;">Emailadresse *</td>
                               <td style="width:73%; line-height: 22px; border-bottom:1px solid #fff; vertical-align: middle; background:#eee; padding:0px; height:22px;"><input type="text" name="email" style="width:100%; border:none; background:#eee; height:25px;">
                                </td>  
                             </tr>
                             <tr>
                                <td style="width:27%; min-width:180px; background:#fff; line-height: 25px; vertical-align: middle; 
                                    border-bottom:1px solid #fff; padding-left:0px; position:relative;">
                                <label class="myCheckbox" style="width:24px; height:24px; margin-top:2px; float:left;">
                                    <input type="checkbox" name="is_member" style="border:none; position:absolute; top:-3px; width:10px; margin-right:2px;">
                                    <span></span>
                                </label>
                                <div style="display:inline; margin-top:3px; margin-left:5px; font-family:Circular-Book; font-weight:normal;">Mitgtded im Kunstverein *</div></td>
                                <td style="padding:0px; height:24px;"><input type="text" name="first_name" style="width:98%; border:none; background:#fff; height:25px; margin-left:0px; padding-left:10px; padding-right:3px;" placeholder="Mitgtdedsnummer"></td>
                             </tr> 
                             <tr>
                                <td colspan="2" style="background:#fff; line-height: 18px; vertical-align: middle; 
                                    border-bottom:1px solid #fff; padding-left:0px; padding-top:10px;font-size:12px; padding-bottom:10px;" colspan="2"><span style="font-size:14px; font-weight:bold;">Zahlung</span><br>
                                  Wir bieten als Zahlungsm&ouml;gtdchtkeit Bankeinzug an.<br>Diese Einzugserm&auml;chtigung gilt nur f&uuml;r die hier aufgef&uuml;hten Veranstaltungen, danach ertdscht sie.
                               </td>
                             </tr>
                             <tr>
                                <td style="width:27%; min-width:180px; background:#eee; line-height: 25px; vertical-align: middle; 
                                    border-bottom:1px solid #fff; padding-left:8px;">IBAN *</td>
                               <td style="width:73%; line-height: 22px; border-bottom:1px solid #fff; vertical-align: middle; background:#eee; padding:0px; height:22px;"><input type="text" name="iban" style="width:100%; border:none; background:#eee; height:24px;">
                               </td>  
                             </tr> 
                             <tr>
                                <td style="width:27%; min-width:180px; background:#eee; line-height: 25px; vertical-align: middle; 
                                    border-bottom:1px solid #fff; padding-left:8px;">Kontoinhaber *</td>
                               <td style="line-height: 22px; border-bottom:1px solid #fff; vertical-align: middle; background:#eee; padding:0px; height:22px;"><input type="text" name="first_name" style="width:100%; border:none; background:#eee; height:24px;">
                               </td>  
                             </tr> 
                             <tr>
                                <td style="width:27%; min-width:180px; background:#eee; line-height: 25px; vertical-align: middle; 
                                    border-bottom:1px solid #fff; padding-left:8px;">Name der Bank *</td>
                               <td style="line-height: 22px; border-bottom:1px solid #fff; vertical-align: middle; background:#eee; padding:0px; height:22px;"><input type="text" name="first_name" style="width:100%; border:none; background:#eee; height:24px;">
                               </td>  
                             </tr> 
                             <tr>
                                <td colspan="2" style="background:#fff; line-height: 25px; vertical-align: middle; 
                                    border-bottom:1px solid #fff; padding-left:8px;" colspan="2"><p>* Pftdchtfelder</p>
                               </td>
                             </tr>
                             <tr>
                                <td colspan="2" style="background:#fff; position:relative; padding-left:0;">
                                  <label class="myCheckbox" style="width:24px; height:24px; margin-top:0px; float:left;">
                                    <input type="checkbox" name="terms_1" style="float:left; position:absolute; top:1px; left:0; margin-left:0;">
                                    <span></span>
                                  </label>
                                  <span style="display:inline; position:relative; top:3px; margin-left:5px; font-family:Circular-Book; font-weight:normal;">Mit den <u>Anmelde- und Teilnahmebedingugen</u> erkl&auml;re ich mich einverstanden.</span></td>
                             </tr> 
                             <tr>
                                <td colspan="2" style="background:#fff; position:relative; padding-left:0;">
                                  <label class="myCheckbox" style="width:24px; height:24px; margin-top:0px; float:left;">
                                    <input type="checkbox" name="terms_2" style="float:left; position:absolute; top:3px; left:0; margin-left:0;">
                                    <span></span>
                                  </label>
                                  <span style="display:inline; position:relative; top:3px; margin-left:5px; font-family:Circular-Book; font-weight:normal;">Ich will gerne den Newsletter der Kunsthalle Bremen empfangen.</span>
                                </td>  
                             </tr> 
                             <tr>
                               <td colspan="2" style="padding:20px 0; text-align:center;">
                                  <button type="submit" class="btn submit-btn">JETZT ANMELDEN</button>
                               </td>
                             </tr>
                             <tr><td colspan=2 style="height:50px; background:#fff;">&nbsp;</td></tr>
                          </table>
                        </div>
                        </li>
                      </ul>
                          <input type="hidden" id="slide_index_<?php echo $evt['index'];?>" value="<?php echo (intval($slide_cnt)-1);?>">
                      </form>
                    </div></div></div>
                </div>                                       
          </div>
                        <?php endforeach; ?>  
                  <?php endforeach; ?>      
                    </div>
                </div>    
      <?php endforeach; ?>
          </div>
      </div>
    </div></div>
  </div>
<script>

var calendar_json = '';
var calendar = [];
var form_height = 0;
var show_reg = true;
var curEventId = 0;
var main_block_height = 0;
var main_panel_ht_orig = 0;
var curSlideHeight = 0;
var curEventCount = 0;

$(function() {
  var exhibition_id = '<?php echo $exhibition->id;?>';
  // alert(calendar.length);
  $('.accordion .hide').hide();
  $('.reg_form').submit(function(event) {
     event.preventDefault();

     if(!isNaN(curEventId) && curEventId > 0) {
       event.preventDefault();
       var formData = $('#reg_form_'+curEventId).serialize();
       $.ajax({
          type: 'POST',
          url: '/register-for-event',
          data: formData,
          dataType: 'json',
          success:function(data) { 
                console.log('Success..'+ "\n\n");
            },
          error:  function(jqXHR, textStatus, errorThrown) {
                  console.log('Registration failed.. ');
                }
       }); 
     }
  });

  // calendar = '< ?php print($cal_arr);?>';
  /**/
   $.ajax({
      type: 'GET',
      url: '/get-exb-calendar-json',
      data: { 'exhibition_id' : exhibition_id },
      dataType: 'json',
      success:function(data) { 
            console.log('Calendar json response..');
            console.log(data);
            calendar_json = data.calendar_json;
            calendar = $.parseJSON(calendar_json);
        },
      error:  function(jqXHR, textStatus, errorThrown) {
              console.log('Calendar failed.. ');
            }
   }); 
   /**/

   form_height = '<?php echo $form_height;?>';
   currSlideHeight = parseInt('<?php echo $firstSlideHeight;?>');
});

var openEventBlockHeight = 0;
var isEventBlockOpen = false;
var isFormOpen = false;
var isClosing = false;
var animated = false;

function showReg(id, slideNo) {
  // alert(id); return;
  // curSlideHeight = slideHeight;
  if($('#edb_'+id).length) {
    $('#edb_'+id).removeClass('white-bdr').addClass('no-white-bdr');
  }
  isClosing = false;
  if(slideNo == '0') { slideNo = 1; }
  if(id != curEventId) {
    hideCurReg(id);
  } else {
    show_reg = true;
  }
  var main_panel_cal_ht = $('#main_panel_cal').css('height').replace('px', '');
  // var slider_ht = $('#cal_slider').css('height').replace('px', '');
  // var newSlideHeight = $('#fs_slide_'+slideNo).css('height').replace('px', '');
  /*
    ar = calendar[slideIndex].days;
    s = '';
    var newSlideHeight = 100;
    for(var v in ar) {
      for(var k in ar[v]) {
        events = ar[v][k];
        newSlideHeight += ((events.length) * 300);
      }
    }/**/
  newSlideHeight = 2000;  
  var panel_ht = 100;
  if(show_reg) {
      isEventBlockOpen = true;
      if(curEventId > 0 && $('#form_tbl_'+curEventId).length) {
         $('#form_tbl_'+curEventId).css('display', 'none'); //delay(200).animate({ display: 'none'});
      }
      $('#form_tbl_'+id).css('display', 'inline');
      $('#event_title_'+id).addClass('title-xl');
      $('#event_subtitle_'+id).addClass('title-l');
      $('#hide_'+id).fadeIn(200);
      $('#hide_'+id).show();
      $('#hide_'+id).css('background', '#fff');
      $('#edb_'+id).css('background', '#fff');
      $('#et_'+id).removeClass('event-timing').addClass('event-timing-black');
      $('#more_btn_'+id).hide();
      // ar = calendar[slideNo-1].days;
      /*
      panel_ht = $('#hide_'+id).css('height').replace('px', '');
      panel_ht = parseInt(panel_ht);

      var edbHeight = $('#edb_'+id).css('height').replace('px', '');
      var detailHeight = $('#more_btn_blk_'+id).css('height').replace('px', '');      
      openEventBlockHeight = (parseInt(edbHeight)+parseInt(detailHeight));
      var newMaxHeight = (newSlideHeight + openEventBlockHeight) + 'px';
      curSlideHeight = (parseInt(curSlideHeight) + parseInt(openEventBlockHeight));
      // $('#main_panel_cal').delay(200).animate({ height: newMaxHeight });
      // $('#cal_slider').css('height', newMaxHeight);
      /**/
      if($('#event_block_'+id).length) {
        // var detail = $('#event_detail_'+id).html();
        // detail = detail.replace('width="', ' style="width:');
        // detail = detail.replace('" height="', '; height:');
        // // alert(detail);
        // $('#event_detail_'+id).html(detail);

        console.log('scrolling to selected event..');
        var scrollPos = $("#event_block_"+id).offset().top - 18;
        $('html, body').animate({
          scrollTop: scrollPos
        }, 700);
        console.log('scrolled.');
      }
      if($('#slide_index_'+id).length && !isNaN($('#slide_index_'+id).val())) {
         _slideIndex = parseInt($('#slide_index_'+id).val());
         if(slideIndex != _slideIndex) {
            animated = false;
         }
         slideIndex = _slideIndex;
      }

      curEventId = id;
      animated = false;
      adjustHeights();
      show_reg = false;
  }

  curEventId = id;
}

function showRegForm(id, event, btn) {
   isFormOpen = true;
   event.preventDefault();
   btn.blur();
   adjustHeights();

   // var main_panel_cal = $('#main_panel_cal').css('height').replace('px', '');
   // $('#main_panel_cal').css('height', (parseInt(main_panel_cal)+parseInt(form_height))+'px');
   // var cal_slider_ht = $('#cal_slider').css('height').replace('px', '');
   // $('#cal_slider').css('height', (parseInt(curSlideHeight)+parseInt(form_height))+'px');
   $('#form_pane_'+id).delay(720).css('display', 'inline');
}

function hideCurReg() {
    $('#edb_'+curEventId).css('background', 'none');
    $('#hide_'+curEventId).hide();
    $('#form_pane_'+curEventId).hide();
    show_reg = true;
    isEventBlockOpen = false;
    isFormOpen = false;
    isClosing = true;
    /*
    var main_panel_cal_ht = $('#main_panel_cal').css('height').replace('px', '');
    var slider_ht = $('#cal_slider').css('height').replace('px', '');
    var panel_ht = 100;
    cal_slider_ht = $('#cal_slider').css('height').replace('px', '');
    console.log("hideCurReg()..\ncal_slider_ht: " + cal_slider_ht + "\ncurSlideHeight: "+ curSlideHeight+ "\main_panel_cal_ht: "+ main_panel_cal_ht);    

    defaultSliderHeight = (parseInt(cal_slider_ht) - (parseInt(openEventBlockHeight)));
    main_panel_cal_ht = (parseInt(main_panel_cal_ht) - parseInt(curSlideHeight)) + parseInt(defaultSliderHeight);
    if(isFormOpen) {      
      defaultSliderHeight = parseInt(defaultSliderHeight) - parseInt(form_height);
      main_panel_cal_ht = parseInt(main_panel_cal_ht) - parseInt(form_height);
      console.log("Deducing the form height: " + form_height + " => "+ defaultSliderHeight);
    }
    console.log("\ndefaultSliderHeight : " + defaultSliderHeight);    
    /**/
    // console.log("main_panel_cal_ht: " + main_panel_cal_ht);    
    if(!isNaN(curEventId) && curEventId > 0) {
      $('#edb_'+curEventId).removeClass('no-white-bdr').addClass('white-bdr');
      $('#event_title_'+curEventId).removeClass('title-xl');
      $('#event_subtitle_'+curEventId).removeClass('title-l');

      // cal_slider_ht = defaultSliderHeight + 'px'; 
      // $('#main_panel_cal').css('height', (parseInt(cal_slider_ht)) +'px');
      // $('#cal_slider').css('height', cal_slider_ht);

      $('#hide_'+curEventId).fadeOut(200);
      $('#hide_'+curEventId).hide();
      $('#edb_'+curEventId).css('background', 'none');
      // $('#et_'+curEventId).css('color', '#fff');
      $('#et_'+curEventId).removeClass('event-timing-black').addClass('event-timing');
      $('#more_btn_'+curEventId).show();      
    }
    animated = false;
    adjustHeights();
}

var curBaseHeight = 0;
var priceFieldCount = 0;
var curDayIndex = 0;
var detail_block_ht = 0;

function adjustHeights() {
    console.log("\nSlide index: " + slideIndex + "\n");
    var baseHeight = 0;
    detail_block_ht = 0;
    if(!isNaN(calendar.slideHeights[slideIndex])) {
       baseHeight = parseInt(calendar.slideHeights[slideIndex]);
    }
    var block_ht = 360;
    var day_index = 0;
    if(curEventId.length > 1) {
      for(var j in calendar) {
        ar = calendar[j].days;
        for(var v in ar) {
          for(var k in ar[v]) {
            ++day_index;
            events = ar[v][k];
            for(i=0; i<events.length; i++) {
              evt = events[i];
              if(evt.index == curEventId) {
                curDayIndex = day_index;
                // detail_block_ht = parseInt(evt.cl_dates_height) + 100;
                // alert(curDayIndex);
                // if($('#day_'+day_index).length) {
                //   $('#day_'+day_index).removeClass('white-bdr').addClass('no-white-bdr');
                // }
                priceFieldCount = evt.priceFieldCount;
                break;
              }
            }
          }
        }
      }
    }
    // baseHeight += detail_block_ht;
    var info = '';
    info += "adjustHeight()..\nbaseHeight : " + baseHeight;
    if(curEventId > 0) {
      $('#event_title_'+curEventId).removeClass('title-xl');
      $('#event_subtitle_'+curEventId).removeClass('title-l');
      var edbHeight = $('#edb_'+curEventId).css('height').replace('px', '');
      var detailHeight_ = $('#more_btn_blk_'+curEventId).css('height').replace('px', '');      
      var detailHeight = $('#event_detail_'+curEventId).css('height').replace('px', '');      
      openEventBlockHeight = (parseInt(edbHeight)+parseInt(detailHeight));
    }    
    if(isEventBlockOpen) {
        baseHeight = parseInt(baseHeight) + parseInt(openEventBlockHeight);
        detail_block_ht += parseInt(openEventBlockHeight);
        info += "\n+ event block height => " + openEventBlockHeight + ' => ' + baseHeight;
    }
    if(isFormOpen) {
        baseHeight = parseInt(baseHeight) + parseInt(form_height) + (priceFieldCount * 25);
        detail_block_ht += 800;  // (parseInt(form_height) + (priceFieldCount * 25)) - 130;
        info += "\n+ form height => " + form_height + ' => ' + baseHeight;
        animated = false;
    }
    // console.log('INFO: ' + info);
    var calendarHeight = baseHeight + 'px';
    var mainPanelHeight = (parseInt(baseHeight) + 0) + 'px';
    $('#hide_'+curEventId).css('height', detail_block_ht+'px');
    $('#main_panel_cal').css('height', mainPanelHeight);
    animated = true;
    $('#cal_slider').css('height', calendarHeight); //.delay(200).animate({ height: baseHeight }); //.css('height', baseHeight);
    curBaseHeight = baseHeight;
}
$(function() {
    var main_panel_cal_ht = $('#main_panel_cal').css('height').replace('px', '');
    var slideHeight = '<?php echo $slide_height;?>';
});
</script>
<script src="../../../js/jquery-1.11.0.min.js"></script>
<script src="../../../js/jquery.cycle.lite.js"></script>
<script>
$jq1 = jQuery.noConflict();
var currSlide = 0;
var slideIndex = 0;
var origCurrSlide = 0;
var slideCount = 0;

$jq1(function() {

  if($jq1("#cal_slider").length) {
    $jq1("#cal_slider").cycle({
        timeout: 0,
        fx: 'scrollHorz',
        // prev: '#prev',
        // next: '#next',
        speed: 900,
        after: function(curr, next, opts) {
          // alert(opts.currSlide);
          slideCount = opts.slideCount;
          // slideIndex = opts.currSlide;
          // currSlide = opts.currSlide + 1;
          // if(currSlide == slideCount) {
             // slideIndex = 0;
             // currSlide = 1;
          // }
          origCurrSlide = opts.currSlide;
        }
    });
  }
  $jq1('.accordion .hide').hide();
});

function changeCalSlide(indx) {
  if(!isNaN(indx)) {
    $jq1('#cal_slider').cycle(parseInt(indx));
  }
}
// $('#cal_slider').on('cycle-update-view', function (e, opts, slideOptionsHash, currSlideEl) {
//       slideCount = opts.slideCount;
//       slideIndex = opts.currSlide;
//       currSlide = opts.currSlide + 1;
//       if(currSlide == slideCount) {
//          slide_index = 0;
//          currSlide = 1;
//       }
// });

var cnt = 0;

function showNext() {
    console.log('Next..');
    if(slideIndex < slideCount-1) {
        ++slideIndex;
    } else {
      slideIndex = 0;
    }
    $jq1("#cal_slider").cycle('next');
    animated = false;
    // alert('index: '+ slideIndex + "\ncurrSlide : " + currSlide);
    adjustHeights();

/*
    var slideNo = currSlide;
    var s = '';
    var slideNum = currSlide;
    if(slideNum == slideCount) {
       slideNum = 1;
    }
    ar = calendar[slideIndex].days;
    s = '';
    var newSlideHeight = 100;
    for(var v in ar) {
      for(var k in ar[v]) {
        events = ar[v][k];
        newSlideHeight += (events.length) * 300;
      }
    }
// alert('cur slide: '+ slideNum + "\nslideHeight: "+ newSlideHeight);
    var main_panel_cal_ht = $('#main_panel_cal').css('height').replace('px', '');
    var slider_ht = $('#cal_slider').css('height').replace('px', '');
    // var newSlideHeight = $('#fs_slide_'+currSlide).css('height').replace('px', '');
    if(main_block_height == 0) {
       main_block_height = main_panel_cal_ht;
    }
    var panel_ht = $('.hide').css('height').replace('px', '');
    var slider_ht = $('#cal_slider').css('height').replace('px', '');
    if(currSlide < (slideCount - 1)) {
       slideNo = (currSlide );
    } else {
       slideNo = 1;
    }
    var slide_height = $('#fs_slide_'+(slideNo)).css('height').replace('px', '');
    var newSliderHt = (parseInt(slide_height)) + 'px';    
    $('#cal_slider').css('height', newSliderHt);
    main_block_height = 110;
    main_panel_cal_new_ht = ((parseInt(main_block_height) + (parseInt(newSlideHeight)))) + 'px';
    $('#main_panel_cal').delay(200).animate({ height: main_panel_cal_new_ht });
    cal_slider_new_ht = ((parseInt(newSlideHeight) ) + 120) + 'px';
    $('#cal_slider').css('height', cal_slider_new_ht);
    /**/
}

function showPrev() {
    console.log('Prev..');
    if(slideIndex > 0) {
        --slideIndex;
    } else {
      slideIndex = slideCount-1;
    }
    $jq1("#cal_slider").cycle('prev');
    animated = false;
    adjustHeights();
/*
    var main_panel_cal_ht = $('#main_panel_cal').css('height').replace('px', '');
    var slider_ht = $('#cal_slider').css('height').replace('px', '');
    var newSlideHeight = $('#fs_slide_'+currSlide).css('height').replace('px', '');    
    if(main_block_height == 0) {
       main_block_height = main_panel_cal_ht;
    }
    var panel_ht = $('.hide').css('height').replace('px', '');
    var slider_ht = $('#cal_slider').css('height').replace('px', '');
    var slideNo = currSlide;
    if(currSlide < (slideCount - 1)) {
       slideNo = (currSlide + 1);
    } else {
       slideNo = 1;
    }
    var slide_height = $('#fs_slide_'+(slideNo)).css('height').replace('px', '');
    var newSliderHt = (parseInt(slide_height)) + 'px';    
    $('#cal_slider').css('height', newSliderHt);
    main_panel_cal_new_ht = ((parseInt(main_block_height) + (parseInt(newSlideHeight) - parseInt(form_height)))) + 'px';
    $('#main_panel_cal').delay(200).animate({ height: main_panel_cal_new_ht });
    cal_slider_new_ht = ((parseInt(newSlideHeight) + parseInt(slider_ht)) + 120) + 'px';
    $('#cal_slider').css('height', cal_slider_new_ht);
    /**/
}

function showEvent(id, slideNo, slideHeight) {
   // if(!isNaN(slideNo)) {
   //   $jq1("#cal_slider").cycle(parseInt(slideNo));
   // }
   var s = '';
   var n = 0;
   var slideNum = -1;
   for(var v in calendar) {
     slideNum++;
     s += slideNum + "\n";
     for(var k in calendar[v].days) {
       events = calendar[v].days[k].events;
       for(i=0; i<events.length; i++) {
         ++n;
         if(events[i].index == id) {
            slideNo = slideNum;
            break;
         }
       }
     }
   }

   if($('#event_block_'+id).length) {
     changeCalSlide(slideNo);
        var scrollPos = $("#event_block_"+id).offset().top - 18;
        $('html, body').animate({
          scrollTop: scrollPos
    }, 700);
   }

   showReg(id, slideNo);
}

function updateTotal(fld_name, fld, cnt) {
   var total = 0;
   var fld_total = 0;
   if(!isNaN(cnt)) {
     var price_flds = [ 'regular_price_adult', 'regular_price_child', 'member_price_adult', 'member_price_child',
                        'reduced_price_adult', 'siblings_price_adult', 'siblings_price_child' ];
     for(i=0; i<price_flds.length; i++) {
       if($('#'+price_flds[i]+'_'+cnt +'_inp').length && !isNaN($('#'+price_flds[i]+'_'+cnt +'_inp').val())) {
           fld_total = (parseInt($('#'+price_flds[i]+'_'+cnt +'_inp').val()) * parseInt($('#'+price_flds[i]+'_'+cnt).val()));
           total += fld_total;
           $('#'+price_flds[i]+'_lbl_'+cnt).html(fld_total);
       }
     }
     console.log('Total: ' + total);
     $("#total_price_"+cnt).val(total);
   }
}

function checkNum(fld) {
  if(isNaN(fld.value) || (!isNaN(fld.value) && parseInt(fld.value) < 0)) {
     fld.value = 0;
  } 
}
</script>
<style>
input {
  font-size:12px;
}
input[type="text"] {
    background:#f6f6f6 !important;
}
textarea {
    background:#f6f6f6 !important;
}

/*
button.accordion {
    background-color: #eee;
    color: #444;
    cursor: pointer;
    padding: 18px;
    width: 100%;
    border: none;
    text-align: left;
    outline: none;
    font-size: 15px;
    transition: 0.4s;
}

button.accordion.active, button.accordion:hover {
    background-color: #ddd;
}
/*
button.accordion.active:after {
    content: "\2796"; /* Unicode character for "minus" sign (-) * /
}
*/
/*
div.panel {
    padding: 0 18px;
    width:100%;
    height:100%;  
    margin:0px auto;
    background-color: white;
    max-height: 0;
    overflow: hidden;
    border-radius:1px;
    transition: 0.6s ease-in-out;
    opacity: 0;
}

div.panel.show {
    opacity: 1;
    max-height: 800px;  
}

/**/
.hide {
  background: #ffffff;
}
.title-l {
   font-size:16px;
}
.title-xl {
   font-size:22px;
}
.price-inp {
   width:39px; height:29px; text-align:center; border:none; background:#fff; text-align:center;
}
.price-inp-sel {
   width:39px; height:29px; text-align:center; border:none; background:#eee; text-align:center; padding:0;
}

/*input[type=checkbox] {
  display: none;
}
* /.checkbox label:before {
  border-radius: 3px;
}

input[type=checkbox]:checked + label:before {
  content: "\2713";
  text-shadow: 1px 1px 1px rgba(0, 0, 0, .2);
  font-size: 15px;
  color: #f3f3f3;
  text-align: center;
  line-height: 15px;
}
/**/

.myCheckbox input {
    position: relative;
    z-index: -9999;
}

.myCheckbox span {
    width: 20px;
    height: 20px;
    display: block;
    background: url("http://kunsthalle-site.dev/images/tick_off.png");
}

.myCheckbox input:checked + span {
    background: url("http://kunsthalle-site.dev/images/tick_on.png");
}
</style>

<script>
// var acc = document.getElementsByClassName("accordion");
var i;
// window.onload = function() {
  // showReg(1);
// }
/*
for (i = 0; i < acc.length; i++) {
    acc[i].onclick = function(){
        this.classList.toggle("active");
        this.nextElementSibling.classList.toggle("show");
  }
}

/**/
var showEDB = false;
var curEDB = 0;
/*
function toggleEDB_BG(id) {
  curEDB = id;
  if(showEDB) {
    // $('#more_btn_'+id).css('display', 'none');
    $('#edb_'+id).css('background', '#fff');
    $('#et_'+id).css('color', '#222');
    showEDB = false;
  } else {
    // $('#more_btn_'+id).css('display', 'inline');
    $('#edb_'+id).css('background', 'none');
    $('#et_'+id).css('color', '#fff');
    showEDB = true;
  }
}
/**/
function closeEDB(id) {
/*
var acc = document.getElementsByClassName("accordion");
for (i = 0; i < acc.length; i++) {
    acc[i].onclick = function(){
        // this.classList.toggle("active");
        this.nextElementSibling.classList.toggle("hide");
  }
}
*/

    // $('#more_btn_'+id).css('display', 'inline');
    // $('#edb_'+id).css('background', 'none');
    // $('#et_'+id).css('color', '#fff');
}

</script>