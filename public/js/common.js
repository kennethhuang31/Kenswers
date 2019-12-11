;(function () {
	'use strict';
	/**
	* common Module
	*
	* Description
	*/
	angular.module('common', [])
		.service('TimelineService', [
			'$http', 
			'AnswerService', 
			function($http, AnswerService){
				var me = this;
				me.data = [];
				me.current_page = 1;

				// Get the home data
				me.get = function (conf) {
					if (me.pending) return;
					me.pending = true;

					conf = conf || {page: me.current_page};

					$http.post('api/timeline', conf)
						.then(function (r) {
							if (r.data.status)
							{
								if (r.data.data.length) 
								{
									me.data = me.data.concat(r.data.data);
									// Count the votes of every answer
									me.data = AnswerService.count_vote(me.data);
									me.current_page++;
								}
								else
									me.no_more_data = true;
								
							}
							else
								console.error('network error');
						}, function () {
							console.error('network error');
						})
						.finally(function () {
							me.pending = false;
						});
				}

				// Vote in the timeline
				me.vote = function (conf) {
					// Using the core function of votes
					AnswerService.vote(conf)
						.then(function (r) {
							// if successfully, update the data of votes.
							if(r)
							{
								AnswerService.update_data(conf.id);
							}
						});
				}
		}])

		.controller('HomeController', [
			'$scope', 
			'TimelineService',
			'AnswerService',
			function($scope, TimelineService, AnswerService){
				var $win;
				$scope.Timeline = TimelineService;
				TimelineService.get();

				$win = $(window);
				$win.on('scroll', function () {
					if ($win.scrollTop() - ($(document).height() - $win.height()) > -30)
					{
						TimelineService.get();
					}
				})

				// Monitor the change of answer data, if data updated, the data of other module will also update immediately.
				$scope.$watch(function () {
					return AnswerService.data;
				}, function (newData, oldData) {
					var timeline_data = TimelineService.data;
					for (var k in newData) {
						// Update the answer data of timeline.
						for (var i = 0; i < timeline_data.length; i++)
						{
							if(k == timeline_data[i].id) {
								timeline_data[i] = newData[k];
							}
						}
					}
					TimelineService.data = AnswerService.count_vote(TimelineService.data);
				}, true)
		}])
})();