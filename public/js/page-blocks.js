var is_protected = 0;

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
    // Don't continue unless user has ticked terms of use checkbox
    if(is_protected && !$('#termsofuse_tou').is(':checked')) {
        console.log('Terms of use checkbox not checked');
        return false;
    }

    var list = $('#termsofuse_files').val();
    list = list.split(', ').join(',');
    var items = list.split(',');
    var idStr = '';
    var ar = [];
    for(var i in items) {
        ar = items[i].split('_');
        idStr += ar[0]+',';
    }
    idStr = idStr.substr(0, idStr.lastIndexOf(','));
    var ids = idStr.split(',');
    console.log('DL ids:');console.log(ids);
    console.log(items)
    var isIE11 = !!window.MSInputMethodContext && !!document.documentMode;
    console.log('User browser IE11? '+ isIE11);
    kunsthalle.hideModal('termsofuse');
    if(items.length > 0) {
        $.ajax({
            type: 'POST',
            url: '/handle-downloads',
            data: { 'ids': ids, 'page_id': $('#page_id').val(), 'name': $('#termsOfUseName').val(), 'firm': $('#termsOfUseFirm').val(), 'publication_date':$('#dateOfPublication').val() },
            dataType: 'json',
            success:function(data) { 
                        console.log('handleDownload success...');
                        console.log(data);
                        if(data.item != undefined) {
                            var filename = data.item.substr(data.item.lastIndexOf('/')+1, data.item.length);
                            // window.location.href = data.item;
                            new_tab = (filename.indexOf('.zip') > -1) ? false : true;
                            doDownload(data.file, filename, new_tab);
                            if(data.dl_files != undefined) {
                                var dl_files = data.dl_files;
                                for(var i in dl_files) {
                                    filename = dl_files[i].substr(dl_files[i].lastIndexOf('/')+1, dl_files[i].length);
                                    doDownload(dl_files[i], filename, true);
                                }
                            }
                        }
                        if(data.file != undefined) {
                            if(isIE11) { 
                              window.document.location = data.file; 
                            } else {
                              var filename = data.file.substr(data.file.lastIndexOf('/')+1, data.file.length);
                              console.log(filename + "\n"+ data.file);
                              new_tab = (filename.indexOf('.zip') > -1) ? false : true;
                              doDownload(data.file, filename, new_tab);
                            }
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

function doDownload(url, name, new_tab) {
    var click, save_link, event;
    save_link = document.createElementNS("http://www.w3.org/1999/xhtml", "a")
    if( !("download" in save_link) ) return false; // a[download] not supported on this browser
    console.log('url: '+ url);
    save_link.href = url;
    if(new_tab) { save_link.target = '_blank'; }    
    if(!new_tab) { save_link.download = name; }
    console.log("save_link:\n", save_link);    
    event = document.createEvent("MouseEvents");
    event.initMouseEvent(
        "click", true, false, window, 0, 0, 0, 0, 0
        , false, false, false, false, 0, null
    );
    return save_link.dispatchEvent(event); // false if event was cancelled
}

function getDLPW() {
    var dlpw = prompt('Enter password: ', '');

    return dlpw;
}

function showConfirmation() {
    kunsthalle.showModal('confirm_member_registration');
}
