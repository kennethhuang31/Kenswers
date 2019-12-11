<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Request;
use Hash;

class User extends Model
{
	// Api for signup
    public function signup() 
    {
    	// to check if the username/password is null.
    	$check_username_and_password = $this->check_username_and_password();
    	if (!$check_username_and_password)
    		return err("username or password can not be null.");
		$username = $check_username_and_password[0];
		$password = $check_username_and_password[1];

    	// to check if the username exists.
    	$user_exists = $this
    		->where('username', $username)
    		->exists();
    	if ($user_exists)
    		return err("username exists.");
    	
    	// to encrypt the password
    	$hashed_password = bcrypt($password);

    	// to store in the database
    	$user = $this;
    	$user->username = $username;
    	$user->password = $hashed_password;
    	if ($user->save())
    		return sus(['id' => $user -> id]);
    	else
    		return err('db insert failed.');
    } 

    // Api for getting the user information
    public function read()
    {
        if (!rq('id'))
            return err('id required');

        $id = rq('id') === 'self' ?
            session('user_id') : rq('id');

        $get = ['id', 'username', 'avatar_url', 'intro'];
        $user = $this->find($id, $get);
        $data = $user->toArray();
        $answer_count = answer_ins()->where('user_id', $id)->count();
        $question_count = question_ins()->where('user_id', $id)->count();
        $data['answer_count'] = $answer_count;
        $data['question_count'] = $question_count;

        return sus($data);
    }

    // Api for login
    public function login() 
    {
    	// to check if the username/password exists
    	$check_username_and_password = $this->check_username_and_password();
    	if (!$check_username_and_password)
    		return err("username or password can not be null.");
		$username = $check_username_and_password[0];
		$password = $check_username_and_password[1];

		// to check if this username in the database
		$user = $this->where('username', $username)->first(); //return the first searching result.
		if (!$user)
			return err("username does not exist.");
		$hashed_password = $user->password;

		// to check if this password is correct.
		if (!Hash::check($password, $hashed_password))
			return err("password is not correct.");
		
		// to put the user information to the session storage.
		session()->put('username', $user->username);
		session()->put('user_id', $user->id);

		return sus(['id' => $user->id]);

    }

    public function check_username_and_password()
    {
    	$username = rq("username");
    	$password = rq("password");
    	if ($username && $password)
    		return [$username, $password];
    	else
    		return false;
    }

    public function logout() 
    {
    	// to delete the user information in the session storage.
    	session()->forget('username');
    	session()->forget('user_id');

    	return sus();
    }

    public function is_login()
   	{
   		return is_login();
   	} 

    // Api for change password
    public function change_password()
    {
        // Check login
        if (!user_ins()->is_login())
            return err('login required.');

        if (!rq('old_password') || !rq('new_password'))
            return err('old password and new password both required');

        $user = $this->find(session('user_id'));

        if (!Hash::check(rq('old_password'), $user->password))
            return err('old password is wrong');

        $user->password = bcrypt(rq('new_password'));
        return $user->save()?
            sus():
            err('db update failed.');
    }

    // Api for find back the password
    public function reset_password()
    {
        if ($this->is_robot())
            return err("robot possible.");

        if (!rq('phone'))
            return err('phone is required');

        $user = $this->where('phone', rq('phone'))->first();

        if (!$user)
            return err('invalid phone number');

        $captcha = $this->generate_captcha();
        
        $user->phone_captcha = $captcha;
        if ($user->save())
        {
            $this->send_sms();
            // for preparing for the check for the next time 
            $this->update_robot_time();
            return sus();
        }
        else
        {
            return err('db update failed');
        }        
    }

    // Api for validate and find the password back
    public function validate_reset_password()
    {
        if ($this->is_robot(2))
            return err("robot possible.");

        if (!rq('phone') || !rq('phone_captcha') || !rq('new_password'))
            return err('phone, new password and phone_captcha required.');

        // Check if this user exists
        $user = $this
            ->where(['phone' => rq('phone'), 'phone_captcha' => rq('phone_captcha')])
            ->first();

        if (!$user)
            return err('invalid phone or captcha');

        $user->password = bcrypt(rq('new_password'));
        $this->update_robot_time();
        return $user->save() ?
            sus() : err('db update failed');
    }

    // check the robot 
    public function is_robot($time = 10)
    {
        // if there is not last_action_time in the session, it means the api has not been used.
        if (!session('last_action_time'))
            return false;

        $current_time = time();
        $last_action_time = session('last_action_time');
        $elapsed = $current_time - $last_action_time;
         return !($elapsed > $time);
    }

    public function update_robot_time()
    {
        session()->put('last_action_time', time());
    }

    public function send_sms()  
    {   
        return true;
    }

    // generate the captcha
    public function generate_captcha()
    {
        return rand(1000, 9999);
    }

    public function answers()
    {
        return $this
            ->belongsToMany('App\Answer')
            ->withPivot('vote')
            ->withTimestamps();
    }

    // Api for check if this username exists
    public function exists()
    {
        $count = $this->where(rq())->count();
        return $count?
            sus() : err('username not exist');
    }
}
