@extends('layouts.default')
@section('content')

    @if($page->banner)
         <header>
            <div class="bg-ct picture-container" style="background-image: url('{{$DOMAIN}}/files/exhibition_pages/{{$page->banner->image}}');">
                <h1 class="text-center title-{{{ isset($page->banner->text_position) ? $page->banner->text_position : 'middle' }}}">
                @if($page->banner && $page->banner->banner_text)    
                   @foreach($page->banner->banner_text as $t)
                    <?php $size = 's'; if($t->size) { $size = strtolower($t->size); } ?>
                       <span class="text-{{$size}}">{{$t->line}}</span>
                   @endforeach
                @endif    
                </h1>
            </div>  
          </header>                  
    @endif    

    <div class="ce container-fluid">
        <button type="button" class="btn btn-default btn-raised" onclick="history.go(-1)">ZURÜCK</button>
    </div>    

    @include('pages.page-blocks')  
<script>

function authenticateDL() {
    var allowDL = false;
    console.log('Verifying password..');
    $.ajax({
        type: 'POST',
        url: '/get-dl-password',
        data: {'pw': $('#termsOfUsePassword').val()},
        async: false,  
        dataType: 'json',
        success:function(data) { 
                console.log('getDLPassword success..');
                var dl_pw = data.dl_password;
                console.log($('#termsOfUsePassword').val() + "\ndata:"); console.log(data);
                if(parseInt(data) == 0) {   
                    $('#termsOfUsePassword').val('');
                    console.log('Wrong PW..');
                    allowDL = false;
                    return false;
                } else {
                    console.log('PW Ok');
                    allowDL = true;
                    return true;
                }
        },
        error:  function(jqXHR, textStatus, errorThrown) {
                console.log('getDLPassword failed.. ');
                allowDL = false;
                return false;
        }
    });
    return allowDL;
}

function handleDownload() {
    var auth = false;
    if(is_protected == 1) {
        auth = authenticateDL();
        console.log('auth: '+ auth);
        if(!auth){    
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
                            $('#zip').html('<iframe width="1" height="1" frameborder="0" src="' + data.item + '"></iframe>');
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

function registerForEvent(id, event) {
    console.log("registerForEvent("+id+") called");
    var $inputs = $('#reg_form_'+id+' :input');
    var frm = document.getElementById('reg_form_'+id);
    var formData = new FormData(frm);
    console.log("Form inputs for event id: "+id);
    var data = {};
    for(var i in $inputs) { 
        console.log("Inp: "+ $inputs[i].name + " : "+ $inputs[i].value); 
        console.log("T: " + typeof $inputs[i]);
        data[$inputs[i].name] = $inputs[i].value;
        if($inputs[i].name == 'newsletter') {
            break;
        }
    }
    data['id'] = id;
    var total = $('.total_price_'+id).html();
    total = total.substr(0, total.indexOf(' '));
    total = total.replace(',', '.', total.replace(' €', ''));
    data['total'] = total;


    var ds = JSON.stringify(data);    
    console.log("JSON -> "); console.log(ds);
    $.ajax({
        type: 'POST',
        url: '/register-for-event',
        data: {ds},
        dataType: 'json',
        success:function(data) { 
                    var modal = kunsthalle.showModal('confirm_event_registration');
                    console.log('registerForEvent success..');
                    console.log(data);
                    return false;
                },
        error:  function(jqXHR, textStatus, errorThrown) {
                    console.log('registerForEvent failed.. ');
                    return false;
                }
    }); 
}
</script>
<style>
.ce-headline p {
  font-family: Georgia, Times, Times New Roman, serif;
  font-size: 24.5px; 
  line-height:24px;
}
.ce-headline p {
  font-family: Georgia, Times, Times New Roman, serif;
  font-size: 20px; }
p a { text-decoration: underline; }
</style>

@stop