<div ng-controller="LoginController" class="login container">
	<div class="card">
		<h1>Login</h1>
		<!-- [: User.login_data :] -->
		<form name="login_form" ng-submit="User.login()">
			<div class="input-group">
				<label>Username: </label>
				<input name="username" 
					type="text" 
					ng-model="User.login_data.username"
					required
				>
			</div>

			<div class="input-group">
				<label>Password: </label>
				<input name="password" 
					type="password" 
					ng-model="User.login_data.password"
					required
				>
			</div>

			<div class="input-error-set">
				<div ng-if="User.login_failed_error">
					Username or password is wrong
				</div>
			</div>

			<button type="submit"
					class="primary"
					ng-disabled="login_form.password.$error.required || 
					login_form.username.$error.required"
			>Login</button>
		</form>
	</div>
</div>