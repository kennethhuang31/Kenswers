;(function () {
	'use strict';
	/**
	* user Module
	*
	* Description
	*/
	angular.module('user', [
			'answer',
		])
		.service('UserService',['$http', '$state', function($http, $state){
			var me = this;
			me.signup_data = {};
			me.login_data = {};

			me.read = function (param) {
				return $http.post('/api/user/read', param)
					.then(function (r) {
						console.log('r',r);
					});
			}

			me.signup = function(){
				$http.post('/api/signup', me.signup_data)
					.then(function(r){
						if (r.data.status)
						{
							me.signup_data = {};
							$state.go('login');
						}
					}, function(e) {
						console.log('e',e);
					});
			}

			me.login = function(){
				$http.post('/api/login', me.login_data)
					.then(function(r){
						// console.log(r)
						if (r.data.status)
							// refersh the the site
							location.href = '/';
						else
							me.login_failed_error = true;
					}, function(e) {
						console.log('e',e);
					});
			}

			me.username_exists = function() {
				$http.post('/api/user/exists', 
					{username: me.signup_data.username})
					.then(function(r){
						if (r.data.status == '1')
						{
							me.signup_username_exists = true;
						}
						else
						{
							me.signup_username_exists = false;
						}
					}, function(e) {
						console.log('e',e);
					});
			}

		}])

		.controller('SignupController', [
			'$scope', 
			'UserService', 
			function($scope, UserService){
				$scope.User = UserService;

				$scope.$watch(function () {
					return UserService.signup_data;
				}, function(newValue, oldValue) {
					if (newValue.username != oldValue.username)
						UserService.username_exists(); 
				}, true);

		}])

		.controller('LoginController', [
			'$scope', 
			'UserService', 
			function($scope, UserService){
				$scope.User = UserService;
		}])

		.controller('UserController', [
			'$scope', 
			'$stateParams', 
			'UserService', 
			'AnswerService', 
			function($scope, $stateParams, UserService, AnswerService){
				$scope.User = UserService;
				console.log($stateParams);
				UserService.read($stateParams);
				AnswerService.read({user_id: $stateParams.id})
					.then(function (r) {
						if(r)
							UserService.his_answers = r;
					});

				QuestionService.read({user_id: $stateParams.id})
					.then(function (r) {
						if(r)
							UserService.his_questions = r;
					});
		}])
})();