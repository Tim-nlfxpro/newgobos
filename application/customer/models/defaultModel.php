<?php
class defaultModel extends BaseModel {
    
	function getOrders($status=null,$orderby=null) {
		$orders = $this->db->table('order')
			->select('*')
			->where('customer_id = ?', $this->session->get('user')->customer_id);
			
		if($status) {
			$orders->where('status = "?"', $status);
		}
		
		if($orderby) {
			$orders->orderby($orderby);
		}
		
		$orders->orderby('order_id DESC');
		
		return $orders->fetch();
	}
	
}
