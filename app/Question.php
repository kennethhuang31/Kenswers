<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
	// Create the Question
    public function add()
    {
    	// Check login
    	if (!user_ins()->is_login())
    		return ['status' => 0, 'msg' => 'login required.'];

    	// check if there is a title of a question
    	if(!rq("title"))
    		return ['status' => 0, 'msg' => 'title required.'];

    	// Save the data to the database
    	$this->title = rq("title");
    	$this->user_id = session("user_id");
    	if(rq("desc"))
    		$this->desc = rq("desc");

    	return $this->save()? 
    		['status' => 1, 'id' => $this->id]:
    		['status' => 0, 'msg' => 'db insert failed.'];
    }

    // Api for update
    public function change()
    {
    	// Check login
    	if (!user_ins()->is_login())
    		return ['status' => 0, 'msg' => 'login required.'];

    	// Check the id of question
    	if (!rq("id"))
    		return ['status' => 0, 'msg' => 'id required.'];

    	// Get the model of this id's date
    	$question = $this->find(rq("id"));

    	// Check if the question exists
    	if (!$question)
    		return ['status' => 0, 'msg' => 'the question does not exist'];
    	if ($question->user_id != session("user_id"))
    		return ['status' => 0, 'msg' => 'permission deny.'];
    	if (rq("title"))
    		$question->title = rq('title');
    	if (rq("desc"))
    		$question->desc = rq('desc');

    	// Save to the database.
    	return $question->save()? 
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

    // To show questions
    public function read()
    {
    	// If there is an id in the request, then return the question of the id 
    	if (rq("id"))
	    	return ['status' => 1, 'data' => $this->find(rq("id"))];

        if (rq('user_id'))
        {
            $user_id = rq('user_id') === 'self' ?
                session('user_id') :
                rq('user_id');
            return $this->read_by_user_id($user_id);
        }

	    // division page
        list($limit, $skip) = paginate(rq('page'), rq("limit"));

	    // Construct the query and return the collection data
	    $r = $this
	    	->orderBy("created_at")
	    	->limit($limit)
	    	->skip($skip)
	    	->get(['id', 'title', 'desc', 'user_id', 'created_at', 'updated_at'])
	    	->keyBy('id');

	    return ['status' => 1, 'data' => $r];
    }

    // To delete questions
    public function remove()
    {
    	// Check login
    	if (!user_ins()->is_login())
    		return ['status' => 0, 'msg' => 'login required.'];

    	// Check the id of question
    	if (!rq("id"))
    		return ['status' => 0, 'msg' => 'id required.'];

    	// Get the model of this id's date
    	$question = $this->find(rq("id"));

    	// Check if the question exists
    	if (!$question)
    		return ['status' => 0, 'msg' => 'the question does not exist'];
    	if ($question->user_id != session("user_id"))
    		return ['status' => 0, 'msg' => 'permission deny.'];

    	return $question->delete() ?
    		['status' => 1]:
    		['status' => 0, 'msg' => 'db delete failed.'];
    }

    public function users()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
