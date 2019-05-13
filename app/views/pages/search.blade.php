@extends('layouts.default')
@section('content')

<div class="ce ce-searchresult container">
	<a href="/"><span class="icon icon-arrow-left icon-inline icon-s"></span>Home</a>
	<div style="display:inline-block; margin-left:20px;font-weight:normal;"><a href="javascript:kunsthalle.showModal('search');void(0);">Neue Suche</a></div>
	@if($results)
		<h4>Es wurde(n) {{count($results)}} Ergebnis(se) zu <strong>{{$search_term}}</strong> gefunden: </h4>

		<ul class="list-unstyled">
			@foreach($results as $res)	
				    @if(isset($res['page_type']) && isset($res['url']))
				        @if($res['page_type'] != 'event') 
							<li><a href="{{$res['url']}}">{{$res['page_title_de']}} 
						        @if($res['page_type'] != 'exhibition')
									<small>
									  @if(isset($res['menu_title_de'])) {{$res['menu_title_de']}} @endif
									  @if($res['page_type'] == 'page_section' && isset($res['cs_item_slug'])) > {{$res['cs_item_slug']}} @endif
									</small>
								@else
									<small>Ausstellungen</small>	
								@endif	
							</a></li>
						@endif
						@if($res['page_type'] == 'event')
							<li><a href="{{$res['url']}}">{{$res['page_title_de']}} 
								<small>Veranstaltung > {{$res['date_info']}}</small>
							</a></li>
						@endif
					@endif
			@endforeach	
		</ul>
	@else	
		<h4>Zu Ihrem Suchbegriff <strong>{{$search_term}}</strong> wurden leider keine Ergebnisse gefunden.</h4>
	@endif	

	<div id="modals">
		@include('includes.search_modal')
	</div>		
</div>
@stop