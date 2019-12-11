<div ng-controller="SignupController" class="signup container">
	<div class="card">
		<h1>Signup</h1>
		<!-- [: User.signup_data :] -->
		<form name="signup_form" ng-submit="User.signup()">
			<div class="input-group">
				<label>Username: </label>
				<input name="username" 
					type="text" 
					ng-minlength=4
					ng-maxlength=24
					ng-model="User.signup_data.username"
					ng-model-options="{debounce: 500}" 
					required
				>
				<div ng-if="signup_form.username.$touched" class="input-error-set">
					<div ng-if="signup_form.username.$error.required">
						Username is required
					</div>
					<div ng-show="signup_form.username.$error.minlength ||
						 signup_form.username.$error.maxlength">
						Minimum 4 characters and maximum 24 characters
					</div>
					<div ng-if="User.signup_username_exists">
						Username exists
					</div>
				</div>
			</div>

			<div class="input-group">
				<label>Password: </label>
				<input name="password" 
					type="text" 
					ng-minlength=6
					ng-maxlength=255
					ng-model="User.signup_data.password"
					required
				>
				<div ng-if="signup_form.password.$touched" class="input-error-set">
					<div ng-if="signup_form.password.$error.required">
						Password is required
					</div>
					<div ng-if="signup_form.password.$error.minlength">
						Minimum 6 characters required
					</div>
					<div ng-if="signup_form.password.$error.maxlength">
						Maximum 255 characters
					</div>

				</div>
			</div>
			<button type="submit"
					class="primary"
					ng-disabled="signup_form.$invalid"
			>Signup</button>
		</form>
	</div>
</div>