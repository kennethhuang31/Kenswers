<div ng-controller="UserController">
	<div class="card">
		<h1></h1>
	</div>
	user page

	<h2>User Questions</h2>
	<div ng-repeat="(key, value) in User.his_questions">
		[: value.title :]
	</div>

	<h2>User Answers</h2>
	<div ng-repeat="(key, value) in User.his_answers">
		[: value.content :]
	</div>
</div>