<!DOCTYPE html>
<html ng-app="kenswers" user-id="{{session('user_id')}}">
<head>
	<meta charset="utf-8">
	<title>Kenswers</title>
	<link rel="stylesheet" type="text/css" href="/node_modules/normalize.css/normalize.css">
	<link rel="stylesheet" type="text/css" href="/css/base.css">
	<script type="text/javascript" src="/node_modules/jquery/dist/jquery.js"></script>
	<script type="text/javascript" src="/node_modules/angular/angular.js"></script>
	<script type="text/javascript" src="/node_modules/angular-ui-router/release/angular-ui-router.js"></script>
	<script type="text/javascript" src="/js/base.js"></script>
	<script type="text/javascript" src="/js/user.js"></script>
	<script type="text/javascript" src="/js/common.js"></script>
	<script type="text/javascript" src="/js/question.js"></script>
	<script type="text/javascript" src="/js/answer.js"></script>
</head>
<body>
	<div class="navbar clearfix">
		<div class="container">
			<div class="fl">
				<div ui-sref="home" class="navbar-item brand">Kenswers</div>
				<form ng-submit="Question.go_add_question()" id="quick_ask" ng-controller="QuestionAddController">
					<div class="navbar-item">
						<input type="text" ng-model="Question.new_question.title">
					</div>
					<div class="navbar-item">
						<button type="submit">Add Question</button>
					</div>
				</form>		
			</div>

			<div class="fr">
				<a ui-sref="home" class="navbar-item">Home</a>
				@if(session('username'))
					<a ui-sref="home" class="navbar-item">{{session('username')}}</a>
					<a href="{{url('/api/logout')}}" class="navbar-item">Logout</a>
				@else
					<a ui-sref="login" class="navbar-item">Login</a>
					<a ui-sref="signup" class="navbar-item">Signup</a>
				@endif
			</div>
		</div>
	</div>

	<div class="page">
		<div ui-view></div>
	</div>
</body>
</html>