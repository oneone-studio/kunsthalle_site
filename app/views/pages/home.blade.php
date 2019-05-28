@extends('layouts.default')
@section('content')

  <div class="row">    
    <div class="col-md-12">
      <div id="main_content_inner" style="width:100%; clear:both; padding-top:5px; position:relative;vertical-align:middle;">
          <!---->
          <div id="prev" class="prev" style="width:18px; height:28px; position:absolute; left:15px; top:50%; z-index:9999; border:0px solid red;" onclick="this.blur()">&nbsp;</div>
          <div id="next" class="next" style="width:18px; height:28px; position:absolute; top:50%; right:15px; z-index:9999; border:none; cursor:pointer;" onclick="this.blur()">&nbsp;</div>
          <!---->

          <div id="home_slider" class="home_slider" style="position:relative; min-height:768px;padding:0;">
          <?php
            $slides = [ 'slide_1.jpg', 'slide_2.jpg', 'slide_3.jpg' ];
            $cnt = 0;
            foreach($slides as $slide) {
                ++$cnt;
                $slide_img = 'images/'. $slide;
          ?>      
                <div class="fs_slide" style="background:url('<?php echo $slide_img; ?>') no-repeat; background-position: center -90px; background-size:cover; ">
                </div>

      <?php }
            ?>
          </div>

      </div>
    </div>

  </div> 

<script src="../js/jquery-1.11.0.min.js"></script>
<script src="../js/jquery.cycle.lite.js"></script>
<script>

$jq1 = jQuery.noConflict();

$jq1(function() {

  if($jq1("#home_slider").length) {
    $jq1("#home_slider").cycle({
        timeout: 0,
        fx: 'scrollHorz',
        prev: '#prev',
        next: '#next',
        speed:    400
    });
  }

});

</script>
  


@stop

