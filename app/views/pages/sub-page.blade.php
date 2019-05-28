@extends('layouts.default')
@section('content')

    @if($page->banner)
        <header>
          <div class="bg-ct picture-container" style="background-image: url('{{$DOMAIN}}/files/pages/{{$page->banner->image}}');">
              <h1 class="text-center title-middle">
                @if($page->banner && $page->banner->banner_text)    
                   @foreach($page->banner->banner_text as $t)
                    <?php $size = 's'; if($t->size) { $size = strtolower($t->size); } ?>
                       <span class="text-{{$size}}">{{$t->{'line_'.$lang} }}</span>
                   @endforeach
                @endif    
              </h1>
          </div>  
        </header>                  
    @endif    

    <div class="ce container-fluid">
        <button type="button" class="btn btn-default btn-raised" onclick="history.go(-1)">{{$back_btn_text}}</button>
    </div>    

    @include('pages.page-blocks')

@stop