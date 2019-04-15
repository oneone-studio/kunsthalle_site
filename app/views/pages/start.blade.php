@extends('layouts.start-layout')
@section('content')
    @if($slider)
    
        <?php 
            $vid_index = count($slides)-1;
            // echo '<pre>'; print_r($slides);exit;
        ?>    
        <header>
            <div class="swiper-container">
                <div class="swiper-wrapper">
                @foreach($slides as $slide)              
                    <div class="swiper-slide">
                            <div class="bg-ct picture-container" style="background-image: url('{{$DOMAIN}}/files/sliders/{{$slide->filename}}');">
                                <h1 class="text-center title-{{$slide->text_position}}">
                                 <a href="{{$slide->url}}" title="" style="display: block;">
                                    @if($slide->slide_text)    
                                       @foreach($slide->slide_text as $t)
                                        <?php $size = 's';
                                            if($t->size) { $size = strtolower($t->size); }
                                        ?>
                                           <span class="text-{{$size}}">{{$t->{'line_'.$lang} }}</span>
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
<script>
var indx = 0;
var vid_index = 4;
var max = Number('{{count($slider->page_slider_images)}}') + 1;
var swiper = null;

var swiper = null;

function setSwiper() {
    if(swiper == null) {
        swiper = new Swiper('#content>header>.swiper-container', {
            nextButton: '.next',
            prevButton: '.prev',
            autoplay: 12000,
            loop: true
        });
    }
}

// alert(max);
// $(function() {
//     $('.next').click(function() { 
//         if(indx < (max-1)) { ++indx; } else { indx = 0; }         
//         console.log('Next -> indx: '+indx + ' - vid_index: '+vid_index); 
//         doProc(); 
//     });
//     $('.prev').click(function() { 
//         if(indx > 0) { --indx; } else { indx = (max-1); }
//         console.log('Prev -> indx: '+indx + ' - vid_index: '+vid_index);
//         doProc(); 
//     });

//     var v = document.getElementById('myvid');
//     v.controls = false;
//     v.autoplay = false;  
//     v.loop = true;
// });

function doProc() {
    // setSwiper();
    var v = document.getElementById('myvid');
    console.log("Slide check: "+ ($('.swiper-slide-active').attr('data-swiper-slide-index')) + "\nPlay ? "+ ($('.swiper-slide-active').hasClass('video-slide')));
    var play = $('.swiper-slide-active').hasClass('video-slide') ? true : false;
    if(indx == vid_index) {
        console.log('Playing >>');
        v.play();
    } else {
        console.log('Paused ||');
        v.pause();
    }
}
</script>
@stop