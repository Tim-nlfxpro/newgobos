<?php
class customerController extends customerModel {
	
	function createAction() {
		if($this->params->post->save) {
			$exists = $this->db->table('customer')->select()->where('email = "?"', $this->params->post->email)->fetchOne();
			if($exists->count() > 0) {
				$this->view->error = 'This email address already exists';
				return;
			}
			
			$data = array(
				'first_name' => $this->params->post->first_name,
				'last_name' => $this->params->post->last_name,
				'email' => $this->params->post->email,
				'phone' => $this->params->post->phone,
				'password' => $this->_genPassword(),
				'is_admin' => $this->params->post->is_admin,
			);
			
			$db = $this->db->table('customer')
					 ->insert($data)
					 ->save();
					 
			$customer_id = $db->insertId();
					 
			$address = array(
				'customer_id' => $customer_id,
				'to_name'  => $this->params->post->to_name,
				'business_name'  => $this->params->post->business_name,
				'street'  => $this->params->post->street,
				'street_cont'  => $this->params->post->street2,
				'city'  => $this->params->post->city,
				'state'  => $this->params->post->state,
				'zip'  => $this->params->post->zip,
				'country'  => $this->params->post->country,
			);
			
			$adb = $this->db->table('address')
							->insert($address)
							->save();

			$db = $this->db->table('customer')->select()->where('customer_id = ?', $customer_id)->fetchOne();
			$db->address_id = $adb->insertId();
			$db->save();
			
			$this->ext('email')->to($db->email, $db->first_name)
		 		   ->from('gobos@nlfxpro.com', 'NLFX Pro')
				   ->subject('Your New NLFX Pro Gobos Account')
				   ->template('user_signup')
				   ->data($db)
				   ->send();
			
			$this->redirect->url('admin', 'order', 'create');
		}
		
	}
	
	
	private function _genPassword() {
		$chars = "abcdefghijkmnopqrstuvwxyz023456789"; 
	    srand((double)microtime()*1000000); 
	    $i = 0; 
	    $pass = '' ; 
	
	    while ($i <= 7) { 
	        $num = rand() % 33; 
	        $tmp = substr($chars, $num, 1); 
	        $pass = $pass . $tmp; 
	        $i++; 
	    } 
	
	    return $pass; 
	}
	
	/*
	function testAction() {
		$data = new stdClass();
		$data->email = 'me@mikestowe.com';
		$data->password = 'pass';
		$data->first_name = 'Mike';
		echo 'sending...<br />';
		
		$this->ext('email')->to($data->email, $data->first_name)
		 		   ->from('gobos@nlfxpro.com', 'NLFX Pro')
				   ->subject('Your New Gobo Factory Account')
				   ->template('user_signup')
				   ->data($data)
				   ->send();
		
		die('success');
	}
	*/
	
	
}