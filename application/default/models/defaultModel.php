<?php
class defaultModel extends BaseModel {
    
	function findPassword($email) {
		$r = $this->db->table('customer')
					  ->select('email, password, first_name')
					  ->where('email = "?"', $email)
					  ->fetchOne();
					  
		if($r->count() == 1) {
			$this->ext('email')->to($r->email, $r->first_name)
							   ->from('gobos@nlfxpro.com', 'NLFX Pro')
							   ->subject('Your Gobo Account Password')
							   ->template('forgot_password')
							   ->data($r)
							   ->send();
			return true;
		} else {
			return false;
		}
	}
	
}
