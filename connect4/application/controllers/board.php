<?php

class Board extends CI_Controller {
     
    function __construct() {
    		// Call the Controller constructor
	    	parent::__construct();
	    	session_start();
    } 
          
    public function _remap($method, $params = array()) {
	    	// enforce access control to protected functions	
    		
    		if (!isset($_SESSION['user']))
   			redirect('account/loginForm', 'refresh'); //Then we redirect to the index page again
 	    	
	    	return call_user_func_array(array($this, $method), $params);
    }

    
    
	function index() {
		$user = $_SESSION['user'];
    		    	
	    	$this->load->model('user_model');
	    	$this->load->model('invite_model');
	    	$this->load->model('match_model');
	    	
	    	$user = $this->user_model->get($user->login);
	    	$invite = $this->invite_model->get($user->invite_id);
	    	
	    	$match = $this->match_model->get($user->match_id);
	    	$otherUser = $this->user_model->getFromId($match->user1_id);
	    	$player = $otherUser->login;
	    	
	    	if ($user->user_status_id == User::WAITING) {
	    		$invite = $this->invite_model->get($user->invite_id);
	    		$otherUser = $this->user_model->getFromId($invite->user2_id);
	    	}
	    	else if ($user->user_status_id == User::PLAYING) {
	    		$match = $this->match_model->get($user->match_id);
	    		if ($match->user1_id == $user->id){
	    			$player = $user->login;
	    			$otherUser = $this->user_model->getFromId($match->user2_id);
	    		}else{
	    			$otherUser = $this->user_model->getFromId($match->user1_id);
	    			$player = $otherUser->login;
	    		}
	    			
	    	}
	    	
	    	$data['user']=$user;
	    	$data['otherUser']=$otherUser;
	    	$data['player']=$player;
	    	
	    	switch($user->user_status_id) {
	    		case User::PLAYING:	
	    			$data['status'] = 'playing';
	    			break;
	    		case User::WAITING:
	    			$data['status'] = 'waiting';
	    			break;
	    	}
	    	
		$this->load->view('match/board',$data);
    }
    

 	function postMsg() {
 		$this->load->library('form_validation');
 		$this->form_validation->set_rules('msg', 'Message', 'required');
 		
 		if ($this->form_validation->run() == TRUE) {
 			$this->load->model('user_model');
 			$this->load->model('match_model');
 		//	redirect('board/index', 'refresh');
 			

 			$user = $_SESSION['user'];
 			 
 			$user = $this->user_model->getExclusive($user->login);
 			if ($user->user_status_id != User::PLAYING) {	
				$errormsg="Not in PLAYING state";
 				goto error;
 			}
 			
 			$match = $this->match_model->get($user->match_id);			
 			
 			$msg = $this->input->post('msg');
 			
 			if ($match->user1_id == $user->id)  {
 				$msg = $match->u1_msg == ''? $msg :  $match->u1_msg . "\n" . $msg;
 				$this->match_model->updateMsgU1($match->id, $msg);
 			}
 			else {
 				$msg = $match->u2_msg == ''? $msg :  $match->u2_msg . "\n" . $msg;
 				$this->match_model->updateMsgU2($match->id, $msg);
 			}
 				
 			echo json_encode(array('status'=>'success'));
 			 
 			return;
 		}
		
 		$errormsg="Missing argument";
 		
		error:
			echo json_encode(array('status'=>'failure','message'=>$errormsg));
 	}
 
