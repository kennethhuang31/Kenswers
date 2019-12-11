<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
	// Api for create an answer
    public function add()
    {
    	// Check login
    	if (!user_ins()->is_login())
    		return ['status' => 0, 'msg' => 'login required.'];

    	if(!rq("question_id") || !rq('content'))
    		return ['status' => 0, 'msg' => 'question_id and content required.'];

    	$question = question_ins()->find(rq("question_id"));

    	// Check if the question exists
    	if (!$question)
    		return ['status' => 0, 'msg' => 'the question does not exist'];

    	// Check if the question has been answered
    	$answered = $this
    		->where(['question_id' => rq('question_id'), 'user_id' => session('user_id')])
    		->count();
    	if ($answered)
    		return ['status' => 0, 'msg' => 'question answered.'];

    	// Save the data
    	$this->content = rq('content');
    	$this->question_id = rq('question_id');
    	$this->user_id = session('user_id');
    	return $this->save() ?
    		['status' => 1, 'id' => $this->id]:
    		['status' => 0, 'msg' => 'db insert failed.'];
    }

    // Api for update the answer
    public function change()
    {
    	// Check login
    	if (!user_ins()->is_login())
    		return ['status' => 0, 'msg' => 'login required.'];

    	// check if there is a title of a question
    	if(!rq("id") || !rq('content'))
    		return ['status' => 0, 'msg' => 'id and content required.'];

    	$answer = $this->find(rq('id'));
    	if ($answer->user_id != session('user_id'))
    		return ['status' => 0, 'msg' => 'permission deny'];

    	$answer->content = rq('content');
    	return $answer->save() ?
    		['status' => 1]:
    		['status' => 0, 'msg' => 'db update failed.'];
    }

    public function read_by_user_id($user_id)
    {
        $user = user_ins()->find($user_id);
        if(!$user)
            return err('user not exists');

        $r = $this->where('user_id', $user_id)
            ->get()->keyBy('id');

        return sus($r->toArray());
    }

    // Api for show the answers
    public function read()
    {
    	if (!rq('id') && !rq('question_id') && !rq('user_id'))
    		return ['status' => 0, 'msg' => 'id, user_id or question_id required.'];

        if (rq('user_id'))
        {
            $user_id = rq('user_id') === 'self' ?
                session('user_id') :
                rq('user_id');
            return $this->read_by_user_id($user_id);
        }

    	if (rq('id'))
    	{
    		$answer = $this
                // ->with('user')
                ->with('users')
                ->find(rq('id'));
    		if (!$answer)
    			return ['status' => 0, 'msg' => 'question unanswered.'];
    		return ['status' => 1, 'data' => $answer];
    	}

    	// Check if this question exist
    	if (!question_ins()->find(rq('question_id')))
    			return ['status' => 0, 'msg' => 'question does not exist.'];

    	// Search all answers of this question
    	$answers = $this
    		->where('question_id', rq('question_id'))
    		->get()
    		->keyBy('id');



    	return ['status' => 1, 'data' => $answers];
    }

    // Api for voting
    public function vote()
    {
        // Check login
        if (!user_ins()->is_login())
            return ['status' => 0, 'msg' => 'login required.'];

        if (!rq('id') || !rq('vote'))
            return ['status' => 0, 'msg' => 'id or vote required.'];

        $answer = $this->find(rq('id'));
        if (!$answer)
            return ['status' => 0, 'msg' => 'question unanswered.'];

        // 1 for upvode , 2 for downvote, 3 for clearvote
        $vote = rq('vote');
        if ($vote != 1 && $vote != 2 && $vote != 3)
            return ['status' => 0, 'msg' => 'invalid vote'];

        // Check if this user voted, this vote will be deleted.
        $vote_ints = $answer
            ->users()
            ->newPivotStatement()
            ->where('user_id', session('user_id'))
            ->where('answer_id', rq('id'))
            ->delete();

        if ($vote == 3)
            return ['status' => 1];



        $answer
            ->users()
            ->attach(session('user_id'), ['vote' => $vote]);

        return ['status' => 1];

    }

    public function user()
    {   
        return $this->belongsTo('App\User');
    }

    public function users()
    {   
        return $this
            ->belongsToMany('App\User')
            ->withPivot('vote')
            ->withTimestamps();
    }
}
