<?php
      $cal = $calendar;
      // echo '<pre>'; print_r($cal); exit; 
?>

  <div id="main_panel" class="row" style="border-bottom:0px solid red; height:100%;">    
    <div class="col-md-12">
      <div id="main_content_inner" style="width:100%; background:#C2CA88; clear:both; margin-top:8px; position:relative; vertical-align:middle;">
          <div style="width:99%; float:left; margin:10px 20px; height:90px; padding:5px; border-bottom:1px solid #fff; clear:both;">
              <div class="nav-btn-sel is-selected">ALLES ZEIGEN</div>
              <div class="nav-btn">KINDER UND FAMILIEN</div>
              <div class="nav-btn">JUGENDLICHE</div>
              <div class="nav-btn">MITGLIEDER</div>
              <div class="nav-btn">F&Uuml;HRUNGEN</div>
              <div class="nav-btn">KURSE UND WORKSHOPS</div>
              <div class="nav-btn">SEMINARE</div>
              <div class="nav-btn">REISEN</div>
          </div>

          <!--- ->
          <div id="prev" class="prev" style="width:18px; height:28px; position:absolute; left:15px; top:50%; z-index:9999; border:0px solid red;" onclick="this.blur()">&nbsp;</div>
          <div id="next" class="next" style="width:18px; height:28px; position:absolute; top:50%; right:15px; z-index:9999; border:none; cursor:pointer;" onclick="this.blur()">&nbsp;</div>
          <!---->

          <div id="cal_slider" class="home_slider" style="position:relative; height:100%; min-height:668px; max-height:auto; padding:0;">
          <?php
            $cnt = 0;
            foreach($cal as $c):
                ++$cnt;
                $text_align = 'text-align:center;';
                // $float = '';
                $btn_class = 'more-btn';
          ?>      
                <div class="fs_slide" style="font-size:12px;">

                    <div class="cal-block" style="width:99%; height:100%; margin:0 auto; text-align:center;">

                        <div style="width:300px; height:25px; margin:0 auto; background:none; clear:both;">
                            <div style="width:90px; background:none; font-size:28px; margin:15px auto 5px auto; font-weight:bold;"><?php echo $c['month'];?></div>
                        </div>
                        <div style="width:240px; height:0px; margin:0 auto; background:none; clear:both;">
                            <div style="width:40px; float:right; margin-top:-15px; font-size:14px;">
                               <div id="prev" onclick="showPrev()" style="width:15px; float:left; cursor:pointer; background:url('../images/arrowleft.png') no-repeat; background-size:12px 18px;">&nbsp;</div>
                               <div id="next" onclick="showNext()" style="width:15px; float:right; cursor:pointer; background:url('../images/arrowright.png') no-repeat; background-size:12px 18px;">&nbsp;</div>
                            </div>
                        </div>
                        <div style="width:100%; height:40px; margin:15px auto; clear:both; text-align:center; font-size:16px; font-weight:bold;"><?php echo $c['year'];?></div>
                   
                  <?php foreach($c['days'] as $day => $data): ?>                          
                        
                          <div class="evt-day-block" style="width:98%; min-height:40px; padding:10px; margin:35px auto 10px auto; border-bottom:0px solid #fff; color:#fff; clear:both; background:none; text-align:center; font-size:18px; font-weight:bold;"><?php echo $day;?></div>
                          <?php 
                            $cnt = 0;
                            foreach($data['events'] as $evt): 
                               ++$cnt; 
                               $cluster = [];
                               if(isset($evt['event_cluster'])) { 
                                   $cluster = $evt['event_cluster']; 
                                   $clustered_dates = [];
                                   if(isset($evt['clustered_dates'])) {
                                    // echo 'OK..zz'; exit;
                                       // foreach($cluster->clustered_dates as $cle) {
                                          $clustered_dates = $evt['clustered_dates'];
                                       // }
                                   }
                               }
                               // echo "Dates: ". implode(', ', $clustered_dates); exit;
                          ?>

                              <div id="edb_<?php echo $cnt;?>" class="evt-day-block accordion" style="width:98%; min-height:100px; position:relative; margin:0px auto 0px auto; border-bottom:1px solid #fff; color:#000; clear:both; background:none; text-align:center;">
                                 <div style="width:100%; margin:5px auto 0 auto; position:relative; top:15px;">
                                   
                                   <h4 id="event_title" style="height:14px;"><?php echo $evt['title_en'];?></h4>

                                   <h7 id="event_subtitle"><?php echo $evt['subtitle_en'];?></h7>
                                   
                                   <div id="et_<?php echo $cnt;?>" style="width:100px; height:20px; padding-top:2px; position:absolute; top:0px; left:15px; font-size:11px; text-align:left; background:url('../images/clock_white.png') no-repeat; background-size:18px 18px; background-position:-1px -1px; padding-left:23px; color:#fff;">
                                    <?php echo $evt['start_time'] .' - '. $evt['end_time']; ?>
                                   </div>
                                 </div>
                                 <div id="more_btn_blk_<?php echo $cnt;?>" class="title" style="width:100%; clear:both; margin-top:20px; float:left; text-align:center;"><div id="more_btn_<?php echo $cnt;?>"  onclick="showReg('<?php echo $cnt;?>')" class='more-btn' style="background:url('../images/more_btn.png') no-repeat; background-size:64px 22px; border:none; width:64px; height:22px; cursor:pointer; margin:auto;">&nbsp;</div>
                                   <div class="hide" id="hide_<?php echo $cnt;?>" style="width:100%; height:100%; background:#fff;">
                                      <div style="width:100%; float:left; position:relative;">
                                         
                                         <div id="close_btn_<?php echo $cnt;?>" onclick="hideCurReg()" style="position:absolute; top:2px; right:10px; background:url(../images/close.png) no-repeat; width:20px; height:20px; cursor:pointer; border:none;">&nbsp;</div>
                                         
                                         <div style="width:25%; float:left; margin-left:15px;">
                                            <ul style="list-style:none; padding-left:0; text-align:left;">
                                            
                                      <?php if(count($cluster)) { ?>
                                                <li style="line-height:8px; margin-top:12px;"><h4><?php echo $cluster->title_en;?></h4><p><?php 
                                                echo $cluster->subtitle_en;?></p></li>

                                                <?php if(count($clustered_dates)) : ?>
                                                    <li style="line-height:8px; margin-top:12px;"><h4>Weitere Termine in dieser Reihe:</h4><p><?php 
                                                    echo implode(', ', $clustered_dates); ?></p></li>
                                                <?php endif; ?> 

                                            <?php 
                                            setlocale(LC_MONETARY,"de_DE");
                                            if(is_numeric($cluster->cost_all_at_once_adult)) : ?>
                                                    <li style="line-height:8px; margin-top:12px;"><h4>Costen:</h4><p><?php 
                                                    echo str_replace('EUR', '', money_format("%i", $cluster->cost_all_at_once_adult)); ?> &euro; bzw 
                                            <?php endif;
                                                  if(is_numeric($cluster->cost_3_month_in_advance_adult)) : 
                                            ?> 
                                                    <?php 
                                                    echo ', '. str_replace('EUR', '', money_format("%i", $cluster->cost_3_month_in_advance_adult)); ?> &euro; pro Seminarterim </p></li>
                                            <?php endif; ?> 

                                      <?php } ?>    

                                              <li style="line-height:8px; margin-top:12px;"><h4>Leitung</h4><p>{{$evt['guide_name']}}</p></li>

                                              <li style="line-height:8px; margin-top:12px;"><h4>Anmeldung</h4><p>Diese Veranstaltung ist bereits ausgebucht!</p></li>
                                              <li style="line-height:8px; margin-top:12px;"><h4>Kosten</h4><p>Kunstvereinsmitglieder: 28,00 &euro;</p></li>
                                            </ul>
                                         </div>

                                         <div style="width:65%; float:left; text-align:left;">
                                           <p style="text-align:left;">Carel Fabritius (1622-1654), Johannes Vermeer (1632-1675) und Pieter de Hooch (1629-1684) sind die herausragenden Namen der Delfter Malerei nach der Mitte des 17. Jahrhunderts. Ihre Bilder geboren zu den besonderen Schatzen und Publikumslieblingen in den Museen der Welt. Vermeer und de Hooch entwichelten in der politisch wie wirtschaftlich bedeutenden Stadt Delf ihre faszinierenden Bildwelten: Sie widmeten sich neuartigen Genredarstellungen mit wenigen Figuren in einem Innenraum, schufen Szenen von unvergleichlicher Stille und Ein-dringlichkeit, in denen eine beispellose Stimmung des Lichts und eine
                                            <br><br><button style="background:url(../images/button_register.png) no-repeat; width:101px; height: 23px; border:none; color:#fff; font-size:11px;">ANMELDEN ></button>
                                           </p>
                                         <!-- </div>  -->
                                        <form method="post" action="">  
                                        <ul style="list-style:none; min-height:1200px; max-height:auto; margin-left:0; padding-left:0; background:#fff;">
                                          <li style="min-height:10px;">
                                          </li>
                                          <li style="border-top:0px solid #eee; background:#fff;">
                                             <div style="width:0%; float:left;">&nbsp;</div>
                                             <div style="width:100%; float:left; text-align:left; padding-left:0px;">
                                               <h3>Anmeldung</h3>
                                               <p>Hiermit melde ich forgende Anzahl Personen verbindlich zu oben stehender Veranstaltung an:</p>
                                               <table style="width:100%; list-style:none; border-spacing: 0px; padding-left:0; background:#fff;">
                                                 <tr>
                                                    <td style="width:40px; height:23px; padding:0px; border-bottom:0px solid #eee;">
                                                      <input type="text" name="price_adult" value="1" class="price-inp-sel" style="margin-left:0px; margin-top:5px;"></td>
                                                      <td style="width:360px; float:left; padding-left:10px; padding-top:4px; line-height:23px; vertical-align: middle; height:22px; border-bottom:1px solid #eee;">Kinder </td>
                                                      <td style="width:332px; float:left; line-height:23px; vertical-align: middle; padding-top:2px; height:24px; border-bottom:1px solid #eee;"><div id="kinder_price" style="width:60px; float:left; text-align:right; margin-right:3px;">8</div> &euro; </td>
                                                 </tr> 
                                                 <tr>
                                                    <td style="width:40px; height:23px; padding:0px; border-bottom:0px solid #eee;">
                                                      <input type="text" name="price_adult" value="1" class="price-inp-sel" style="margin-left:0px; margin-top:5px;"></td>
                                                      <td style="width:360px; float:left; padding-left:10px; padding-top:4px; line-height:23px; vertical-align: middle; height:22px; border-bottom:1px solid #eee;">Erwachsene </div>
                                                      <td style="width:332px; float:left; line-height:23px; vertical-align: middle; padding-top:2px; height:24px; border-bottom:1px solid #eee;"><div id="kinder_price" style="width:60px; float:left; text-align:right; margin-right:3px;">14</div> &euro;</td>
                                                 </tr> 
                                                 <tr>
                                                    <td style="width:40px; height:23px; padding:0px; border-bottom:0px solid #eee;">
                                                      <input type="text" name="price_adult" value="1" class="price-inp" style="margin-left:0px; margin-top:5px;"></td>
                                                      <td style="width:360px; float:left; padding-left:10px; padding-top:4px; line-height:23px; vertical-align: middle; height:22px; border-bottom:1px solid #eee;">Personen </div>
                                                      <td style="width:332px; float:left; line-height:23px; vertical-align: middle; padding-top:2px; height:24px; border-bottom:1px solid #eee;"><div id="kinder_price" style="width:60px; float:left; text-align:right; margin-right:3px;">22</div> &euro;</td>
                                                 </tr> 
                                               </table>  

                                               <table border="0" style="width:746px; clear:both; border-spacing: 0px; list-style:none; padding-left:0; margin-top:9px;">
                                                 <tr>
                                                   <td style="width:35%; min-width:180px; background:#eee; line-height: 24px; vertical-align: middle; 
                                                        border-bottom:1px solid #fff; padding-left:8px;">Name *</td>
                                                   <td style="width:65%; line-height: 22px; border-bottom:1px solid #fff; vertical-align: middle; background:#eee; padding:0px; height:22px;"><input name="first_name" style="width:100%; border:none; background:#eee; height:24px;">
                                                   </td> 
                                                 </tr>
                                                 <tr>
                                                   <td style="width:35%; min-width:180px; background:#eee; line-height: 24px; vertical-align: middle; 
                                                        border-bottom:1px solid #fff; padding-left:8px;">Nachname *</td>
                                                   <td style="width:65%; line-height: 22px; border-bottom:1px solid #fff; vertical-align: middle; background:#eee; padding:0px; height:22px;"><input name="first_name" style="width:100%; border:none; background:#eee; height:24px;">
                                                   </td>  
                                                 </tr>
                                                 <tr>
                                                    <td style="width:35%; min-width:180px; background:#eee; line-height: 14px; vertical-align: top; padding-top:5px; border-bottom:1px solid #fff; padding-left:8px;">Name und Alter der Kinder<br>(Angabe freiwiltdg) </td>
                                                    <td style="width:520px; padding:0px; border-bottom:1px solid #fff; background:#eee; height:70px;"><textarea name="children_detail" 
                                                    style="border:none; background:#eee; margin-top:2px; width:99.6%; height:99%;"></textarea> 
                                                    </td> 
                                                 </tr>
                                                 <tr>
                                                    <td style="width:35%; min-width:180px; background:#eee; line-height: 25px; vertical-align: middle; 
                                                        border-bottom:1px solid #fff; padding-left:8px;">Emailadresse *</td>
                                                   <td style="width:65%; line-height: 22px; border-bottom:1px solid #fff; vertical-align: middle; background:#eee; padding:0px; height:22px;"><input name="first_name" style="width:100%; border:none; background:#eee; height:25px;">
                                                    </td>  
                                                 </tr>
                                                 <tr>
                                                    <td style="width:35%; min-width:180px; background:#eee; line-height: 25px; vertical-align: middle; 
                                                        border-bottom:1px solid #fff; padding-left:8px;"><input type="checkbox" name="is_member" style="border:none; position:relative; top:-1px; margin-right:8px;">Mitgtded im Kunstverein *</td>
                                                    <td style="width:55%; padding:0px; height:24px;"><input name="first_name" style="width:98%; border:none; background:#fff; height:25px; margin-left:10px;" placeholder="Mitgtdedsnummer"></td>
                                                 </tr> 
                                                 <tr>
                                                    <td style="width:100%; background:#fff; line-height: 25px; vertical-align: middle; 
                                                        border-bottom:1px solid #fff; padding-left:8px;" colspan="2"><h4>Zahlung</h4>
                                                      <p>Wir bieten als Zahlungsm&ouml;gtdchtkeit Bankeinzug an.<br>Diese Einzugserm&auml;chtigung gilt nur f&uuml;r die hier aufgef&uuml;hten Veranstaltungen, danach ertdscht sie.</p>
                                                   </td>
                                                 </tr>
                                                 <tr>
                                                    <td style="width:35%; min-width:180px; background:#eee; line-height: 25px; vertical-align: middle; 
                                                        border-bottom:1px solid #fff; padding-left:8px;">IBAN *</td>
                                                   <td style="width:65%; line-height: 22px; border-bottom:1px solid #fff; vertical-align: middle; background:#eee; padding:0px; height:22px;"><input name="first_name" style="width:100%; border:none; background:#eee; height:24px;">
                                                   </td>  
                                                 </tr> 
                                                 <tr>
                                                    <td style="width:35%; min-width:180px; background:#eee; line-height: 25px; vertical-align: middle; 
                                                        border-bottom:1px solid #fff; padding-left:8px;">Kontoinhaber *</td>
                                                   <td style="width:65%; line-height: 22px; border-bottom:1px solid #fff; vertical-align: middle; background:#eee; padding:0px; height:22px;"><input name="first_name" style="width:100%; border:none; background:#eee; height:24px;">
                                                   </td>  
                                                 </tr> 
                                                 <tr>
                                                    <td style="width:35%; min-width:180px; background:#eee; line-height: 25px; vertical-align: middle; 
                                                        border-bottom:1px solid #fff; padding-left:8px;">Name der Bank *</td>
                                                   <td style="width:65%; line-height: 22px; border-bottom:1px solid #fff; vertical-align: middle; background:#eee; padding:0px; height:22px;"><input name="first_name" style="width:100%; border:none; background:#eee; height:24px;">
                                                   </td>  
                                                 </tr> 
                                                 <tr>
                                                    <td style="width:100%; background:#fff; line-height: 25px; vertical-align: middle; 
                                                        border-bottom:1px solid #fff; padding-left:8px;" colspan="2"><p>* Pftdchtfelder</p>
                                                   </td>
                                                 </tr>
                                                 <tr>
                                                    <td colspan="2" style="width:100%; background:#fff;"><input type="checkbox" name="terms_1" style="position:relative; top:0px;"><span style="position:relative; top:2px; margin-left:10px;">Mit den <u>Anmelde- und Teilnahmebedingugen</u> erkl&auml;re ich mich einverstanden.</span></td>
                                                 </tr> 
                                                 <tr>
                                                    <td colspan="2" style="width:100%; background:#fff;"><input type="checkbox" name="terms_2" style="position:relative; top:0px;"><span style="position:relative; top:2px; margin-left:10px;">Ich will gerne den Newsletter der Kunsthalle Bremen empfangen.</span>
                                                    </td>  
                                                 </tr> 
                                                 <tr>
                                                   <td colspan="2" style="width:100%; padding:20px 0;">
                                                      <button style="background:#E65655; border:none; color:#fff; font-family: 'Circular-Book'; cursor:pointer; padding:8px 16px; font-size:10px;">JETZT ANMELDEN</button>
                                                   </td>
                                                 </tr>
                                                 <tr><td colspan=2 style="height:50px; background:#fff;">&nbsp;</td></tr>
                                               </table>
                                             </div> 
                                          </li>
                                       </ul>
                                       </form>
                                     </p>
                                   </div>
                                 </div>
                                </div>
                                 
                              </div>

                        <?php endforeach; ?>  

                  <?php endforeach; ?>      

                    </div>
                </div>
    
      <?php endforeach; ?>
          </div>

      </div>
    </div>

  </div> 


