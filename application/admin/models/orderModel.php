<?php
class orderModel extends BaseModel {
    
	static function status_codes() {
		return array(
			'queued',
			'order placed',
			'in process', 
			'awaiting customer response', 
			'awaiting nlfx response', 
			'proof', 
			'pending changes', 
			'art approved', 
			'shipped', 
			'cancelled', 
		);
	}
	
	function getCustomers() {
		return $this->db->table('customer')
					->select('customer_id, first_name, last_name, email')
					->orderBy('last_name, first_name, email')
					->fetchAll();
	}
	
	function getOrderInfo($orderId) {
		$order = new stdClass;
		$order->info  = $this->getOrderById($orderId);
		$order->notes = $this->getNotes($orderId);
		return $order;
	}
	
	
	function getOrderById($orderId) {
		return $this->db->table('order','o')
			->select('*')
			->join('address','a','o.shipping_id = a.address_id')
			->where('order_id = ?', $orderId)
			->fetchOne();
	}
	
	
	function getNotes($orderId) {
		return $this->db->table('history')
						->select('*')
						->join('customer','c','customer_id')
						->where('order_id = ?', $orderId)
						->orderby('datetime DESC')
						->fetchAll();
	}
	
	
	function addOrder($data) {
		$order = $this->db->table('order')
					->insert($data);
					
		$order->save();
			
		$this->addHistory($order->insertId(), 'new order', 'placed order #'.$order->insertId());
		
		$cb = $this->db->table('customer')->select()->where('customer_id = ?', $data['customer_id'])->fetchOne();
		
		$this->ext('email',true)->to($cb->email, $cb->first_name)
					   ->from('gobos@nlfxpro.com', 'NLFX Pro')
					   ->subject('Your Gobo Order')
					   ->template('customer_order_added')
					   ->data($order->data)
					   ->send();
					   
		$this->ext('email',true)->to('gobos@nlfxpro.com', 'NLFX Pro')
					   ->from('gobos@nlfxpro.com', 'NLFX Pro')
					   ->subject('New Gobo Order')
					   ->template('admin_order_added')
					   ->data($order->data)
					   ->send();
		
		return $order;
	}
	
	
	function addNote($orderId, $note, $visible) {
		$history = array(
			'customer_id' => $this->session->get('user')->customer_id,
			'order_id'    => $orderId,
			'type'        => 'note',
			'datetime'    => date('Y-m-d H:i:s'),
			'message'	  => $this->session->get('user')->first_name . ' added the following '.($visible ? '' : 'internal ').'note:<div class="'.($visible ? '' : 'hidden').'note">'.$note.'</div>',
			'visible_to_customer'    => ($visible ? 1 : 0)
		);
		
		$this->db->table('history')
			->insert($history)
			->save();
		
		if($visible) {	
			$cb = $this->db->table('order')
			       ->select()
			       ->join('customer','c','customer_id')
			       ->where('order_id = ?', $orderId)
			       ->fetchOne();
			       
			$this->ext('email')->cc('gobos@nlfxpro.com', 'NLFX Pro')
					   ->to($cb->email, $cb->first_name)
					   ->from('gobos@nlfxpro.com', 'NLFX Pro')
					   ->subject('Note added to Order')
					   ->template('admin_note_added')
					   ->data($note)
					   ->send();
		}
	}
	
	
	function addHistory($order_id,$type,$message) {
		$history = array(
			'customer_id' => $this->session->get('user')->customer_id,
			'order_id'    => $order_id,
			'type'        => $type,
			'datetime'    => date('Y-m-d H:i:s'),
			'message'	  => $this->session->get('user')->first_name . ' ' . $message,
			'visible_to_customer'    => 1
		);
		
		$this->db->table('history')
			->insert($history)
			->save();
			
		$cb = $this->db->table('order')
			       ->select()
			       ->join('customer','c','customer_id')
			       ->where('order_id = ?', $order_id)
			       ->fetchOne();
			       
		$this->ext('email')->cc('gobos@nlfxpro.com', 'NLFX Pro')
					   ->to($cb->email, $cb->first_name)
					   ->from('gobos@nlfxpro.com', 'NLFX Pro')
					   ->subject('Order Updated')
					   ->template('history_updated')
					   ->data($history['message'])
					   ->send();
	}
	
}
