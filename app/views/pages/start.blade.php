@extends('layouts.start-layout')
@section('content')
    @if($slider)
        <header>
            <div class="swiper-container">
                <div class="swiper-wrapper">
                @foreach($slider->page_slider_images as $slide)
                    <div class="swiper-slide">
                        <div class="bg-ct picture-container" style="background-image: url('{{$DOMAIN}}/files/sliders/{{$slide->filename}}');">
                            <h1 class="text-center title-{{$slide->text_position}}">
                             <a href="{{$slide->url}}" title="" style="display: block;">
                                @if($slide->slide_text)    
                                   @foreach($slide->slide_text as $t)
                                    <?php $size = 's';
                                        if($t->size) { $size = strtolower($t->size); }
                                        $line = ($lang == 'en') ? $t->line_en : $t->line_de;
                                    ?>
                                       <span class="text-{{$size}}">{{ $line }}</span>
                                   @endforeach
                                @endif    
                             </a>
                            </h1>
                        </div>
                    </div>
                @endforeach                    
                </div>
                <div class="next"><span class="icon icon-right icon-white icon-l" /></div>
                <div class="prev"><span class="icon icon-left icon-white icon-l" /></div>
            </div>
        </header>
    @endif    

<div id="modals">
    @include('includes.search_modal')
</div>

@stop