<script>
$(function() {

  $('.accordion .hide').hide();

});

var show_reg = true;
var cur_reg = 0;

function showReg(id) {
  if(id != cur_reg) {
    hideCurReg(id);
  } else {
    show_reg = true;
  }
  var main_panel_ht = $('#main_panel').css('height').replace('px', '');
  var slider_ht = $('#cal_slider').css('height').replace('px', '');
  var panel_ht = 100;
  if(show_reg) {
      $('#event_title').addClass('title-xl');
      $('#event_subtitle').addClass('title-l');
      $('#hide_'+id).fadeIn(200);
      $('#hide_'+id).show(); //slideDown(650);
      $('#hide_'+id).css('background', '#fff');
      $('#edb_'+id).css('background', '#fff');
      $('#et_'+id).css('color', '#222');
      $('#more_btn_'+id).hide();
      // $('#cal_slider').css('')
      panel_ht = $('#hide_'+id).css('height').replace('px', '');;
      main_panel_new_ht = (parseInt(main_panel_ht) + parseInt(panel_ht)) + 'px';
      $('#main_panel').css('height', main_panel_new_ht);
      document.getElementById('main_panel').style.height = main_panel_new_ht;
      cal_slider_new_ht = (parseInt(slider_ht) + parseInt(panel_ht)) + 'px';
      $('#cal_slider').css('height', cal_slider_new_ht);
      document.getElementById('cal_slider').style.height = cal_slider_new_ht;
      show_reg = false;
  } else {
      panel_ht = $('#hide_'+id).css('height').replace('px', '');;
      main_panel_new_ht = (parseInt(main_panel_ht) - parseInt(panel_ht)) + 'px';
      $('#main_panel').css('height', main_panel_new_ht);
      cal_slider_new_ht = (parseInt(slider_ht) - parseInt(panel_ht)) + 'px';
      $('#cal_slider').css('height', cal_slider_new_ht);

      $('#hide_'+id).fadeOut(200);
      $('#hide_'+id).hide();
      $('#edb_'+id).css('background', 'none');
      $('#et_'+id).css('color', '#fff');
      $('#more_btn_'+id).show();
      show_reg = true;
  }
  cur_reg = id;
}

