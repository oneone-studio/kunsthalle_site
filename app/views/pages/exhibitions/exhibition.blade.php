 @extends('layouts.default')
@section('content')
<?php setlocale(LC_ALL, "de_DE", 'German', 'german'); ?>
  <div class="row">    
    <div class="col-md-12">
      <div id="main_content_inner" style="width:100%; clear:both; padding-top:0px; position:relative;vertical-align:middle;">

          <div style="width:100%; clear:both; margin:20px auto 0px auto; position:relative;">
            <button style="width:170px; height:30px; background:#eeeedd; position:absolute; left:15px; top:15px; z-index:9999; color:red; border:none;
              background:#fff; text-align:center; vertical-align:middle; font-size:12px; font-family:Circular-Book;"
              onclick="this.blur();">ALLE AUSSTELLUNGEN</button>

            <div style="width:230px; height:30px; background:none; position:absolute; right:-32px; top:15px; z-index:9999;">
              <div style="width:72px; height:90px; display:inline-block; margin-left:5px; border:0px solid black;
              background:url(/images/date_bg.png) no-repeat; background-size:72px 84px; padding:5px; text-align:center; vertical-align:middle;">
                 <div style="width:100%; height:18px; clear:both; font-size:9px; position:relative; left:-2px; text-align:center; color:#111; font-family:Circular-Book;">START</div>
                 <div style="width:100%; clear:both; text-align:center; margin-left:-2px; color:#000; font-size:12px; font-weight:bold; font-family:Circular-Book; line-height:14px;">
                   <?php echo strtoupper(strftime(date('M', strtotime($exhibition->start_date)))); ?><br>
                   <?php echo date('d', strtotime($exhibition->start_date)); ?><br>
                   <?php echo date('Y', strtotime($exhibition->start_date)); ?>
                 </div> 
              </div>
              <div style="width:72px; height:90px; display:inline-block; margin-left:5px; border:0px solid black;
              background:url(/images/date_bg.png) no-repeat; background-size:72px 84px; padding:5px; text-align:center; vertical-align:middle;">
                 <div style="width:100%; height:18px; clear:both; font-size:9px; position:relative; left:-2px; text-align:center; color:#111; font-family:Circular-Book;">ENDE</div>
                 <div style="width:100%; clear:both; text-align:center; margin-left:-2px; color:#000; font-size:12px; font-weight:bold; font-family:Circular-Book;line-height:14px;">
                   <?php echo strtoupper(strftime(date('M', strtotime($exhibition->end_date)))); ?><br>
                   <?php echo date('d', strtotime($exhibition->end_date)); ?><br>
                   <?php echo date('Y', strtotime($exhibition->end_date)); ?>
                 </div> 
              </div>
            </div>  

            <div id="prev" class="prev" style="width:17px; height:31px;display:none;position:absolute; left:15px; top:50%; z-index:9999; border:0px solid red;" 
               onclick="this.blur()">&nbsp;</div>
            <div id="next" class="next" style="width:17px; height:31px;display:none; position:absolute; top:50%; right:15px; z-index:9999; border:none; cursor:pointer;"   onclick="this.blur()">&nbsp;</div>
            <div id="home_slider" class="home_slider" style="position:relative; min-height:768px;padding:0;">
            <?php
              $slides = [ 'slide_1.jpg', 'slide_2.jpg', 'slide_3.jpg' ];
              $cnt = 0;
              foreach($slides as $slide) {
                  ++$cnt;
                  $slide_img = '/images/'. $slide;
            ?>      
                  <div class="fs_slide" style="background:url('<?php echo $slide_img;?>') no-repeat; background-position: center -90px; background-size:cover; ">
                  </div>
        <?php } ?>
            </div>
          </div>  

          <div style="max-width:800px; margin:25px auto; background:none;">
          <?php echo '<span class="exb-title"><strong>'. $exhibition->title_de .'</strong></span><br>
            <div class="exb-subtitle">'. $exhibition->subtitle_de .'</div><br>
            <span class="exb-content">'. $exhibition->content_de . '</span>'; ?>
          </div>

          <div style="width:100%; clear:both; margin:20px auto 0px auto; position:relative;">
            <div id="s2_prev" class="prev" style="width:17px; height:31px; position:absolute; left:15px; top:50%; z-index:9999; border:none;" onclick="this.blur()">&nbsp;</div>
            <div id="s2_next" class="next" style="width:17px; height:31px; position:absolute; top:50%; right:15px; z-index:9999; border:none; cursor:pointer;" onclick="resetSlide();this.blur()">&nbsp;</div>

            <div id="gallery_slider" class="gallery_slider" style="position:relative; min-height:768px; padding:0;">
            <?php
              $slides = $exhibition->gallery_images;
              $cnt = 0;
              foreach($slides as $slide) {
                  $slide_img = '/images/'. $slide->image;
            ?>      
                  <div class="fs_slide">
                    <div class="fs_slide fs" 
                    style="background:url('<?php echo $slide_img; ?>') no-repeat; background-position: center -0px; background-color:#EDF4F8; position:relative;" id="slide_<?php echo $slide->id;?>">&nbsp;</div>
                    
                    <div style="width:100%; position:absolute; bottom:0; background:#D3D3D3; min-height:90px;" 
                      id="slide_detail_block_<?php echo $slide->id;?>">
                       <div style="width:93%; float:left; display:inline;">
                          <div style="width:75%; float:left; font-family:Dekka; font-size:12px; display:inline; padding:15px 20px;" id="slide_detail_<?php echo $slide->id;?>"></div>
                       </div>
                       <div id="read_btn_<?php echo $slide->id;?>" class="gallery-read-btn-u" onclick="toggleSlideDetail('<?php echo $slide->id;?>');">&nbsp;</div>
                    </div>   
                    <input type="hidden" id="slide_detail_id_<?php echo $slide->id;?>" value="0">
                 </div> 
        <?php     ++$cnt;
              }
              ?>
            </div>
          </div>

          <div style="width:100%; overflow:hidden;">
            @include('pages.calendar-block')
          </div>

      </div>
    </div>

  </div> 

