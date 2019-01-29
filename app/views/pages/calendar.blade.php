@extends('layouts.default')
@section('content')
             @include('pages.calendar-section')   
    <!-- content ende -->

<script type="text/javascript">
var cnt = 0;
function clickFilter() {
    ++cnt;
    if(cnt > 10) {
        console.log('click..');
        $('.date-selector-wrapper').collapse('show');
        return 1;
    }
    setTimeout('clickFilter()', 50);
}

window.onload = function() {
    var url = document.URL;
    console.log(url);
    var isDirect = false;
    if(url.indexOf('return_url=') == -1) {
        var arr = url.split('/');
        for(var i in arr) {
            if(arr[i].length == 0) { arr.splice(i, 1); }
        }
        console.log(arr);
        var indx_str = arr[arr.length-1];
        console.log(indx_str);
        var indx = indx_str;
        slideNo = 1;
        if(indx_str.indexOf('_') > -1) {
            var indx_ar = indx_str.split('_');
            indx = indx_ar[0];
            slideNo = indx_ar[1];
            if(!isNaN(indx) && !isNaN(slideNo)) {
                isDirect = true;
            }
        }
    }
    if(!isDirect) {
        clickFilter();
    }    
}

</script>

@stop