	function getMsg() {
 		$this->load->model('user_model');
 		$this->load->model('match_model');
 			
 		$user = $_SESSION['user'];
 		 
 		$user = $this->user_model->get($user->login);
 		if ($user->user_status_id != User::PLAYING) {	
 			$errormsg="Not in PLAYING state";
 			goto error;
 		}
 		// start transactional mode  
 		$this->db->trans_begin();
 			
 		$match = $this->match_model->getExclusive($user->match_id);			
 			
 		if ($match->user1_id == $user->id) {
			$msg = $match->u2_msg;
 			$this->match_model->updateMsgU2($match->id,"");
 		}
 		else {
 			$msg = $match->u1_msg;
 			$this->match_model->updateMsgU1($match->id,"");
 		}

 		if ($this->db->trans_status() === FALSE) {
 			$errormsg = "Transaction error";
 			goto transactionerror;
 		}
 		
 		// if all went well commit changes
 		$this->db->trans_commit();
 		
 		echo json_encode(array('status'=>'success','message'=>$msg));
		return;
		
		transactionerror:
		$this->db->trans_rollback();
		
		error:
		echo json_encode(array('status'=>'failure','message'=>$errormsg));
 	}
 	
 	function getMove() {
 		$this->load->model('user_model');
 		$this->load->model('match_model');
 	
 		$user = $_SESSION['user'];
 	
 		$user = $this->user_model->get($user->login);
 		if ($user->user_status_id != User::PLAYING) {
 			$errormsg="Not in PLAYING state";
 			goto error;
 		}
 		// start transactional mode
 		$this->db->trans_begin();
 	
 		$match = $this->match_model->getExclusive($user->match_id);
 		
 		//diserialize
 		$blob = $match->board_state;
 		$tState = unserialize(base64_decode($blob));
 		
 		if ($this->db->trans_status() === FALSE) {
 			$errormsg = "Transaction error";
 			goto transactionerror;
 		}
 			
 		// if all went well commit changes
 		$this->db->trans_commit();
 			
 		echo json_encode(array('status'=>'success','tState'=>$tState));
 		return;
 	
 		transactionerror:
 		$this->db->trans_rollback();
 	
 		error:
 		echo json_encode(array('status'=>'failure','message'=>$errormsg));
 	}
 	
 	
 /*	
 	function SendGame()
 	{
 		$user = $_SESSION['user'];
 		$this->load->model('user_model');
 		$this->load->model('match_model');
 		$game = $this->input->post('name');
 	
 		$user = $this->user_model->getExclusive($user->login);
 		if ($user->user_status_id != User::PLAYING) {
 			$errormsg="Not in PLAYING state";
 			goto error;
 		}
 	
 		$match = $this->match_model->get($user->match_id);
 		$this->match_model->updateBoard($match->id, serialize($game));
 	
 		return;
 		error:
 		echo json_encode(array('status'=>'failure'));
 	}
*/
 	function SendGame(){
//  		if(isset($_POST['board'])) { 			
//  			$json = $_POST['board'];
 			$this->load->model('match_model');
 			$this->load->model('user_model');
 			$user = $this->user_model->get($_POST['user']);
			$ball = $_POST['move_place'];
 			$this->match_model->updateBoard($user->match_id, serialize($ball)); 			
 	}
 	
 	function GetGame(){
 		$this->load->model('match_model');
 		$this->load->model('user_model');
//  		$user->match_id = 11;
 		$user = $this->user_model->get($_GET['user']);
 		if(!empty($this->match_model->getBlob($user->match_id)->board_state))
 			$ball = unserialize($this->match_model->getBlob($user->match_id)->board_state);
 		else
 			$ball = "none";
 		echo json_encode(array("ball" => $ball));
 	}
 /*
  
 	function GetGame()
 	{
 		$user = $_SESSION['user'];
 		$this->load->model('user_model');
 		$this->load->model('match_model');
 	
 	
 		$user = $this->user_model->getExclusive($user->login);
 		if ($user->user_status_id != User::PLAYING) {
 			$errormsg="Not in PLAYING state";
 			goto error;
 		}
 	
 		$match = $this->match_model->get($user->match_id);
 		$ret = $this->match_model->getBlob($match->id);
 	
 		$obj = unserialize($ret->board_state);
 		echo $obj;
 		return;
 	
 		error:
 		echo json_encode(array('status'=>'failure','message'=>$errormsg));
 	
 	}	
 	*/
 }
 
 