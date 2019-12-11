;(function () {
	'use strict';
	/**
	* answer Module
	*
	* Description
	*/
	angular.module('answer', [])
		.service('AnswerService', [
			'$http', 
			function($http){
				var me = this;
				me.data = {};

				/*
					@answerss is an array which is for counting the votes.
				*/

				me.count_vote = function (answers) {
					// iterate all data
					for (var i = 0; i < answers.length; i++) {
						var item = answers[i];
						if (!item['question_id']) continue;

						me.data[item.id] = item;

						if (!item['users']) continue;
						item.upvote_count = 0;
						item.downvote_count = 0;
						var votes = item['users'];
						for (var j = 0; j < votes.length; j++) {
							var v = votes[j];
							if (v['pivot'].vote === 1)
								item.upvote_count++;
							if (v['pivot'].vote === 2)
								item.downvote_count++;
						}
					}
					return answers;
				}

				me.vote = function (conf) {
					if (!conf.id || !conf.vote)
					{
						console.log('id and vote are required');
						return;
					}

					var answer = me.data[conf.id];
					var users = answer.users;

					// Judge if the current user has voted this answer
					for (var i = users.length - 1; i >= 0; i--) {
						if (users[i].id == his.id 
							&& conf.vote == users[i].pivot.vote)
							conf.vote = 3; 
					}

					return $http.post('api/answer/vote', conf)
								.then(function (r) {
									if (r.data.status)
										return true;
									return false;
								}, function () {
									return false;
								});
				}

				me.update_data = function (id) {
					return $http.post('/api/answer/read', {id: id})
						.then(function (r) {
							me.data[id] = r.data.data;
						});
				}

				me.read = function (params) {
					return $http.post('/api/answer/read', params)
						.then(function (r) {
								if (r.data.status) {
									me.data = angular.merge({}, me.data, r.data.data);
									return r.data.data;
								}
								return false;
							}
						);
				}
		}])
})();