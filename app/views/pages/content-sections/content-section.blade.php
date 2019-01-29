@extends('layouts.default')
  <script>
  </script>
@section('content')
  
  <div style="width:70%; display:block; margin:10px auto;">
  <h3>{{$menu_item->title_de}}</h3>

  <div styel="width:100%; position:relative; clear:both; padding:0; margin:10px auto;border:1px solid red;">
      @if(count($pages))
        @foreach($pages as $page)
          <div style="width:160px; height:240px; float:left; margin-left:150px; padding:0;">
           <div style="width:100%; height:200px; float:left; background:url('http://kunsthalle-cms.dev/files/pages/{{$page->page_image}}') no-repeat; text-align:center;">
           </div>
           <div style="width:100%; height:30px; padding:5px;">
              <h5 style="color:orangered;">{{$page->title_de}}</h5>
           </div>
          </div>
        @endforeach 
      @endif
  </div>
</div>

@stop