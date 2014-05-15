<?php
class defaultModel extends BaseModel {
    
	function getOrders($status=null,$orderby=null) {
		
		require_once('application/admin/models/orderModel.php');
		$this->view->status = OrderModel::status_codes();
		
		$orders = $this->db->table('order')
			->select('*')
			->join('customer','c','customer_id');
			
		$and = '';
			
		if($this->params->post->first_name && !empty($this->params->post->first_name)) {
			$orders->where($and.'first_name LIKE "%?%"', $this->params->post->first_name);
			$and = 'AND ';
		}
		
		if($this->params->post->last_name && !empty($this->params->post->last_name)) {
			$orders->where($and.'last_name LIKE "%?%"', $this->params->post->last_name);
			$and = 'AND ';
		}
		
		if($this->params->post->email && !empty($this->params->post->email)) {
			$orders->where($and.'email LIKE "%?%"', $this->params->post->email);
			$and = 'AND ';
		}
			
		if($this->params->post->status) {
			$orders->where($and.'status = "?"', $this->params->post->status);
		}
		
		if($this->params->get->orderby) {
			$orders->orderby($this->params->get->orderby);
		} else {
			$orders->orderby('order_id DESC');
		}
		
		return $orders->fetch();
	}
	
}
