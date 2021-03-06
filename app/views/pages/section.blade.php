@extends('layouts.default')
@section('content')
        <div class="ce ce-submenu container-fluid">
            <div class="ce-submenu-title text-center">
                <?php echo $section->{'title_'.$lang}; ?>
            </div>
            <div class="opener">
                <a href="#" class="opener-close-link">
                    <span class="icon icon-up icon-red"></span>
                </a>
                <a href="#" class="opener-open-link">
                    <span class="icon icon-down icon-red"></span>
                </a>
            </div>
            <ul>
        @foreach($pg_links as $pl)
            <?php $link = '/'.$lang.'/'.$menu_item.'/'.$pl->link;
            $has_calendar = false;
            $is_calendar = false;
            if(strtolower($pl->{'title_'.$lang}) == 'calendar' || strtolower($pl->{'title_'.$lang}) == 'kalender') {  
                $is_calendar = true; 
                if(count($calendar) > 0) { $has_calendar = true; }
            }
            ?>
                <li><a href="{{$link}}" class="btn btn-default btn-raised @if($pl->current_link) active @endif"><?php echo $pl->{'title_'.$lang}; ?></a></li>
        @endforeach    
                <li><a href="#" class="btn btn-default btn-raised close-link"><span class="icon icon-up icon-white"></span></a></li>
	        </ul>
	    </div>
		@if($section->{'headline_'.$lang})
			<div class="ce ce-text container">
				<h2><?php echo $section->{'headline_'.$lang}; ?></h2>
				@if($section->{'detail_'.$lang})
					<p><?php echo $section->{'detail_'.$lang}; ?></p>
				@endif
			</div>
		@endif
		
		@if($section->contacts && count($section->contacts))
			<div class="ce ce-contact container">
				<h4>{{$contact_hdr_text}}</h4>
				@foreach($section->contacts as $c)
					<div>
						<a href="javascript:showContactForm('{{$c->id}}')">
							<span class="icon icon-mail icon-m"></span> 
							{{$c->first_name .' '. $c->last_name}}
						</a>
					</div>
					<div>
						<a href="tel:{{$c->phone}}">
							<span class="icon icon-phone icon-m"></span> 
							{{$c->phone}}
						</a>
					</div>
				@endforeach	
			</div>
		@endif
			
		<div class="ce ce-router">
			@if(isset($showFliters) && $showFliters == true)
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
								<li onclick="showAll()"><span class="icon icon-inline icon-check"></span> <a href="#" data-filter="*">Alle</a></li>
					              @if($tags)
					                 @foreach($tags as $tag)
					                    @if(in_array($tag->id, $tag_ids))
						                    <li onclick="filterItems({{$tag->id}})"><span class="icon icon-inline"></span> <a href="#" 
						                    		data-filter=".filter-guidances"><?php echo $tag->{'tag_'.$lang}; ?></a></li>
					                    @endif
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
			@endif	

			@if(strtolower($section->teaser_size) == 'l')	
				<div class="ce ce-router ce-router-fullwidth-3col">
					<div class="container-fluid">
						<div class="grid">
							<div class="grid-sizer"></div>
						@foreach($pages as $p)
						  @if($p->teaser && $p->teaser->filename)
						  <?php $tag_classes = ' tagged';
						  	foreach($p->tags as $tag) { $tag_classes .= ' tag-'. $tag->id; }
						  ?>
							<article class="grid-item {{$tag_classes}}">
							   <?php 
							     $link = '/'.$lang.'/sb-page/'.$menu_item.'/'.$section_title.'/'.$p->{'slug_'.$lang}; 
							   ?>
							   <a href="{{$link}}">
								<img src="{{$DOMAIN}}/files/teasers/{{$p->teaser->filename}}" alt="" class="img-responsive">
								</a>
								<header>
									<div>
										<h2>{{$p->teaser->{'caption_'.$lang} }}</h2>
										<div>
											@if($p->teaser->{'line_1_'.$lang} && strlen($p->teaser->{'line_1_'.$lang} ) > 0) 
												{{$p->teaser->{'line_1_'.$lang} }} 
											@endif
											@if($p->teaser->{'line_2_'.$lang} && strlen($p->teaser->{'line_2_'.$lang} ) > 0)
												<br />{{$p->teaser->{'line_2_'.$lang} }}
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
			@else
		
			<div class="container-fluid">
				<div class="grid">
					<div class="grid-sizer"></div>

					@foreach($pages as $p)
					  @if($p->teaser && $p->teaser->filename)
						  <?php $tag_classes = ' tagged';
						  	foreach($p->tags as $tag) { $tag_classes .= ' tag-'. $tag->id; }
							   $slug = (isset($p->{'slug_'.$lang}) && trim($p->{'slug_'.$lang}) != '') ? $p->{'slug_'.$lang} : strtolower(str_replace(' ', '-', $p->{'title_'.$lang}));
							   $link = '/'.$lang.'/sb-page/'.$menu_item.'/'.$section_title.'/'.$slug;
						  ?>
						  @if($p->is_main_teaser)					  
						
							<article class="grid-item grid-item--width2 filter-guidances filter-members filter-youngsters filter-holiday-courses filter-seminars filter-movies filter-lectures {{$tag_classes}}">
							  <a href="{{$link}}">
								<img src="{{$DOMAIN}}/files/teasers/{{$p->teaser->filename}}" alt="" class="img-responsive">
								<header>{{$p->teaser->{'caption_'.$lang} }}</header></a>
							</article>						

						  @else	

							<article class="grid-item filter-guidances {{$tag_classes}}">
							  <a href="{{$link}}">
								<img src="{{$DOMAIN}}/files/teasers/{{$p->teaser->filename}}" alt="" class="img-responsive">
								<header>{{$p->teaser->{'caption_'.$lang} }}</header></a>
							</article>
						
						  @endif	
					  @endif
					@endforeach	
				</div>
			</div>
		</div>
	</section>

		@endif	

		</div>
	</section>


    <div id="modals">
        <div id="modal_email_request">
            <div class="container">
                <div class="header">
                    <div class="text-center">
                        <a href="#"><img class="logo" src="/images/kunsthalle_bremen_logo_w.svg" alt="Kunsthalle Bremen" title="Kunsthalle Bremen" /></a>
                    </div>
                    <a href="javascript:kunsthalle.hideModal()" class="pull-right"><span class="icon icon-close icon-white"></span></a>
                </div>
                <div class="content">
                    <div class="ce-emailrequest">
                        <h4>Ihre E-Mail an die Kunsthalle Bremen:</h4>
                        <form id="contact_form" method="POST">
                            <div class="form-group label-placeholder">
                                <label for="emailrequestEmail" class="control-label">Ihre E-Mail-Adresse</label>
                                <input type="email" class="form-control" name="email" id="emailrequestEmail" required />
                            </div>
                            <div class="form-group label-placeholder">
                                <label for="emailrequestName" class="control-label">Ihr Name, Vorname</label>
                                <input type="text" class="form-control" name="name" id="emailrequestName" required>
                            </div>
                            <div class="form-group label-placeholder">
                                <label for="emailrequestComment" class="control-label">Ihre Nachricht</label>
                                <textarea class="form-control" name="comment" id="emailrequestComment" rows="5" required></textarea>
                            </div>
                            <div class="mt-30">
                                <button type="button" class="btn btn-default btn-raised active" onclick="sendMessage()">Jetzt abschicken</button>
                            </div>
                            <input name="receiver_email" id="receiver_email" type="hidden">
                            <input name="contact_id" id="contact_id" type="hidden">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="modal_confirm_email_request">
            <div class="container">
                <div class="header">
                    <div class="text-center">
                        <a href="#"><img class="logo" src="/images/kunsthalle_bremen_logo_w.svg" alt="Kunsthalle Bremen" title="Kunsthalle Bremen" /></a>
                    </div>
                    <a href="javascript:kunsthalle.hideModal()" class="pull-right"><span class="icon icon-close icon-white"></span></a>
                </div>
                <div class="content">
                    <div class="ce-confirmation">
						<h4>VIELEN DANK FÜR IHRE NACHRICHT</h4>
						<p>Wir werden Ihre E-Mail so schnell wie möglich beantworten.</p>
                        <a href="javascript:kunsthalle.hideModal()" class="btn btn-default btn-raised active">OK</a>
                    </div>
                </div>
            </div>
        </div>
        @include('includes.search_modal')
    </div>

	@include('includes.sidemenu')

	<!-- content ende -->
