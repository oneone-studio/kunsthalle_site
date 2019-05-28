@extends('layouts.default')
  <script>
  </script>
@section('content')
  
  <?php //echo '<pre>'; print_r($sections); exit; ?>

    <div class="page_content" style="position:relative; width:100%;">
<!--  <p class="page-title">Pages 
        <a href="/content/pages/create/{{$cs_id}}" class="link">new</a>
        <span style="color:#9f9f9f; padding-left:2px; padding-right:0px;">|</span>
        <a href="/content/content-sections" class="link" style="margin-left:2px;">back</a>
      </p>
 -->
    <?php $slider_ids = []; ?>

    @foreach($sections as $ps)

      @if($ps->type == 'content')
        <div styel="width:97%; padding:0; margin:20px auto;">
            {{ $ps->content_de }}
        </div>
      @endif

      @if($ps->type == 'slider')

        <?php $slider_ids[] = $ps->id; ?>

        <div styel="width:100%; position:relative; clear:both; padding:0; margin:10px auto;">

            <div id="s2_prev_{{$ps->id}}" class="prev" style="width:20px; height:36px; background-color:#fcfcfc; background-position:1px 2px; opacity:.2; float:left; position:relative; left:-14px; top:350px; z-index:9999; border:0px solid yellow;" onclick="this.blur()">&nbsp;</div>
            <div id="s2_next_{{$ps->id}}" class="next" style="width:20px; height:36px; background-color:#fcfcfc; background-position:1px 2px; opacity:.2; float:right; position:relative; top:350px; right:-14px; z-index:9999; border:0px solid blue; cursor:pointer;" onclick="this.blur()">&nbsp;</div>

            <div id="gallery_slider_{{$ps->id}}" class="gallery_slider" style="width:100%; position:relative; min-height:768px; padding:0;">
            <?php
              $slides = $ps->page_slider_images;
              $cnt = 0;
              foreach($slides as $slide) {
                  $slide_img = '/files'.$slide->path . $slide->filename;
            ?>      
                  <div class="fs_slide">
                      <div class="fs_slide fs" 
                        style="background:url('<?php echo $slide_img; ?>') no-repeat; background-position: center -0px; background-color:#EDF4F8; 
                            position:relative;" id="slide_<?php echo $slide->id;?>">&nbsp;</div>
                      
                      <div style="width:100%; position:absolute; bottom:0; background:#D3D3D3; min-height:90px;" 
                        id="slide_detail_block_<?php echo $slide->id;?>">
                         <div style="width:93%; float:left; display:inline;">
                            <div style="width:75%; float:left; font-family:arial; font-size:12px; display:inline; padding:15px 20px;" id="slide_detail_<?php echo $slide->id;?>"></div>
                         </div>
                         <div id="read_btn_<?php echo $slide->id;?>" class="gallery-read-btn-u" onclick="toggleSlideDetail('<?php echo $slide->id;?>');">&nbsp;</div>
                      </div>   
                      <input type="hidden" id="slide_detail_id_<?php echo $slide->id;?>" value="0">
                 </div> 
        <?php    ++$cnt;
              }
              ?>
            </div>
            
        </div>
      @endif

    @endforeach

    </div>

<script src="/js/jquery-1.11.0.min.js"></script>
<script src="/js/jquery.cycle.lite.js"></script>
<script>

var curSlideId = 0;
var curBG = '';
var detailActive = false;
var galleryHTML = '';

$jq1 = jQuery.noConflict();
$jq1(function() {

<?php foreach($slider_ids as $sid): 
?>
        if($jq1("#gallery_slider_{{$sid}}").length) {
          $jq1("#gallery_slider_{{$sid}}").cycle({
              timeout: 0,
              fx: 'scrollHorz',
              prev: '#s2_prev_{{$sid}}',
              next: '#s2_next_{{$sid}}',
              speed:    400
          });
        }

<?php endforeach ?>

});

function showNextSlide(id) {
  alert(id);
  $jq1("#gallery_slider_"+id).cycle(1);
}

function resetSlide() {
  if(!isNaN(curSlideId) && curSlideId > 0) {
    $('#slide_'+curSlideId).css('background-size', '1024px 768px');
    $('slide_detail_'+curSlideId).css('display', 'none');
  }
}

</script>

@stop