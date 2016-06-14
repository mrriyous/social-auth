<?php
trait Facebook{
	public function login_facebook()
	{
		$helper = $this->fb->getRedirectLoginHelper();
		$permissions = ['email', 'user_likes','user_friends']; // optional
		$loginUrl = $helper->getLoginUrl(base_url('login/social/finish?pv=facebook'), $permissions);

		redirect($loginUrl);
	}

	public function login_finish_facebook()
	{
		$helper = $this->fb->getRedirectLoginHelper();
		try {
		  $accessToken = $helper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
		  echo 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  // When validation fails or other local issues
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}
		if (isset($accessToken)) {
			$_SESSION['facebook_access_token'] = (string) $accessToken;
			redirect(base_url("login/social/confirm?pv=facebook"));
		}else{
			redirect(base_url("?login=cancel"));
		}
	}

	public function get_facebook_user_data($access_token)
	{
		try {
			$user = $this->fb->get('/me?fields=email,id,name,first_name,last_name,birthday,gender,hometown,location,picture.width(200)',$access_token);
			return $user->getGraphUser();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
		   	exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		   echo 'Facebook SDK returned an error: ' . $e->getMessage();
		   exit;
		}
	}

	public function get_facebook_graph($api,$access_token)
	{
		try {
			$user = $this->fb->get('me?fields='.$api,$access_token);
			return $user->getGraphUser();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
		   	exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		   echo 'Facebook SDK returned an error: ' . $e->getMessage();
		   exit;
		}
	}

	public function get_facebook_user_id($userdata)
	{
		return $userdata->getId();
	}

	public function generate_facebook_user_data($userdata,$access_token=null)
	{
		$birthday = $userdata->getBirthday();
		return [
			'member_social'=>'facebook',
			'member_social_id' => $this->get_facebook_user_id($userdata),
			'member_firstname'=>$userdata->getFirstName(),
			'member_lastname'=>$userdata->getLastName(),
			'member_dob'=>(empty($birthday) ? "" : $birthday->format('Y-m-d')),
			'member_since'=>date('Y-m-d'),
			'member_email'=>$userdata->getEmail(),
			'approved'=>1,
			'member_picture' => $userdata->getPicture()->getUrl(),
			'activation_status' => 'Validated',
		];
	}
}