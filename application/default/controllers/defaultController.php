<?php
class defaultController extends defaultModel {
       
	function init() {
	    
	}
	
    function defaultAction() {
        if(!$this->session->get('user')) {
            $this->redirect->url(array('default','default','login'));
            return;
        }

        if(!$this->session->get('user')->isAdmin) {
        	$this->redirect->url(array('customer','default','default'));
        	return;
        }
        
        $this->redirect->url(array('admin','default','default'));
        return;
    }
    
    function loginAction() {
    	if($this->params->post->register) {
    		$continue = true;
    		$error = array();
    		
    		if(empty($this->params->post->first_name)) {
    			$continue = false;
    			$error[] = 'Please fill in your first name';
    		}
    		
    		if(empty($this->params->post->last_name)) {
    			$continue = false;
    			$error[] = 'Please fill in your last name';
    		}
    		
    		if(empty($this->params->post->email)) {
    			$continue = false;
    			$error[] = 'Please fill in your email';
    		}
    		
    		if(!preg_match('/^([a-z0-9\.\-_]+)+(@[a-z0-9][a-z0-9\.\-]*)(\.[a-z]{2,6})+$/i', $this->params->post->email)) {
    			$continue = false;
    			$error[] = 'Please enter a valid email';
    		}
    		
    		if(empty($this->params->post->password)) {
    			$continue = false;
    			$error[] = 'Please create a password';
    		}
    		
    		if(empty($this->params->post->passwordconf)) {
    			$continue = false;
    			$error[] = 'Please fill confirm your password';
    		}
    		
    		if($this->params->post->password != $this->params->post->passwordconf) {
    			$continue = false;
    			$error[] = 'Passwords did not match';
    		}
    		
    		if(!empty($this->params->post->validator)) {
    			$continue = false;
    			$error[] = 'Failed Spam Test';
    		}
    		
    		$inDb = $this->db->table('customer')->select('customer_id')
    			->where('email = "?"', $this->params->post->email)
    			->fetchOne()
    			->count();
    			
    		if($inDb != 0) {
    			$continue = false;
    			$error[] = 'Email already registered';
    		}
    		
    		if(!$continue) {
    			$this->view->signup_error = implode('<br />', $error);
    			return;
    		}
    		
    		$this->layout->view = 'signup';
    	}
    	
    	
    	// Add a User
    	if($this->params->post->adduser) {
    		$cdata = array(
    			'first_name' => $this->params->post->first_name,
    			'last_name' => $this->params->post->last_name,
    			'email' => $this->params->post->email,
    			'password' => $this->params->post->password,
    			'phone' => $this->params->post->phone,
    		);
    		
    		$id = $this->db->table('customer')->insert($cdata)->save()->insertId();
    		
    		$adata = array(
    			'customer_id' => $id,
    			'to_name' => $this->params->post->ship_to,
    			'business_name' => $this->params->post->business,
    			'street' => $this->params->post->street,
    			'street_cont' => $this->params->post->street_cont,
    			'city' => $this->params->post->city,
    			'state' => $this->params->post->state,
    			'zip' => $this->params->post->zip,
    			'country' => $this->params->post->country,
    		);
    		
    		$aid = $this->db->table('address')->insert($adata)->save()->insertId();
    		
    		$cst = $id = $this->db->table('customer')->select()->where('customer_id = ?', $id)->fetchOne();
    		$cst->address_id = $aid;
    		$cst->save();
    		
    		$this->ext('email')->to($cst->email, $cst->first_name)
		 		   ->from('gobos@nlfxpro.com', 'NLFX Pro')
				   ->subject('Your New NLFX Pro Gobos Account')
				   ->template('user_signup')
				   ->data($cst)
				   ->send();
				   
		    $this->validateAction();
    	}
    }
    
    function logoutAction() {
    	$this->session->delete('user');
    	$this->redirect->url(0);
    }
    
    function validateAction() {
    	if($this->params->post->email && $this->params->post->password) {

		$this->session->set('login_error',true);
		
    		$user = $this->db->table('customer')
    						->select()
    						->where('email = "?" AND password = "?"',$this->params->post->email, $this->params->post->password)
    						->fetchOne();

    		if($user->count() == 1) {
    			$user->last_ip = $_SERVER['REMOTE_ADDR'];
    			$user->user_agent = $_SERVER['HTTP_USER_AGENT'];
    			$user->last_login = date("Y-d-d H:i");
    			$user->save();
    			
    			$user_sess = new stdClass();
    			$user_sess->customer_id = $user->customer_id;
    			$user_sess->first_name = $user->first_name;
    			$user_sess->last_name = $user->last_name;
    			$user_sess->email = $user->email;
    			$user_sess->phone = $user->phone;
    			$user_sess->is_admin = (bool)$user->is_admin;
    			$this->session->set('user',$user_sess);
			
			$this->session->delete('login_error');
    		}
    	
    		$this->redirect->url(array('customer','default','default'));
    		return;
    	}
		
    }
    
    
    function forgotAction() {
		if($this->params->post->email) {
			$r = $this->findPassword($this->params->post->email);
			if($r) {
				$this->view->success = 'Your account password has been emailed to you';
				return;
			}
			$this->view->error = 'Account could not be found';
		}
	}
}
