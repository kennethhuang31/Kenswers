<div ng-controller="HomeController" class="home card container">
	<h1>Latest Posts</h1>
	<div class="hr"></div>
	<div class="item-set">
		<div ng-repeat="row in Timeline.data track by $index" class="feed item clearfix">
			<div ng-if='row.question_id && row.users[0]' class="vote">
				<div ng-click="Timeline.vote({id:row.id, vote:1})" class="up">Up [: row.upvote_count :]</div>
				<div ng-click="Timeline.vote({id:row.id, vote:2})" class="down">Down [: row.downvote_count :]</div>
			</div>
			<div class="feed-item-content">
				<div ng-if="!row.question_id" class="content-act">[: row.users.username :] added this question</div>
				<div ng-if="row.question_id" class="content-act">[: row.users[0].username :] added this answer</div>
				<div class="title">[: row.title :]</div>
				<div class="content-owner">
					[: row.users.username :]
					<span class="desc">Former Sr UI Engineer</span>
				</div>
				<div class="content-main">
					[: row.content :]
				</div>
				<div class="action-set">
					<div class="comment">Comments</div>
				</div>
				<div class="comment-block">
					<div class="hr"></div>
					<div class="comment-item-set">
						<div class="comment-rect"></div>
						<div class="comment-item clearfix">
							<div class="user">Mo Nastri</div>
							<div class="comment-content">
								Very enjoyable read! The best part was hearing about a cleaned-up Washburn. I never got a chance to play against pro players, but I did get into some Saturday morning gym games with some NC State players in the 90s, one or two of whom got NBA looks. Even those guys are head and shoulders above the rest of us rec-leaguers players. Folks you thought were not great shooters seemed to never miss in those games.
							</div>
						</div>
						<div class="comment-item clearfix">
							<div class="user">Tyler Tidwell</div>
							<div class="comment-content">
								 I did get into some Saturday morning gym games with some NC State players in the 90s, one or two of whom got NBA looks. Even those guys are head and shoulders above the rest of us rec-leaguers players. Folks you thought were not great shooters seemed to never miss in those games.
							</div>
						</div>
						<div class="comment-item clearfix">
							<div class="user">Isaac Clark</div>
							<div class="comment-content">
								Even those guys are head and shoulders above the rest of us rec-leaguers players. Folks you thought were not great shooters seemed to never miss in those games.
								Very enjoyable read! The best part was hearing about a cleaned-up Washburn. I never got a chance to play against pro players, but I did get into some Saturday morning gym games with some NC State players in the 90s, one or two of whom got NBA looks. 
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="hr"></div>
		</div>
		<div ng-if="Timeline.pending" class="tac">Loading...</div>
		<div ng-if="Timeline.no_more_data" class="tac">No more data.</div>
	</div>	
</div>