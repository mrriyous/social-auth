<?php
trait Twitter{
	public function login_twitter()
	{	
		if(isset($_GET['oauth_token']) && isset($_GET['oauth_verifier']) && $_SESSION['token'] == $_GET['oauth_token'])
		{	
			return $this->login_finish_twitter();
		}

		$request_token = $this->tw->getRequestToken($this->tw_callback);
		$_SESSION['token'] 			= $request_token['oauth_token'];
		$_SESSION['token_secret'] 	= $request_token['oauth_token_secret'];

		if($this->tw->http_code == '200')
		{
			$login_url = $this->tw->getAuthorizeURL($request_token['oauth_token']);
			redirect($login_url); 
		}else{
			redirect(base_url());
			die("error connecting to twitter! try again later!");
		}
	}

	public function login_finish_twitter()
	{
		if(empty($_SESSION['token']) or empty($_SESSION['token_secret']))
		{
			die("Token invalid");
			redirect(base_url());
		}

		$this->tw = new TwitterOAuth($this->tw_consumer_key, $this->tw_consumer_secret, $_SESSION['token'] , $_SESSION['token_secret']);
		$access_token = $this->tw->getAccessToken($_GET['oauth_verifier']);
		$_SESSION['twitter_access_token'] = $access_token;
		
		unset($_SESSION['token']);
		unset($_SESSION['token_secret']);
		redirect(base_url('login/social/confirm?pv=twitter'));
	}

	public function get_twitter_user_data($access_token)
	{
		$screen_name 		= $access_token['screen_name'];
		$twitter_id			= $access_token['user_id'];
		$oauth_token 		= $access_token['oauth_token'];
		$oauth_token_secret = $access_token['oauth_token_secret'];

		$this->tw = new TwitterOAuth($this->tw_consumer_key, $this->tw_consumer_secret, $oauth_token , $oauth_token_secret);

		$user_info = $this->tw->get('account/verify_credentials');
		return $user_info;
	}

	public function get_twitter_user_id($userdata)
	{
		return $userdata->id;
	}

	public function generate_twitter_user_data($userdata,$access_token)
	{
		$name=explode(" ", $userdata->name);
		return [
			'member_social'=>'twitter',
			'member_social_id' => $userdata->id,
			'member_firstname'=>htmlspecialchars($name[0]),
			'member_lastname'=>htmlspecialchars($name[1]),
			'member_username'=>$userdata->screen_name,
			'member_dob'=>"",
			'member_since'=>date('Y-m-d'),
			'member_email'=>'',
			'approved'=>1,
			'member_picture' => 'https://twitter.com/'.$userdata->screen_name.'/profile_image?size=original',
			'activation_status' => 'Validated',
			'oauth_token' => $access_token['oauth_token'],
			'oauth_secret' => $access_token['oauth_token_secret'],
		];
	}
}