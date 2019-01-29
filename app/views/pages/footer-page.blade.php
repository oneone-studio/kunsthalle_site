@extends('layouts.default')
@section('content')
<style>
a { text-decoration: underline; }
a:visited { text-decoration: underline; }
</style>
<script>
$(function() {
	$('a').click(function() {
		this.blur();
	});
});
</script>
  <div class="ce ce-headline container">
  		<h2 class="anchor">{{$page->title_de}}</h2>
  		<p>{{$page->page_contents[0]->content_de}}</p>
  </div>

@stop