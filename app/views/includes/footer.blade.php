<?php $version = '201904091031'; ?>
    <ul id="footermenu" class="list-unstyled">
       @foreach($ftr_links as $href => $title)      
          <li><a href="/{{$lang}}/view/static/page/{{$href}}" title="#">{{$title}}</a></li>
       @endforeach   
       <!-- <li><a href="javascript:gaOptout()">Click here to opt-out of Google Analytics</a></li> -->
    </ul>
    <ul class="list-inline">
        <li>
            <a href="http://www.facebook.com/KunsthalleBremen" title="#" target="_blank">
                <span class="icon icon-facebook icon-l"></span>
            </a>
        </li>
        <li>
            <a href="https://twitter.com/Kunsthalle_HB" title="#" target="_blank">
                <span class="icon icon-twitter icon-l"></span>
            </a>
        </li>
        <li>
            <a href="https://www.instagram.com/Kunsthalle.Bremen/" title="" target="_blank">
                <span class="icon icon-instagram icon-l"></span>
            </a>
        </li>
        <li>
            <a href="http://www.youtube.com/KunsthalleBremen" title="#" target="_blank">
                <span class="icon icon-youtube icon-l"></span>
            </a>
        </li>
        <li>
            <a href="https://www.voicerepublic.com/users/kunsthalle-bremen" title="Voice Republic" target="_blank">
                <span class="icon icon-voice-republic icon-l"></span>
            </a>
        </li>
        <li>
            <a href="https://artsandculture.google.com/partner/kunsthalle-bremen" title="" target="_blank">
                <span class="icon icon-google-arts icon-l"></span>
            </a>
        </li>
    </ul>
    <div class="container">
        <div class="row">
            <div class="col-sm-offset-2 col-sm-8 col-md-offset-3 col-md-6">
                <form id="newsletter_form" action="//kunsthalle-bremen.us15.list-manage.com/subscribe/post?u=d96402a89ce6a567920593b13&amp;id=014f5dc121" method="post" name="mc-embedded-subscribe-form"><!--  onsubmit="return subscribeForNewsletter(event)" -->
                    <p>Regelmäßig über Ausstellungen und Aktivitäten der Kunsthalle informiert werden - abonnieren Sie unseren Newsletter:</p>
                    <div class="form-group label-placeholder">
                      <label for="mce-EMAIL">Bitte hier E-Mail-Adresse eintragen </label>
                      <input type="email" value="" name="EMAIL" class="form-control" id="mce_EMAIL" required>
                    </div>
                    <div style="position: absolute; left: -5000px;" aria-hidden="true">
                      <input type="text" name="b_d96402a89ce6a567920593b13_014f5dc121" tabindex="-1" value="">
                    </div>
                    <div class="clear">
                      <button type="submit" class="btn btn-link">OK</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- footer ende -->
<!-- jquery -->
    <!-- jquery -->
    <!-- <script src="/bower_components/jquery/dist/jquery.min.js" type="text/javascript"></script> -->
    <script src="/bower_components/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <!-- bootstrap -->
    <script src="/bower_components/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- material-design -->
    <script src="/bower_components/bootstrap-material-design/dist/js/material.min.js" type="text/javascript"></script>
    <script src="/bower_components/bootstrap-material-design/dist/js/ripples.min.js" type="text/javascript"></script>
    <!-- swiper -->
    <script src="/bower_components/swiper/dist/js/swiper.jquery.min.js" type="text/javascript"></script>
    <!-- validate -->
    <script src="/bower_components/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
    <script src="/bower_components/jquery-validation/dist/additional-methods.min.js" type="text/javascript"></script>
    <!-- isotope -->
    <script src="/bower_components/isotope/dist/isotope.pkgd.min.js" type="text/javascript"></script>
    <script src="/bower_components/isotope-packery/packery-mode.pkgd.min.js" type="text/javascript"></script>
    <!-- imagesloaded -->
    <script src="/bower_components/imagesloaded/imagesloaded.pkgd.min.js" type="text/javascript"></script>
    <!-- german validation messages -->
    <script src="/js/messages_de.js" type="text/javascript"></script>
    <!-- custom -->
    <script src="/js/main.js?v={{$version}}" type="text/javascript"></script>
    <script src="/js/page-blocks.js?v={{$version}}" type="text/javascript"></script>

<script type="text/javascript">
function subscribeForNewsletter(evt) {
    evt.preventDefault();
    var frm = document.getElementById('newsletter_form');
    var formData = new FormData(frm);
    // var re = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;    
    // var res = re.test($('#mce_EMAIL').val());    
    var email = $('#mce_EMAIL').val();
    var err = false;
    if(email.indexOf('@') == -1 || email.indexOf('.') == -1) {
        err = true;
    }
    if(!err) {
        console.log("Email validation: "+ res);
        if(res) {
            $.ajax({
                type: 'POST',
                url: "//kunsthalle-bremen.us15.list-manage.com/subscribe/post?u=d96402a89ce6a567920593b13&amp;id=014f5dc121",
                data: { 'email': $('mce_EMAIL').val(), 'name': 'b_d96402a89ce6a567920593b13_014f5dc121', 'subscribe': 'subscribe' }, // formData,
                dataType: 'json',
                success:function(data) { 
                            console.log('subscribeForNewsletter success..'); console.log(data);
                        },
                error:  function(jqXHR, textStatus, errorThrown) {
                            console.log("subscribeForNewsletter failed\n\n"+ errorThrown);
                        }
            });
        }
    }
}

$(function() {
    if($('a[title="Klicken Sie hier"]').length) {
        $('a[title="Klicken Sie hier"]').attr('href', 'javascript:gaOptout()');
    }
    if($('.optout_lnk').length) {
        $('.optout_lnk').attr('href', 'javascript:gaOptout()');
    }
});

</script>
@include('includes.sidemenu')