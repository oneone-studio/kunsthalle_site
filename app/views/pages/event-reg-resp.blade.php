@extends('layouts.default')
@section('content')
<!-- Modal -->
 <div id="modals">
    <div id="modal_confirm_event_registration">
        <div class="container">
            <div class="header">
                <div class="text-center">
                    <a href="#"><img class="logo" src="/images/kunsthalle_bremen_logo_w.svg" alt="Kunsthalle Bremen" title="Kunsthalle Bremen" /></a>
                </div>
                <a href="javascript:doRedirect();" class="pull-right"><span class="icon icon-close icon-white"></span></a>
            </div>
            <div class="content">
                <div class="ce-confirmation">
                    <h4>Vielen Dank für Ihre Anmeldung</h4>
                    <p>Vielen Dank für Ihre Anmeldung, die wir schnellstmöglich bearbeiten werden.
                    Eine Bestätigung Ihrer Teilnahme erhalten Sie nach Prüfung Ihrer Anmeldung.</p>
                    <p>Ihre Kunsthalle Bremen</p>
                    <a href="/{{$lang}}{{$calendar_uri}}" class="btn btn-default btn-raised active">OK</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/bower_components/jquery/dist/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
function showConfirmation() {
    kunsthalle.showModal('confirm_event_registration');
}

var confirm = false;
var return_url = '';
@if(isset($return_url))
   return_url = '{{$return_url}}'    
@endif

@if(isset($action) && ($action == 'confirmation' || $action == 'bestaetigung'))
   confirm = true;    
@endif

var url = document.URL;
if(url.indexOf('/bestaetigung') > -1 || url.indexOf('/confirmation') > -1) {
    confirm = true;
}
console.log(return_url);
var arr = return_url.split('_');
console.log(arr);

function doRedirect() {
    console.log('Return: '+ return_url);
    if(return_url.length > 0) {
        var arr = return_url.split('_');
        console.log(arr);
        location.href = return_url;
    }
}

$(function() {
    showCfmMsg('confirm_event_registration');
});
</script>
@stop