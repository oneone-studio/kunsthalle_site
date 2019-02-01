@extends('layouts.default')
@section('content')

	    <div class="ce ce-menu container-fluid">
	        <ul class="list-inline">
	            <li><a href="/{{$lang}}/view/exhibitions/list/current" class="btn btn-default btn-raised @if($category && $category == 'current') active @endif">Aktuelle Ausstellungen</a></li>
	            <li><a href="/{{$lang}}/view/exhibitions/list/upcoming" class="btn btn-default btn-raised @if($category && $category == 'upcoming') active @endif">Kommende Ausstellungen</a></li>
	            <li><a href="/{{$lang}}/view/exhibitions/list/past" class="btn btn-default btn-raised @if($category && $category == 'past') active @endif">Vergangene Ausstellungen</a></li>
	        </ul>
	    </div>


		@if(isset($use_main_exb) && $use_main_exb == true && isset($main_exb))
			<div class="ce ce-teaser">
				<div class="container-fluid">
					<div class="teaser-wrapper pa-10 mb-30">
						<div class="teaser-item">
							<a href="/{{$lang}}/view/exhibitions/exb-page/{{ strtolower(str_replace(' ', '-', $main_exb->title_en)) }}">
							<img src="{{$DOMAIN}}/files/teasers/{{$main_exb->teaser->filename}}" alt="" class="img-responsive"></a>
						</div>
						<div class="teaser-item text pa-15">
							<h1>
	                            @if($main_exb->teaser)    
	                                @if(isset($main_exb->teaser->caption) && strlen($main_exb->teaser->caption) > 0)
										<span class="text-xl">{{$main_exb->teaser->caption}}</span>
									@endif
	                                @if(isset($main_exb->teaser->line_1) && strlen($main_exb->teaser->line_1) > 0)
										<span class="text-l">{{$main_exb->teaser->line_1}}</span>
									@endif
	                                @if(isset($main_exb->teaser->line_2) && strlen($main_exb->teaser->line_2) > 0)
										<br><span class="text-s">{{$main_exb->teaser->line_2}}</span>
									@endif
	                            @endif    
							</h1>
						</div>
					</div>
				</div>	
			</div>	
		@endif	

		<div class="ce ce-router ce-router-fullwidth-3col">
			<!--- ->
			<div class="filter">
				<div class="container-fluid mb-15">
					<a href="#" class="open-filter">
						<span class="icon icon-filter icon-black"></span>
						Filter
						<span class="icon icon-arrow icon-s icon-inline"></span>
						<span class="filter-name">Alle</span>
					</a>
				</div>
				<div class="menu panel-collapse collapse">
					<div>
						<div class="triangle"></div>
						<ul class="list-unstyled">
							<li><span class="icon icon-inline icon-check"></span> <a href="#" data-filter="*">Alle</a></li>
				              @if($tags)
				                 @foreach($tags as $tag)
				                    <li><span class="icon icon-inline"></span> <a href="/exhibitions/list/current/{{strtolower(str_replace(' ', '-', $tag->tag_de))}}" 
				                    		data-filter=".filter-guidances">{{$tag->tag_de}}</a></li>
				                 @endforeach
				              @endif							
						</ul>
						<div class="text-center">
							<a href="#" class="close-filter">
								<span class="icon icon-close icon-red"></span>
							</a>
						</div>
					</div>
				</div>
			</div>
			<!---->

			<div class="container-fluid">
				<div class="grid">
					<div class="grid-sizer"></div>
					@foreach($pages as $p)
					  @if($p->teaser && $p->teaser->filename && $p->is_main_teaser == 0)
						  <?php $tag_classes = ' tagged';
						  	foreach($p->tags as $tag) { $tag_classes .= ' tag-'. $tag->id; }
						  ?>
						 <article class="grid-item {{$tag_classes}}">
						   <a href="/{{$lang}}/view/exhibitions/exb-page/{{ strtolower(str_replace(' ', '-', $p->title_en)) }}">
							<img src="{{$DOMAIN}}/files/teasers/{{$p->teaser->filename}}" alt="" class="img-responsive"></a>
							<header>
								<div>
									<h2>{{$p->teaser->caption}}</h2>
									<div>
										@if($p->teaser->line_1 && strlen($p->teaser->line_1) > 0) 
										   {{$p->teaser->line_1}} 
										@endif
										@if($p->teaser->line_2 && strlen($p->teaser->line_2) > 0)
											<br />{{$p->teaser->line_2}}
										@endif
									</div>
								</div>
							</header>
						</article>
					  @endif
					@endforeach	
				</div>
			</div>
		</div>
	</section>
	<!-- content ende -->

    <div id="modals">@include('includes.search_modal')</div>
@stop