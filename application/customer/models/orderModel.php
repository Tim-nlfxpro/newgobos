<?php
class orderModel extends BaseModel {
    
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
			->where('o.customer_id = ?', $this->session->get('user')->customer_id)
			->andwhere('order_id = ?', $orderId)
			->fetchOne();
	}
	
	
	function getNotes($orderId) {
		return $this->db->table('history')
						->select('*')
						->join('customer','c','customer_id')
						->where('order_id = ?', $orderId)
						->andWhere('visible_to_customer = ?', 1)
						->orderby('datetime DESC')
						->fetchAll();
	}
	
	
	function addOrder($data) {
		$order = $this->db->table('order')
					->insert($data)
					->save();
			
		$this->addHistory($order->insertId(), 'new order', 'placed order #'.$order->insertId());
		
		$data['order_id'] = $order->insertId();
			
		$this->ext('email',true)->to($this->session->get('user')->email, $this->session->get('user')->first_name)
					   ->from('gobos@nlfxpro.com', 'NLFX Pro')
					   ->subject('Your Gobo Order')
					   ->template('customer_order_added')
					   ->data($data)
					   ->send();
					   
		$this->ext('email',true)->to('gobos@nlfxpro.com', 'NLFX Pro')
					   ->from('gobos@nlfxpro.com', 'NLFX Pro')
					   ->subject('New Gobo Order')
					   ->template('admin_order_added')
					   ->data($data)
					   ->send();
					   
		return $order;
	}
	
	
	function addNote($orderId, $note) {
		$history = array(
			'customer_id' => $this->session->get('user')->customer_id,
			'order_id'    => $orderId,
			'type'        => 'note',
			'datetime'    => date('Y-m-d H:i:s'),
			'message'	  => $this->session->get('user')->first_name . ' added the following note:<div class="note">'.$note.'</div>',
			'visible_to_customer'    => 1
		);
		
		$this->db->table('history')
			->insert($history)
			->save();
			
		$data = new stdClass();
		$data->name = $this->session->get('user')->first_name;
		$data->order_id = $orderId;
		$data->note = $note;
		
		$this->ext('email')->to('gobos@nlfxpro.com', 'NLFX Pro')
				   ->cc($this->session->get('user')->email, $data->name)
				   ->from('gobos@nlfxpro.com', 'NLFX Pro')
				   ->subject('Note added to Order')
				   ->template('customer_note_added')
				   ->data($data)
				   ->send();
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
			
		$this->ext('email')->to('gobos@nlfxpro.com', 'NLFX Pro')
					   ->cc($this->session->get('user')->email, $this->session->get('user')->first_name)
					   ->from('gobos@nlfxpro.com', 'NLFX Pro')
					   ->subject('Order Updated')
					   ->template('history_updated')
					   ->data($history['message'])
					   ->send();
	}
	
	function getAddresses() {
		return $this->db->table('address')
						->select()
						->where('customer_id = ?', $this->session->get('user')->customer_id)
						->orderBy('address_id')
						->fetchAll();
	}
	
}
