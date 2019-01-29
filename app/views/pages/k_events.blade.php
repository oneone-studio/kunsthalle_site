@extends('layouts.default')
  <script>
  var showMenu = true;
  function toggleTopMenu() {
  	  if(showMenu) {
  	  	  $('.page_content').animate({top: '-30px' });
  	  	  $('#menu_slide_btn').removeClass('menu_slide_down').addClass('menu_slide_up');
  	  	  showMenu = false;
  	  }	else {
  	  	  $('.page_content').animate({top: '-290px' });
  	  	  $('#menu_slide_btn').removeClass('menu_slide_up').addClass('menu_slide_down');
  	  	  showMenu = true;
  	  }
  	  $('#menu_slide_btn').blur();
  }
  </script>

@section('content')

  <div class="hours_info">
  	<img src="images/clock_black.png" style="width:20px; height:20px; background:none;">
  	<span style="padding-left:10px; color:green; font-size:12px; font-weight:bold;">Noch 5 stunden offen</span>
  </div>
  <div class="page_content">

	  <div class="top_menu">
	  	   <ul class="top_menu_links">
	  	   	  <li><a href="#">Upcomming Events</a></li>	
	  	   	  <li><a href="#">Exhibitions</a></li>	
	  	   	  <li><a href="#">Blog</a></li>	
	  	   	  <li><a href="#">Shop</a></li>	
	  	   </ul>


	  	 <button id="menu_slide_btn" class="menu_slide_down" onclick="toggleTopMenu()"></button>
	  </div>

	  <div class="page_text">
		  <h1>Events</h1>
		  <p>This is some text for testing. This is some text for testing. This is some text for testing. This is some text for testing. This is some text for testing. This is some text for testing. This is some text for testing. This is some text for testing. This is some text for testing. This is some text for testing. This is some text for testing. This is some text for testing. <br><br>
		  This is some text for testing. This is some text for testing. This is some text for testing. This is some text for testing. This is some text for testing. </p> 
	  </div>
  </div>
@stop