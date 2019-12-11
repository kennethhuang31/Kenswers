<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommonController extends Controller
{
	// Api for timeline
    public function timeline()
    {
    	// division page
        list($limit, $skip) = paginate(rq('page'), rq("limit"));

        // get the questions data
        $questions = question_ins()
        	->with('users')
	    	->limit($limit)
	    	->skip($skip)
	    	->orderBy("created_at", 'desc')
	    	->get();

	   	// get the answers data
	    $answers = answer_ins()
	        ->with('users')
	    	->limit($limit)
	    	->skip($skip)
	    	->orderBy("created_at", 'desc')
	    	->get();

	    // merge the questions and answers data
	    $data = $questions->merge($answers);
	    $data = $data->sortByDesc(function ($item)
	    {
	    	return $item->created_at;
	    });
 
	    $data = $data->values()->all();
	    return ['status' => 1, 'data' => $data];
    }
}
