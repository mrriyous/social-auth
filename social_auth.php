<?php
/**
* 	============================== ===========
*		SOCIAL AUTH , login #Facebook, #Twitter
* 	============================== ===========
*
* 	@version 	1.0
*	@author 	Alfi Syahri
* 	@link 		http://anjir.esy.es
*/

require_once(APPPATH."third_party/Socialauth/Facebook/autoload.php");
require_once(APPPATH."third_party/Socialauth/Twitter/inc/twitteroauth.php");

require_once(__DIR__."/provider/facebook.php");
require_once(__DIR__."/provider/Twitter.php");

class Social_auth
{	
	/**
	*  		@access using Facebook 
	*/

	use Facebook;

	private $fb_app_id = "";
	private $fb_app_secret = "";
	private $fb_graph_ver = "v2.5";

	private $facebook_config;
	protected $fb;

	/**
	*  		@access using Twitter 
	*/
	use Twitter;

	private $tw_consumer_key = "";
	private $tw_consumer_secret = "";
	private $tw_callback = ''; //example : http://example.com/login/social/twitter
	private $tw_config;
	protected $tw;

	function __construct($provider)
	{
		switch ($provider) {
			case 'facebook':
				$this->facebook_config = [
					'app_id' => $this->fb_app_id,
					'app_secret' => $this->fb_app_secret,
					'default_graph_version' => $this->fb_graph_ver
				];
				$this->fb = new Facebook\Facebook($this->facebook_config);
				break;
			case "twitter":
				$this->tw=new TwitterOauth($this->tw_consumer_key,$this->tw_consumer_secret);
				break;
		}	
	}
}
	