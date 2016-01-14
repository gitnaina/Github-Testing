<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(7);
class UserTask extends CI_Controller {
	public $language ='';
	private $_username;
	private $_password;

	function __construct()
	{
		parent::__construct();
		$this->load->model('model_user','',true);
		$this->language = getLang();
		$this->lang->load('user',$this->language);
    }

	function login($chanel=''){

		$this->_loginsetup($chanel);

	}


    private function _loginsetup($chanel){
        
        switch($chanel){
        	case 'fblogin'   : 	$this->_fblogin();		break;
			case 'fwlogin'   : 	$this->_fwlogin();		break;
        	case 'twlogin'   : 	$this->_twlogin();		break;
        	case 'gpluslogin': 	$this->_gpluslogin();	break;
        	default   		 :  $this->_defaultlogin(); break;
       	 }
        
    }
	
	private function _fwlogin(){
	
		session_start();
		
		//fiware application
		$fiwareClientId = $this->config->item( 'fiwareClientId' );
		$fiwareSecret = $this->config->item( 'fiwareSecret' );

		$fwconfig['appid' ]     = $fiwareClientId;
		$fwconfig['secret']     = $fiwareSecret;
		$fwconfig['baseurl']    = BASEURL;

		//fiware user uid
		$user            =   null; 
		$code = $this->input->get('code');
		
		/*-----------------
			IF FIWARE CODE
		-----------------*/		
				
	   if(empty($code)) {

			//$_SESSION['state'] = md5(uniqid(rand(), TRUE)); // CSRF protection
	   		$rand = md5(uniqid(rand(), TRUE));
			$this->session->set_userdata('state',$rand);
			$dialog_url = "https://account.lab.fiware.org/oauth2/authorize?response_type=code&client_id=" 
			. $fwconfig['appid'] . "&redirect_uri=" . urlencode(BASEURL.'userTask/login/fwlogin') . "&state="
			. $rand;
			//redirect($dialog_url );

			 echo("<script> top.location.href='" . $dialog_url . "'</script>");
	   }
		
		/*--------------------------------
			GET ACCESS TOKEN FOR FiWARE USER
		---------------------------------*/
		
		$sessionState=$this->session->userdata('state');
		$requestState=$this->input->get('state');
		if($sessionState && ($sessionState === $requestState)){
    		
			$url="https://account.lab.fiware.org/oauth2/token";
			$params = array();
			$params["client_id"] = $fwconfig['appid'];
			$params["client_secret"] = $fwconfig['secret'];
			$params["grant_type"] = "authorization_code";
			$params["redirect_uri"] = urlencode(BASEURL.'userTask/login/fwlogin');
			$params["code"] = $code;
			$fields_string = "";
			//url-ify the data for the POST
			foreach ($params as $key => $value) {
				$fields_string .= $key . '=' . $value . '&';
			}
			rtrim($fields_string, '&');
			//open connection
			$ch = curl_init();
			//set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $url);
			//set basic authentication header
			curl_setopt($ch, CURLOPT_USERPWD, $fwconfig['appid'] . ":" . $fwconfig['secret']);
			curl_setopt($ch, CURLOPT_POST, count($params));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			/* Tell cURL NOT to return the headers */
			curl_setopt($ch, CURLOPT_HEADER, false);
			/* Execute cURL, Return Data */
			$data = curl_exec($ch);
			
			echo $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			
			curl_close($ch);
			/* 200 Response! */
			if ($status != 200) {
				$data = false;
			}
			
			$data=json_decode($data,true);
			
			
     		$this->session->set_userdata('access_token',$data['access_token']); 
     		/*----------* SAVE FIWARE USER DATA *----------*/

			$result = $this->model_user->saveFiwareUser($data['access_token']);
			
			if($result['status']){
 				$this->_saveUserSession($result['userData']);			
			} else{
				$this->messages->setMessage($result['message'],'alert alert-warning');
			}
   		}
   		else {
     		echo("The state does not match. You may be a victim of CSRF.");
   		}
  
		echo "<script>window.opener.location.reload();window.close();</script>";
		die;
	
	}

	private function _defaultlogin(){

		$this->form_validation->set_rules('username', 'username', 'required|email');
		$this->form_validation->set_rules('passwd', 'Password', 'required');
		
		$return				= 	new	stdClass();
		$return->status 	= 	false;
		$return->message  	= 	lang('mess_wrong login');

		if ($this->form_validation->run() == FALSE){
			echo json_encode($return);			
		}
		else{
				
			$this->_username	=	$this->input->post('username');
			$this->_password	=	$this->input->post('passwd');
			$rememberMe			=	$this->input->post('rememberMe');

				$this->load->helper('cookie'); 
						if ($rememberMe=='set') { 
							$cookie = array(
		                    'name'   => 'username',
		                    'value'  => $this->_username,
		                    'expire' =>  86500,
		                    'secure' => false
				            );
			                $this->input->set_cookie($cookie); 

			                $cookie = array(
				                    'name'   => 'passwd',
				                    'value'  => $this->_password,
				                    'expire' =>  86500,
				                    'secure' => false
				            );
			                $this->input->set_cookie($cookie); 

			                 $cookie = array(
				                    'name'   => 'rememberMe',
				                    'value'  =>'set',
				                    'expire' =>  86500,
				                    'secure' => false
				            );
			                $this->input->set_cookie($cookie); 
							} else {
									$cookie = array(
				                    'name'   => 'username',
				                    'value'  => '',
				                    'expire' =>  86500,
				                    'secure' => false
				            );
			                $this->input->set_cookie($cookie); 

			                $cookie = array(
				                    'name'   => 'passwd',
				                    'value'  => '',
				                    'expire' =>  86500,
				                    'secure' => false
				            );
			                $this->input->set_cookie($cookie); 

			                 $cookie = array(
				                    'name'   => 'rememberMe',
				                    'value'  =>'',
				                    'expire' =>  86500,
				                    'secure' => false
				            );
			                $this->input->set_cookie($cookie); 
						}

    		$credentails	=	array('username'=>$this->_username,'password'=>$this->_password);
			$result			=	$this->model_user->login($credentails);
			
			if($result->status)	{

				$this->_saveUserSession($result);
				
				$taskInHold=$this->session->userdata('taskInHold');
				if($taskInHold){
				
					$taskName=$this->session->userdata('taskName');
					$taskData=$this->session->userdata('taskData');
					$result=$this->tablegrabber->finishTaskInHold($taskName, $taskData);
					
				}
				
			}

			echo json_encode($result);
		}
		
	}

	private function _twlogin(){

	}

	private function _gpluslogin(){

	}
	
	private function _fblogin(){
	
	
	}
	
	private function _saveUserSession($result){
			$this->session->set_userdata('userId',$result->userId);
			$this->session->set_userdata('loggedUser',$result);
			//$this->memcache->set('userInfo_'.$result->userId,$result);
	}

	
	
	
}

