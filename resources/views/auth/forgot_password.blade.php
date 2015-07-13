@extends('layouts.auth.master')

@section('content')
	<div class="ibox-content">
	    <h2 class="font-bold">Forgot password</h2>
	    <p>Enter the email address you registered with and your password will be reset and emailed to you.</p>
		<div class="row">
		    <div class="col-lg-12">
	        	<form action="/forgot-password" method="post" class="m-t">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="form-group">
						<input type="email" name="email" class="form-control input-lg" placeholder="Your email address" data-parsley-errors-container="#error-container3" value="{{ Input::old('email') }}" required>
					</div>
					<button type="submit" class="btn btn-primary block full-width m-b">Send new password</button>
					<a href="/login">
	                    <small>Sign in to your account</small>
	                </a>
				</form>
			</div>
		</div>
	</div>
@stop
