@extends('layouts.login')
@section('content')

    <div class="page_contentz form" style="width:570px; margin:0px auto;">
      
      <h3>Kunsthalle Bremen Access</h3>
	  
	  {{ Form::open(array('route' => 'user.authenticate', 'method' => 'post', 'class' => 'form-inline')) }}

		@if (isset($errors) && ($errors->has('key') ))
		  <div class="alert alert-danger">
		    @if($errors->has('key'))
		    	<?php echo $errors->first('key'); ?>
		    @endif	
		  </div>
		@endif

		<div class="form-group"> 
		    {{ Form::label('', 'Key') }}
		    {{ Form::password('key', ['style' => 'width:300px;', 'placeholder' => '']) }}
		</div>
		<div style="clear:both;"></div>
		<div class="form-group btn-row">
		    <label style="width:70px;">&nbsp;</label>
		     {{ Form::submit('Login', array('class' => 'btn btn-primary', 'style' => 'background:#888;color:white;padding:3px 8px;font-size:12px;position:relative;top:-20px;')) }}
		</div>            

	  {{ Form::close() }}  

<style>
body { background:#fff;  }
label { width:70px; }	
</style>
@stop