@extends('layouts.guests')

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h1>Register</h1>
			</div>
			<div class="panel-body">
				<form data-parsley-validate action="/signup" method="post" class="form-horizontal">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="user[site_id]" value="{{ Request::input('site_id') }}">
					<fieldset>
						<legend>Personal Information</legend>
						<div class="form-group-inline">
							<label class="col-md-2 control-label">Name<span class="required-field">*</span></label>
							<div class="col-md-2">
								<input type="text" name="user[firstname]" class="form-control" value="{{ Input::old('user.firstname') }}" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label">Last Name<span class="required-field">*</span></label>
							<div class="col-md-2">
								<input type="text" name="user[lastname]" class="form-control" value="{{ Input::old('user.lastname') }}" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label">Email<span class="required-field">*</span></label>
							<div class="col-md-6">
								<input type="email" name="user[email]" class="form-control" value="{{ Input::old('user.email') }}" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label">Password<span class="required-field">*</span></label>
							<div class="col-md-6">
								<input id="password" type="password" name="user[password]" class="form-control" value="{{ Input::old('user.password') }}" data-parsley-minlength="2" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label">Confirm Password<span class="required-field">*</span></label>
							<div class="col-md-6">
								<input type="password" name="password_confirm" class="form-control" value="{{ Input::old('password_confirm') }}" data-parsley-equalto="#password" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label">ID / RUT<span class="required-field">*</span></label>
							<div class="col-md-6">
								<input type="text" name="user[nin]" class="form-control" value="{{ Input::old('user.nin') }}" required>
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend>Shipping Address</legend>
						<div class="form-group">
							<label class="col-md-2 control-label">Address<span class="required-field">*</span></label>
							<div class="col-md-6">
								<input name="user[address1]" class="form-control" value="{{ Input::old('user.address1') }}" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label">Apt / Unit</label>
							<div class="col-md-6">
								<input name="user[address2]" class="form-control" value="{{ Input::old('user.address2') }}">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label">City<span class="required-field">*</span></label>
							<div class="col-md-6">
								<input name="user[city]" class="form-control" value="{{ Input::old('user.city') }}" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label">State<span class="required-field">*</span></label>
							<div class="col-md-6">
								<input type="text" name="user[state]" class="form-control" value="{{ Input::old('user.state') }}" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label">Postal Code<span class="required-field"></span></label>
							<div class="col-md-6">
								<input type="text" name="user[postal_code]" class="form-control" value="{{ Input::old('user.postal_code') }}" required>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">Country</label>
							<div class="col-sm-6">
								<select name="user[country_id]" class="form-control">
									@foreach (\App\Models\Country::all() as $country)
										<option value="{{ $country->id }}">{{ $country->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group-inline">
							<label class="col-md-2 control-label">Home Phone</label>
							<div class="col-md-2">
								<input type="text" name="user[phone]" class="form-control" value="{{ Input::old('user.phone') }}">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label">Cell Phone<span class="required-field">*</span></label>
							<div class="col-md-2">
								<input type="text" name="user[cellphone]" class="form-control" value="{{ Input::old('user.cellphone') }}" required>
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend>Terms & Conditions</legend>
						<div class="form-group">
							<div class="col-md-8">
								<textarea class="form-control" rows="7"></textarea>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12">
								<div class="checkbox">
									<label>
										<input id="termscheck" type="checkbox" required> I have read and agree with the terms
									</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12">
								<button type="submit" class="btn btn-lg btn-success">Enviar</button>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</div>
@stop