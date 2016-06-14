<?php

require_once(__DIR__."../social_auth.php");

class LoginController extends CI_Controller
{
	
	public function login_using_social($provider)
	{  
		if($this->session->has_userdata('member') )
		{
			redirect(base_url());
		}

		$class=new Social_auth($provider);
		$method="login_".$provider;	
		
		if(!method_exists($class, $method))
		{
			echo "Provider doesn't exits";
			exit();
		}

		$class->$method();
	}

	public function login_social_finish(){
		if(empty($this->input->get("pv")) )
		{	
			echo "Unkown error";
			exit();
		}
		
		$provider = trim(strtolower($this->input->get("pv")));
		$class=new Social_auth($provider);
		$method = "login_finish_".$provider;
		
		if(!method_exists($class, $method))
		{
			echo "Provider doesn't exits";
			exit();
		}
		$class->$method();
	}

	public function login_social_retrieve()
	{
		if(empty($this->input->get("pv")) )
		{	
			redirect(base_url());
		}

		$provider=trim(strtolower($this->input->get("pv")));
		$class=new Social_auth($provider);

		$access_token = $_SESSION[$provider."_access_token"];
		if(empty($access_token))
		{
			redirect(base_url());
		}

		$method="get_".$provider."_user_data";
		$userdata = $class->$method($access_token);

		$method2="get_".$provider."_user_id";
		$user_id = $class->$method2($userdata);

		$member=$this->Member_model->findBySocial($user_id,$provider);

		$method3="generate_".$provider."_user_data";
		
		$memberData = $class->$method3($userdata,$access_token);
		
		if(empty($member))
		{	
			$member_id=$this->Member_model->InsertMember($memberData);
			$member = $this->Member_model->show_member($member_id);
		}else{
			$this->Member_model->edit_member($member->member_id,$memberData);
		}

		$this->session->set_userdata([
			'member' => $member->member_id,
		]);

		$redirect=(!empty($this->session->userdata('last_suri')) ? $this->session->userdata('last_suri') : base_url() );
		redirect($redirect);
	}
}