@extends('layouts.default')
@section('content')

<?php
      $cal = [];
      $cal[] = [ 
                'month' => 'JUNI',
                'year'  => '2015',
                'days'  => [ 
                      '29 SAMSTAG' => [
                          'events' => [
                            0 => [ 'main_title' => 'JAN VERMEER UND DIE DELFTER SCHULE',
                               'subtitle'   => 'MITGLIEDER AKADEME TAGSSEMINAR',
                               'start_time' => '11:00',
                               'end_time'   => '12:00',
                               ],
                            1 => [ 'main_title' => 'KEKSE, KUNST UND LIMONADE',
                               'subtitle'   => 'KINDERWORKSHOP F&Uuml;R ALLE AB 6 JAHRE',
                               'start_time' => '18:00',
                               'end_time'   => '17:00',
                               ],
                          ],                               
                        ],  
                      '30 SONNTAG' => [
                          'events' => [
                            0 => [ 'main_title' => 'VOM KLASSIZISMUS ZUM KUBISMUS',
                                 'subtitle'   => 'F&Uuml;RHNG: MEISTERWERKE DER FRANZ&Ouml;SISCHEN MALEREI',
                                 'start_time' => '13:00',
                                 'end_time'   => '15:00',
                                 ],
                              ],
                            ],  
                       ],
            ];    
      $cal[] = [ 
                'month' => 'JULI',
                'year'  => '2015',
                'days'  => [ 
                      '19 SAMSTAG' => [
                          'events' => [
                            0 => [ 'main_title' => 'JAN VERMEER UND DIE DELFTER SCHULE',
                               'subtitle'   => 'MITGLIEDER AKADEME TAGSSEMINAR',
                               'start_time' => '11:00',
                               'end_time'   => '12:00',
                               ],
                            1 => [ 'main_title' => 'KEKSE, KUNST UND LIMONADE',
                               'subtitle'   => 'KINDERWORKSHOP F&Uuml;R ALLE AB 6 JAHRE',
                               'start_time' => '18:00',
                               'end_time'   => '17:00',
                               ],
                          ],                               
                        ],  
                      '23 SONNTAG' => [
                          'events' => [
                            0 => [ 'main_title' => 'VOM KLASSIZISMUS ZUM KUBISMUS',
                                 'subtitle'   => 'F&Uuml;RHNG: MEISTERWERKE DER FRANZ&Ouml;SISCHEN MALEREI',
                                 'start_time' => '13:00',
                                 'end_time'   => '15:00',
                                 ],
                              ],
                            ],  
                       ],
            ];    
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
                               <div id="prev" onclick="showPrev()" style="width:15px; float:left; cursor:pointer; background:url('../images/arrowleft.png') no-repeat; background-size:12px 22px;">&nbsp;</div>
                               <div id="next" onclick="showNext()" style="width:15px; float:right; cursor:pointer; background:url('../images/arrowright.png') no-repeat; background-size:12px 22px;">&nbsp;</div>
                            </div>
                        </div>
                        <div style="width:100%; height:40px; margin:15px auto; clear:both; text-align:center; font-size:16px; font-weight:bold;"><?php echo $c['year'];?></div>
                   
                  <?php foreach($c['days'] as $day => $data): ?>                          
                        
                          <div class="evt-day-block" style="width:98%; min-height:40px; padding:10px; margin:35px auto 10px auto; border-bottom:0px solid #fff; color:#fff; clear:both; background:none; text-align:center; font-size:18px; font-weight:bold;"><?php echo $day;?></div>
                          <?php 
                            $cnt = 0;
                            foreach($data['events'] as $evt): 
                               ++$cnt; 
                          ?>

                              <div id="edb_<?php echo $cnt;?>" class="evt-day-block accordion" style="width:98%; min-height:100px; position:relative; margin:0px auto 0px auto; border-bottom:1px solid #fff; color:#000; clear:both; background:none; text-align:center;">
                                 <div style="width:100%; margin:5px auto 0 auto; position:relative; top:15px;">
                                   <h4 style="height:14px;"><?php echo $evt['main_title'];?></h4>
                                   <h7><?php echo $evt['subtitle'];?></h7>
                                   <div id="et_<?php echo $cnt;?>" style="width:100px; height:20px; padding-top:2px; position:absolute; top:0px; left:15px; font-size:11px; text-align:left; background:url('../images/clock_white.png') no-repeat; background-size:18px 18px; background-position:-1px -1px; padding-left:23px; color:#fff;">
                                    <?php echo $evt['start_time'] .' - '. $evt['end_time']; ?>
                                   </div>
                                 </div>
                                 <div id="more_btn_blk_<?php echo $cnt;?>" onclick="showReg('<?php echo $cnt;?>')" class="title" style="width:100%; clear:both; margin-top:20px; float:left; text-align:center;"><div id="more_btn_<?php echo $cnt;?>" class='more-btn' style="background:url('../images/more_btn.png') no-repeat; background-size:64px 22px; border:none; width:64px; height:22px; cursor:pointer; margin:auto;">&nbsp;</div>
                                   <div class="hide" id="hide_<?php echo $cnt;?>" style="width:100%; height:100%; background:#fff;">
                                      <div style="width:100%; float:left; position:relative;">
                                         
                                         <div id="close_btn_<?php echo $cnt;?>" onclick="closeEDB('<?php echo $cnt;?>')" style="position:absolute; top:2px; right:10px; background:url(../images/close.png) no-repeat; width:20px; height:20px; cursor:pointer; border:none;">&nbsp;</div>
                                         
                                         <div style="width:25%; float:left; margin-left:15px;">
                                            <ul style="list-style:none; padding-left:0; text-align:left;">
                                              <li style="line-height:8px; margin-top:12px;"><h6>Anmeldung</h6><p>Diese Veranstaltung ist bereits ausgebucht!</p></li>
                                              <li style="line-height:8px; margin-top:12px;"><h6>Kosten</h6><p>Kunstvereinsmitglieder: 28,00 &euro;</p></li>
                                              <li style="line-height:8px; margin-top:12px;"><h6>Leitung</h6><p>Dr. Alice Gudera</p></li>
                                            </ul>
                                         </div>
                                         <div style="width:65%; float:left; text-align:left;">
                                           <p style="text-align:left;">Carel Fabritius (1622-1654), Johannes Vermeer (1632-1675) und Pieter de Hooch (1629-1684) sind die herausragenden Namen der Delfter Malerei nach der Mitte des 17. Jahrhunderts. Ihre Bilder geboren zu den besonderen Schatzen und Publikumslieblingen in den Museen der Welt. Vermeer und de Hooch entwichelten in der politisch wie wirtschaftlich bedeutenden Stadt Delf ihre faszinierenden Bildwelten: Sie widmeten sich neuartigen Genredarstellungen mit wenigen Figuren in einem Innenraum, schufen Szenen von unvergleichlicher Stille und Ein-dringlichkeit, in denen eine beispellose Stimmung des Lichts und eine
                                            <br><br><button style="background:url(../images/button_register.png) no-repeat; width:101px; height: 23px; border:none; color:#fff; font-size:11px;">ANMELDEN ></button>
                                           </p>
                                         </div> 
                                      </div>
                                      <!--                                       
                                      <form class="form-inline"> 
                                        <div class="form-group">
                                          <label for="exampleInputEmail1">Email address</label>
                                          <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Email">
                                        </div>                                       
                                      </form>
                                      -->                                  
                                        <ul style="list-style:none; min-height:1200px; max-height:auto; padding-left:0; background:#fff;">
                                          <li style="min-height:140px;">
                                          </li>
                                          <li style="border-top:1px solid #eee; background:#fff;">
                                             <div style="width:25%; float:left;">&nbsp;</div>
                                             <div style="width:70%; float:left; text-align:left; padding-left:15px;">
                                               <h6>Anmeldung</h6>
                                               <p>Hiermit melde ich forgende Anzahl Personen verbindlich zu oben stehender Veranstaltung an:</p>
                                               <ul style="width:100%; list-style:none; padding-left:0;">
                                                 <li>
                                                    <div style="width:85%; height:25px; margin-bottom:2px; float:left; display:block;clear:both; border-bottom:1px solid #eee;">
                                                      <div style="width:6%; height:25px; float:left; background:#eee; text-align:center; border-bottom:0px solid #fff;"><span style="position:relative; top:4px;">1</span></div>
                                                      <div style="width:40%; float:left; padding:4px 8px 0px 8px; height:23px;">Kinder </div>
                                                      <div style="width:40%; float:left; padding:4px 8px 0px 8px; height:23px;"><div style="width:40px; text-align:right;">8 &euro;</div> </div>
                                                    </div>
                                                 </li> 
                                                 <li>
                                                    <div style="width:85%; height:25px; margin-bottom:2px; float:left; display:block;clear:both; border-bottom:1px solid #eee;">
                                                      <div style="width:6%; height:25px; float:left; background:#eee; text-align:center; border-bottom:0px solid #fff;"><span style="position:relative; top:4px;">1</span></div>
                                                      <div style="width:47%; float:left; padding:4px 8px 0px 8px; height:24px;">Erwachsene </div>
                                                      <div style="width:47%; float:left; padding:4px 8px 0px 8px; height:24px;"><div style="width:40px; text-align:right;">14 &euro;</div> </div>
                                                    </div>
                                                 </li> 
                                                 <li>
                                                    <div style="width:85%; height:25px; margin-bottom:2px; float:left; display:block;clear:both; border-bottom:1px solid #eee;">
                                                      <div style="width:6%; height:25px; float:left; background:#fff; text-align:center; border-bottom:2px solid #fff;"><span style="position:relative; top:4px;">2</span></div>
                                                      <div style="width:47%; float:left; padding:4px 8px 0px 8px; height:23px;">Personen </div>
                                                      <div style="width:47%; float:left; padding:4px 8px 0px 8px; height:24px;"><div style="width:40px; text-align:right;">22 &euro;</div></div>
                                                    </div>  
                                                 </li>
                                               </ul>  
                                               <ul style="width:100%; clear:both; list-style:none; padding-left:0; margin-top:9px;">
                                                 <li>
                                                    <div style="width:85%; float:left; background:#eee; display:block; border-bottom:1px solid #fff; margin-top:1px; clear:both;">                                                      
                                                      <div style="width:30%; height:25px; float:left; text-align:left;"><span style="position:relative; top:5px; left:10px;">Name *</span></div>
                                                      <div style="width:70%; float:left; padding:4px 8px 0px 8px; height:23px;"><input name="first_name" style="border:none; background:#eee;"> </div>
                                                    </div>  
                                                 </li> 
                                                 <li>
                                                    <div style="width:85%; float:left; background:#eee; display:block; border-bottom:1px solid #fff; margin-top:1px; clear:both;">                                                      
                                                      <div style="width:30%; height:25px; float:left; text-align:left;"><span style="position:relative; top:5px; left:10px;">Nachname *</span></div>
                                                      <div style="width:70%; float:left; padding:4px 8px 0px 8px; height:23px;"><input name="surname" style="border:none; background:#eee;"> </div>
                                                    </div>  
                                                 </li> 
                                                 <li>
                                                    <div style="width:85%; height:60px; float:left; background:#eee; display:block; border-bottom:1px solid #fff; margin-top:1px; clear:both;">                                                      
                                                      <div style="width:30%; height:25px; float:left; text-align:left;"><span style="position:relative; top:5px; left:10px;">Name und Alter der Kinder (Angabe freiwillig) </span></div>
                                                      <div style="width:70%; float:left; padding:4px 8px 0px 8px; height:23px;"><textarea name="children_detail" style="border:none; background:#eee; width:100%; height:50px;"></textarea> </div>
                                                    </div>  
                                                 </li> 
                                                 <li>
                                                    <div style="width:85%; float:left; background:#eee; display:block; border-bottom:1px solid #fff; margin-top:1px; clear:both;">                                                      
                                                      <div style="width:30%; height:25px; float:left; text-align:left;"><span style="position:relative; top:5px; left:10px;">Emailadresse *</span></div>
                                                      <div style="width:70%; float:left; padding:4px 8px 0px 8px; height:23px;"><input name="first_name" style="border:none; background:#eee;"> </div>
                                                    </div>  
                                                 </li> 
                                                 <li>
                                                    <div style="width:85%; float:left; background:#eee; display:block; border-bottom:1px solid #fff; margin-top:1px; clear:both;">                                                      
                                                      <div style="width:4%; float:left;">&nbsp;</div>
                                                      <div style="width:26%; height:25px; float:left; background:#fff; text-align:left;"><span style="position:relative; top:5px; left:10px;">Mitglied im Kunstverein *</span></div>
                                                      <div style="width:60%; float:left; padding:6px 8px 0px 8px; height:23px;">Mitgliedsnummer </div>
                                                    </div>  
                                                 </li> 
                                                 <li>
                                                    <div style="width:85%; padding:10px 0 5px 0; float:left; background:#fff; display:block; border-bottom:1px solid #fff; margin-top:1px; clear:both;">
                                                      <h6>Zahlung</h6>
                                                      <p>Wir bieten als Zahlungsm&ouml;glichtkeit Bankeinzug an.<br>Diese Einzugserm&auml;chtigung gilt nur f&uuml;r die hier aufgef&uuml;hten Veranstaltungen, danach erlischt sie.</p>
                                                   </div>
                                                 </li>
                                                 <li>
                                                    <div style="width:85%; float:left; background:#eee; display:block; border-bottom:1px solid #fff; margin-top:1px; clear:both;">                                                      
                                                      <div style="width:30%; height:25px; float:left; text-align:left;"><span style="position:relative; top:5px; left:10px;">IBAN *</span></div>
                                                      <div style="width:70%; float:left; padding:4px 8px 0px 8px; height:23px;"><input name="first_name" style="border:none; background:#eee;"> </div>
                                                    </div>  
                                                 </li> 
                                                 <li>
                                                    <div style="width:85%; float:left; background:#eee; display:block; border-bottom:1px solid #fff; margin-top:1px; clear:both;">                                                      
                                                      <div style="width:30%; height:25px; float:left; text-align:left;"><span style="position:relative; top:5px; left:10px;">Kontoinhaber *</span></div>
                                                      <div style="width:70%; float:left; padding:4px 8px 0px 8px; height:23px;"><input name="first_name" style="border:none; background:#eee;"> </div>
                                                    </div>  
                                                 </li> 
                                                 <li>
                                                    <div style="width:85%; float:left; background:#eee; display:block; border-bottom:1px solid #fff; margin-top:1px; clear:both;">                                                      
                                                      <div style="width:30%; height:25px; float:left; text-align:left;"><span style="position:relative; top:5px; left:10px;">Name der Bank *</span></div>
                                                      <div style="width:70%; float:left; padding:4px 8px 0px 8px; height:23px;"><input name="first_name" style="border:none; background:#eee;"> </div>
                                                    </div>  
                                                 </li> 
                                                 <li>
                                                    <div style="width:85%; padding:10px 0 5px 0; float:left; background:#fff; display:block; border-bottom:1px solid #fff; margin-top:1px; clear:both;">
                                                      <p>* Pflichtfelder</p>
                                                   </div>
                                                 </li>
                                                 <li>
                                                    <div style="width:85%; float:left; background:#fff; display:block; border-bottom:1px solid #fff; margin-top:1px; clear:both;">                                                      
                                                      <div style="width:2%; float:left; background:#fff;"><input type="checkbox" name="terms_1" style="margin-top:6px;"></div>
                                                      <div style="width:98%; height:25px; float:left; text-align:left; background:#fff;"><span style="position:relative; top:5px; left:10px;">Mit den <u>Anmelde- und Teilnahmebedingugen</u> erkl&auml;re ich mich einverstanden.</span></div>
                                                    </div>  
                                                 </li> 
                                                 <li>
                                                    <div style="width:85%; float:left; background:#fff; display:block; border-bottom:1px solid #fff; margin-top:1px; clear:both;">                                                      
                                                      <div style="width:2%; float:left; background:#fff;"><input type="checkbox" name="terms_2" style="margin-top:6px;"></div>
                                                      <div style="width:98%; height:25px; float:left; text-align:left; background:#fff;"><span style="position:relative; top:5px; left:10px;">Ich will gerne den Newsletter der Kunsthalle Bremen empfangen.</span></div>
                                                    </div>  
                                                 </li> 
                                                 <li>
                                                   <div style="width:100%; float:left; padding:20px 0;">
                                                      <button style="background:#E65655; border:none; float:left; color:#fff; font-family: 'Circular-Book'; cursor:pointer; padding:8px 16px; font-size:10px;">JETZT ANMELDEN</button>
                                                   </div>
                                                 </li>

                                                 <li style="height:50px; background:#fff;">&nbsp;</li>
                                               </ul>
                                             </div> 
                                          </li>
                                       </ul>
                                     </p>
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
//   $('.accordion .title').click(function () {
//     // alert('accordion..')
//       $(this).parent().next().slideToggle().siblings('.hide').slideUp();
//   });

//   $('.accordion .title:last').click(function () {
//        $('.accordion .hide').slideUp();
//   });

});

var show_reg = true;
var cur_reg = 0;

function showReg(id) {
  if(id != cur_reg) {
    hideCurReg(id);
  }
  var main_panel_ht = $('#main_panel').css('height').replace('px', '');
  var slider_ht = $('#cal_slider').css('height').replace('px', '');
  var panel_ht = 100;
  if(show_reg) {
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
      $('#hide_'+id).hide(); //slideUp(650);
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
</style>

<script>
// var acc = document.getElementsByClassName("accordion");
var i;

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

@stop

