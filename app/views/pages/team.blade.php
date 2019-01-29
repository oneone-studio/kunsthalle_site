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
							@if(strlen($c->function) > 0)
								<div class="ce ce-contact container-small">
									<h4>{{$c->function}}:</h4>
									<div>
										<a href="javascript:showContactForm('{{$c->id}}')">
											<span class="icon icon-mail icon-m"></span> 
											{{$c->title}} {{$c->first_name}} {{$c->last_name}}
										</a>
									</div>
									@if(isset($c->phone) && !empty(trim($c->phone)))
									<div>
										<a href="tel:#">
											<span class="icon icon-phone icon-m"></span> 
											<a href="tel:{{$c->phone}}">{{$c->phone}}</a>
										</a>
									</div>
									@endif
								</div>
							@endif
						@endforeach	
					</div>
				</li>
			  @endforeach	
			@endif
		</ul>
	</div>
	<!-- team end -->

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
								<label for="emailrequestEmail" class="control-label">Ihre E-Mail</label>
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
						<h4>Vielen Dank für Ihre Nachricht</h4>
						<p>Wir werden Ihre E-Mail so schnell wie möglich beantworten.</p>
						<a href="javascript:kunsthalle.hideModal()" class="btn btn-default btn-raised active">OK</a>
					</div>
				</div>
			</div>
		</div>
		@include('includes.search_modal')
	</div>

<script src="/bower_components/jquery/dist/jquery.min.js" type="text/javascript"></script>
<script>
function showContactForm(contact_id) {
	$('#contact_id').val(contact_id);
	kunsthalle.showModal('email_request');
}

function validateEmail(email) {
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email) == false)
    {
    	$('#emailrequestEmail').focus().select();
        if($('#emailrequestEmail-error').length) {
           $('#emailrequestEmail-error').show(); 
        }
        return false;
    }
    
    $('#emailrequestEmail-error').hide();
    return true;
}

function sendMessage() {
    // var formData = new FormData($('#contact_form')[0]);
    console.log('sendMessage() called'+"\ncontact id: "+$('#contact_id').val()+"\nname: "+$('#emailrequestName').val()+"\nemail: "+
        $('#emailrequestEmail').val()+"\ncomment: "+ $('#emailrequestComment').val());
    var email = $('#emailrequestEmail').val();
    if(validateEmail(email))
    {
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
}

function showConfirmation() {
    kunsthalle.showModal('confirm_email_request');
}
</script>
@stop