function hideCurReg() {
    $('#hide_'+cur_reg).hide();
    show_reg = true;
    var main_panel_ht = $('#main_panel').css('height').replace('px', '');
    var slider_ht = $('#cal_slider').css('height').replace('px', '');
    var panel_ht = 100;

    if(!isNaN(cur_reg) && cur_reg > 0) {
      $('#event_title').removeClass('title-xl');
      $('#event_subtitle').removeClass('title-l');

      panel_ht = $('#hide_'+cur_reg).css('height').replace('px', '');;
      main_panel_new_ht = (parseInt(main_panel_ht) - parseInt(panel_ht)) + 'px';
      $('#main_panel').css('height', main_panel_new_ht);
      cal_slider_new_ht = (parseInt(slider_ht) - parseInt(panel_ht)) + 'px';
      $('#cal_slider').css('height', cal_slider_new_ht);

      $('#hide_'+cur_reg).fadeOut(200);
      $('#hide_'+cur_reg).hide();
      $('#edb_'+cur_reg).css('background', 'none');
      $('#et_'+cur_reg).css('color', '#fff');
      $('#more_btn_'+cur_reg).show();      
    }
}

</script>

<script src="../../../js/jquery-1.11.0.min.js"></script>
<script src="../../../js/jquery.cycle.lite.js"></script>
<script>

