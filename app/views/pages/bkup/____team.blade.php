@extends('layouts.default')
@section('content')
    <div class="ce ce-menu container-fluid">
        <ul class="list-inline">
        @foreach($pg_links as $pl)
            <?php $link = "/".$menu_item.'/'.$pl->link;
            $has_calendar = false;
            $is_calendar = false;
            if(strtolower($pl->title_en) == 'calendar' || strtolower($pl->title_de) == 'kalender') {  $link = '/'.$pl->link .'/'. $menu_item; $is_calendar = true; 
                if(count($calendar) > 0) { $has_calendar = true; }
            }
            ?>
                <li><a href="{{$link}}" class="btn btn-default btn-raised @if($pl->current_link) active @endif">{{$pl->title_de}}</a></li>
        @endforeach    
        </ul>
    </div>
	<!-- team start -->
	<div class="ce ce-team container container-nogap-sm">
		<h3 class="anchor">Das Team der Kunsthalle Bremen</h3>
		<ul class="list-unstyled">
			@if($contacts && count($contacts))
			  @foreach($contacts as $dep => $cts)
				<li class="ce-team-item-open">
					<h3>
						<a href="#{{strtolower(str_replace(' ', '-', $dep))}}" data-toggle="collapse" class="collapsed">
							{{$dep}} <span class="icon icon-inline icon-collapse pull-right"></span>
						</a>
					</h3>
					<div id="{{strtolower(str_replace(' ', '-', $dep))}}" class="collapse">
					    @foreach($cts as $c)							
							<div class="ce ce-contact container-small">
								@if(strlen($c->function) > 0)
									<h4>{{$c->function}}:</h4>
									<div>
										<a href="javascript:showEmailForm($c->email)">
											<span class="icon icon-mail icon-m"></span> 
											{{$c->title}} {{$c->first_name}} {{$c->last_name}}
										</a>
									</div>
									<div>
										<a href="tel:#">
											<span class="icon icon-phone icon-m"></span> 
											<a href="tel:{{$c->phone}}">{{$c->phone}}</a>
										</a>
									</div>
								@endif
							</div>
						@endforeach	
					</div>
				</li>
			  @endforeach	
			@endif
		</ul>
	</div>
	<!-- team end -->

@stop
