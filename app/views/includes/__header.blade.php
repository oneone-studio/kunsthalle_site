  <div id="top_row" class="w3-row" style="max-width:100%; sheight:30px;">
    <div class="w3-col l3 m3 w3-left" style="float:left; display:block; margin:15px; min-height:30px;">
        <div class="zhours_info" style="position:relative; top:0px; display:block; min-width:175px;z-index:9999; background:url(../images/clock_black.png) no-repeat;">
          <span style="position:relative; left:27px; min-width:50%; top:-1px; color:#18B321; font-size:12px; font-weight:bold;">Noch 5 stunden offen</span>
        </div>
    </div>

    <div id="top_mid" class="w3-col l6 m6 w3-center">

      <div id="top_bar" style="width:100%; height:350px; display:block; clear:both; margin:0px; position:absolute; background:none; left:0; top:-330px; z-index:9999;">

        <div class="top_menu">
            <div style="width:100%; height:92%; float:right; border-bottom:none;"> 
               <ul class="top_menu_links">
               @foreach($main_menu as $mi)
                  <li><a href="/content-section/{{$mi->id}}">{{$mi->title_de}}</a></li>  
               @endforeach
               <!-- 
                <li><a href="#">Upcomming Events</a></li> 
                <li><a href="#">Exhibitions</a></li>  
                <li><a href="#">Blog</a></li> 
                <li><a href="#">Shop</a></li>  
                -->
               </ul>
             </div>
            <div style="width:100%; height: 30px; margin:0 auto; text-align:center; background:none;vertical-align:bottom; clear:both;"> 
             <div id="menu_slide_btn" class="menu_slide_down" onclick="toggleTopMenu()">&nbsp;</div>
            </div> 
        </div>
      </div>
    </div>
        
    <div class="w3-col l3 m3 w3-right">
    <div id="top_r" style="width:250px; display:block; float:right; margin:15px 15px 15px 0px; background:none; z-index:9999;">
        <div class="zhours_info" style="width:35%; float:right;"><img src="../images/magazin_k.png" style="float:right;position:relative;top:-2px;">&nbsp;</div>

        <div class="zhours_info" style="width:30%; float:right;"><img src="../images/tickets.png" style="float:right;position:relative;top:-2px;">&nbsp;</div>

        <div class="zhours_info" style="width:15%; float:right; position:relative; top:0px; background:url(../images/search.png) no-repeat; background-size:20px 20px;">&nbsp;</div>
    
    </div>    
    </div>
  </div>
<style>
.top-bar-mid {
  position:relative; width:70%; height:30px; margin:10px; float:left; display:block;
}
.top-bar-right {
  width:15%; height:30px; margin:10px; float:left; display:block;
}

@media only screen 
  and (min-device-width: 500px) 
  and (max-device-width: 800px) 
  and (-webkit-min-device-pixel-ratio: 2) {

.top-bar-mid {
  position:relative; width:70%; height:40px; float:left; display:block; clear:both; margin:10px;
}
.top-bar-right {
  width:100%; height:40px; float:left; margin:10px 10px; clear:both; display:block; background:blue;
}

}

</style>  
<script>
/**/
$(window).resize(function() {
  var winW = $(window).width();
  if(winW < 815) {
    $('#top_row').css('height', '120px');
    if(winW < 800) {
       $('#top_r').css('margin-top', '0px').css('margin-left', '-20px').css('float', 'left');   //css('left', '-30px').css('top', '45px');
       $('#top_bar').css('margin-top', '90px');
    } else {
       $('#top_r').css('margin-top', '15px').css('margin-left', '0px').css('float', 'right');  //.css('right', '5px').css('top', '5px').css('float', 'right');
       $('#top_mid').css('height', '73px');
       $('#top_bar').css('margin-top', '0px');
    }
  } else {
    $('#top_row').css('height', '30 px');
  }  
});
/**/
</script>
<!--<div class="navbar">
    <div class="navbar-inner">
        <a id="logo" href="/">Single Malt</a>
        <ul class="nav">
            <li><a href="/">Home</a></li>
            <li><a href="/about">About</a></li>
            <li><a href="/projects">Projects</a></li>
            <li><a href="/contact">Contact</a></li>
        </ul>
    </div>
</div>-->