// $(function() {
//   // $('#more_btn_1').click();

//   // $('#edb_1').click(1);

// });

/**/
$jq1 = jQuery.noConflict();

$jq1(function() {

  if($jq1("#cal_slider").length) {
    $jq1("#cal_slider").cycle({
        timeout: 0,
        fx: 'scrollHorz',
        // prev: '#prev',
        // next: '#next',
        speed: 900
    });
  }

  $jq1('.accordion .hide').hide();

  /*
  $jq1('.accordion .title').click(function () {
    // alert('accordion..')
      $jq1(this).parent().next().slideToggle().siblings('.hide').slideUp();
  });

  $jq1('.accordion .title:last').click(function () {
       $jq1('.accordion .hide').slideUp();
  });
  /**/

});

var cnt = 0;
function showPrev() {
    console.log('Prev..');
    if(cnt > 1) {
        --cnt;
    } else {
      cnt = 1;
    }
    $jq1("#cal_slider").cycle('prev');
    // $jq1('#fs_1').slideUp();
}
function showNext() {
    console.log('Next..');
    if(cnt < 3) {
        ++cnt;
    } else {
      cnt = 3;
    }
    $jq1("#cal_slider").cycle('next');
}
/**/

</script>



<!-- Accordion functionality -->

<style>
input {
  font-size:12px;
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
   width:39px; height:21px; text-align:center; border:none; background:#fff; text-align:center;
}
.price-inp-sel {
   width:39px; height:21px; text-align:center; border:none; background:#eee; text-align:center;
}
</style>

<script>
// var acc = document.getElementsByClassName("accordion");
var i;
window.onload = function() {
  showReg(1);
}
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