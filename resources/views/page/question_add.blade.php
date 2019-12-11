<div ng-controller="QuestionAddController" class="question-add container">
	<div class="card">
		<form name="question_add_form" ng-submit="Question.add()">
			<div class="input-group">
				<label>Question Title</label>
				<input type="text" 
					name="title" 
					ng-model="Question.new_question.title"
					ng-minlength="5"
					ng-maxlength="255"
					required 
				>
			</div>
			<div class="input-group">
				<label>Question Description</label>
				<textarea class="question-desc-textarea" 
					type="text" 
					ng-model="Question.new_question.desc"
					name="desc" 
				>
				</textarea>
			</div>
			<div class="input-group">
				<button class="primary" 
					type="submit"
					ng-disabled="question_add_form.title.$invalid"
				>
					Submit
				</button>
			</div>
		</form>
	</div>	
</div>