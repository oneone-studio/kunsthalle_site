@extends('layouts.default')
@section('content')
<script>
$(function() {
	$('a').click(function() {
		this.blur();
	});
});
</script>
  <div class="ce ce-headline container">
  		<h2 class="anchor"><?php echo $page->{'title_'.$lang}; ?></h2>
  		<p><?php echo $page->page_contents[0]->{'content_'.$lang}; ?></p>
  </div>
@stop