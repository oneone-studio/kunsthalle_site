@extends('layouts.default')
@section('content')

    @if($page->banner)
         <header>
            <div class="bg-ct picture-container" style="background-image: url('{{$DOMAIN}}/files/pages/{{$page->banner->image}}');">
                <h1 class="text-center title-{{{ isset($page->banner->text_position) ? $page->banner->text_position : 'middle' }}}">
                @if($page->banner && $page->banner->banner_text)    
                   @foreach($page->banner->banner_text as $t)
                    <?php $size = 's';
                        if($t->size) { $size = strtolower($t->size); }
                    ?>
                       <span class="text-{{$size}}">{{$t->{'line_'.$lang} }}</span>
                   @endforeach
                @endif    
                </h1>
            </div>  
          </header>                  
    @endif    

    @include('pages.page-blocks')  

<script>
// var dl_password = '{{$settings->dl_password}}';

function _handleDownload() {
    if($('#dl_protected').val() == '1') {
        if($('#dl_password').val() != $('#termsOfUsePassword').val()) {
            $('#termsOfUsePassword').val('');
            return false;
        }
    }
    var list = $('#termsofuse_files').val();
    list = list.split(', ').join(',');
    var items = list.split(',');
    console.log(items)
    kunsthalle.hideModal('termsofuse');
    if(items.length > 0) {
        $.ajax({
            type: 'POST',
            url: '/handle-downloads',
            data: { 'ids': items, 'page_id': $('#page_id').val() },
            dataType: 'json',
            success:function(data) { 
                        console.log('handleDownload success..');
                        console.log(data);
                        if(data.item != undefined) {
                            var link = data.item.replace('http://', 'https://');
                            link = link.replace('.net', '.de');
                            console.log('Link: ' + link);
                            $('#zip').html('<iframe width="1" height="1" frameborder="0" src="' + link + '"></iframe>');
                        }
                        return false;
                    },
            error:  function(jqXHR, textStatus, errorThrown) {
                        console.log('handleDownload failed.. ');
                        return false;
                    }
        }); 
    }
    return false;
}

function getDLPW() {
    var dlpw = prompt('Enter password: ', '');

    return dlpw;
}
</script>

<!-- jquery -->
<script src="/bower_components/jquery/dist/jquery.min.js" type="text/javascript"></script>
<script>
function showConfirmation() {
    kunsthalle.showModal('confirm_member_registration');
}

var confirm = false;
@if(isset($action) && $action == 'confirmation')
   confirm = true;    
@endif

$(function() {
    if(confirm) {
        showConfirmation();
    }
});


</script>

@stop