<script src="/bower_components/jquery/dist/jquery.min.js" type="text/javascript"></script>
<script>
function showContactForm(contact_id) {
    $('#contact_id').val(contact_id);
    kunsthalle.showModal('email_request');
}

function showConfirmation() {
    kunsthalle.showModal('confirm_email_request');
}

function sendMessage() {
    console.log('sendMessage() called'+"\ncontact id: "+$('#contact_id').val()+"\nname: "+$('#emailrequestName').val()+"\nemail: "+
        $('#emailrequestEmail').val()+"\ncomment: "+ $('#emailrequestComment').val());
    $.ajax({
        type: 'POST',
        url: '/send-message',
        data: { 'contact_id': $('#contact_id').val(), 'name': $('#emailrequestName').val(), 'email': $('#emailrequestEmail').val(), 'comment': $('#emailrequestComment').val() },
        dataType: 'json',
        success:function(data) { 
                    console.log('sendMessage success..');
                    console.log(data);
                    kunsthalle.hideModal('email_request');
                    showConfirmation();
                },
        error:  function(jqXHR, textStatus, errorThrown) {
                    console.log('sendMessage failed.. ');
                    return false;
                }
    });     
}

function filterItems(id) {
	$('.tagged').hide();
	$('.tag-'+id).show();
}

function showAll() {
	$('.tagged').show();
}

</script>	
@stop