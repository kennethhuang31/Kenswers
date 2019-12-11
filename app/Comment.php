<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
	// Api for creating comments
    public function add()
    {
    	// Check login
    	if (!user_ins()->is_login())
    		return ['status' => 0, 'msg' => 'login required.'];

    	// Check if there is content
    	if(!rq('content'))
    		return ['status' => 0, 'msg' => 'content required.'];

    	// Check question id and answer id
    	if(!rq("question_id") && !rq('answer_id'))
    		return ['status' => 0, 'msg' => 'question_id or answer_id required.'];
    	if(rq("question_id") && rq('answer_id'))
    		return ['status' => 0, 'msg' => 'question_id and answer_id both exist.'];

    	if(rq('question_id'))
		{
			$question = question_ins()->find(rq('question_id'));

			// Check if the question exists
    		if (!$question)
    			return ['status' => 0, 'msg' => 'the question does not exist'];
    		$this->question_id = rq('question_id');
		} 
		else
		{
			$answer = answer_ins()->find(rq('answer_id'));

			// Check if the answer exists
    		if (!$answer)
    			return ['status' => 0, 'msg' => 'the answer does not exist'];
    		$this->answer_id = rq('answer_id');
		}

		// Check if this comment is for reply
		if (rq("reply_to"))
		{
			$target = $this->find(rq('reply_to'));

			// Check if the target comment exists
			if (!$target)
    			return ['status' => 0, 'msg' => 'target comment does not exist'];

    		// Check if they reply to themselves
    		if ($target->user_id == rq('reply_to'))
    			return ['status' => 0, 'msg' => 'cannot reply to yourself'];

    		$this->reply_to = rq('reply_to');
		}

		// Save the data
		$this->content = rq('content');
		$this->user_id = session('user_id');
		return $this->save() ?
    		['status' => 1, 'id' => $this->id]:
    		['status' => 0, 'msg' => 'db insert failed.'];
    }

    // Api for showing the comments
    public function read()
    {
    	// Check question id and answer id
    	if(!rq("question_id") && !rq('answer_id'))
    		return ['status' => 0, 'msg' => 'question_id or answer_id required.'];

    	if(rq('question_id'))
		{
			$question = question_ins()->find(rq('question_id'));

			// Check if the question exists
    		if (!$question)
    			return ['status' => 0, 'msg' => 'the question does not exist'];
    		$data = $this
    			->where('question_id', rq('question_id'))
    			->get();
		}
		else
		{
			$answer = answer_ins()->find(rq('answer_id'));

			// Check if the answer exists
    		if (!$answer)
    			return ['status' => 0, 'msg' => 'the answer does not exist'];
    		$data = $this
    			->where('answer_id', rq('answer_id'))
    			->get();
		}

		return ['status' => 1, 'data' => $data->keyBy('id')];
    }

    // Api for removing the comments
    public function remove()
    {
    	// Check login
    	if (!user_ins()->is_login())
    		return ['status' => 0, 'msg' => 'login required.'];

    	if (!rq('id'))
    		return ['status' => 0, 'msg' => 'id required.'];

    	$comment = $this->find(rq('id'));
    	if (!$comment)
    		return ['status' => 0, 'msg' => 'comment does not exist'];

		if ($comment->user_id != session('user_id'))
			return ['status' => 0, 'msg' => 'permission deny'];

		// delete all replys before deleting this comment.
		$this->where('reply_to', rq('id'))->delete();

		return $comment->delete() ?
    		['status' => 1]:
    		['status' => 0, 'msg' => 'db delete failed.'];
		
    }
}