<script src="/js/jquery-1.11.0.min.js"></script>
<script src="/js/jquery.cycle.lite.js"></script>
<script>

var curSlideId = 0;
var curBG = '';
var detailActive = false;
$jq1 = jQuery.noConflict();
var galleryHTML = '';

$jq1(function() {

  galleryHTML = $jq1("#gallery_slider").html();
  console.log(galleryHTML);

  if($jq1("#home_slider").length) {
    $jq1("#home_slider").cycle({
        timeout: 0,
        fx: 'scrollHorz',
        prev: '#prev',
        next: '#next',
        speed:    400
    });
  }

  if($jq1("#gallery_slider").length) {
    $jq1("#gallery_slider").cycle({
        timeout: 0,
        fx: 'scrollHorz',
        prev: '#s2_prev',
        next: '#s2_next',
        speed:    400
    });
  }

  destroyGallerySlide = function() {
    $jq1("#gallery_slider").cycle('destroy');
  }

  reloadGalleryHtml = function() {
    $jq1("#gallery_slider").html(galleryHTML);
  }

  reloadGallerySlider = function() {
    $jq1("#gallery_slider").cycle({
        timeout: 0,
        fx: 'scrollHorz',
        prev: '#s2_prev',
        next: '#s2_next',
        speed:    400
    });
    // if(!isNaN(curSlideId) && curSlideId >= 0) { $jq1('#slide_'+curSlideId).removeClass('slide-detail'); }
  }

  closeSlideDetail = function(id) {
    // if(!isNaN(curSlideId) && curSlideId >= 0) {
      $jq1('#slide_'+id).removeClass('slide-detail').addClass('fs');
      $jq1('#slide_detail_'+id).css('display', 'none');
      $jq1('#s2_prev').show();
      $jq1('#s2_next').show();
      $jq1('#read_btn_'+id).removeClass('gallery-read-btn-d').addClass('gallery-read-btn-u');
      resetOnclick();
    // }
  }

  resetOnclick = function() {
    $jq1('#slide_'+curSlideId).attr('onclick', "showSlideDetail('"+curSlideId+"')");
    return false;
  }

  resizeSlide = function(id) {
    // if(!isNaN(curSlideId) && curSlideId >= 0) {
      curBG = $jq1('#slide_'+id).css('background');
      $jq1('#slide_'+id).removeClass('fs').addClass('slide-detail');
      // $jq1('#s2_prev').hide();
      // $jq1('#s2_next').hide();
      $jq1('#read_btn_'+id).removeClass('gallery-read-btn-u').addClass('gallery-read-btn-d');
    // } 
  }

});

var curSlideDetailId = 0;
var curSlideDetailActive = false;

function toggleSlideDetail(id) {
  var isDetailActive = 0;
  if($('#slide_detail_id_'+id).length) {
    isDetailActive = $('#slide_detail_id_'+id).val();
  }
  isDetailActive = parseInt(isDetailActive);
  detailActive = isDetailActive;
  
  if(detailActive) {
    closeSlideDetail(id);
    $('#slide_detail_id_'+id).val(0); 
    detailActive = false;
  } else {
    showSlideDetail(id);
    $('#slide_detail_id_'+id).val(1); 
    detailActive = true;
  }
  curSlideDetailId = id;
}

function showSlideDetail(id) {
  if(!isNaN(id)) {
    curSlideId = id;
    $.ajax({
      type: 'GET',
      url: '/get-slide-detail',
      data: { 'id' : id },
      dataType: 'json',
      success:function(data) { 
            console.log('showSlideDetail() response..');
            console.log(data.slide);
            if(data.slide != undefined) {
              var sl = data.slide;
              $('#slide_detail_'+id).html(sl.detail).css('display', 'inline');
              $('#slide_detail_block_'+id).css('display', 'inline');
              $('#slide_'+id).removeAttr('onclick');
              resizeSlide(id);
            }
        },
      error:  function(jqXHR, textStatus, errorThrown) {
              console.log('showSlideDetail failed.. ');
            }
    }); 
  }
}

function resetSlide() {
  if(!isNaN(curSlideId) && curSlideId > 0) {
    $('#slide_'+curSlideId).css('background-size', '1024px 768px');
    $('slide_detail_'+curSlideId).css('display', 'none');
  }
}

</script>

@stop

