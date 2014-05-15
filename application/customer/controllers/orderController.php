<?php
class orderController extends orderModel {
       
	function init() {
		if(!$this->session->get('user')) {
			$this->redirect->url(0);
		}   
	}
	
	function defaultAction() {
		// Create a New Order
		$this->layout->title('My Orders');
		$this->view->myorders = $this->getOrders();
	}
	
	function viewAction() {
		if(!$this->params->request->id) {
			$this->redirect->url(array('customer','default','default'));
		}
		
		$this->view->info = $this->getOrderById($this->params->request->id);
		
		if($this->params->post->save_note) {
			$this->addNote($this->params->post->id,$this->params->post->note);
			
			if($this->params->post->needresponse) {
				$form = $this->db->table('order')->select('status')->where('order_id = ?', $this->view->info->order_id)->fetchOne();
				$form->status = 'awaiting nlfx response';
				$form->save();
				$this->view->info->status = $form->status;
				$this->addHistory($this->params->request->id, 'awaiting nlfx response', 'updated the order status to &quot;Awaiting NLFX Response&quot;');
			}
		}
		
		if($this->params->post->save_proof) {
			$form = $this->db->table('order')->select('status')->where('order_id = ?', $this->view->info->order_id)->fetchOne();
			if($this->params->post->accept == 'yes') {
				$this->addHistory($this->params->request->id, 'art approved', 'accepted the proof for production');
				$form->status = 'art approved';
				$form->save();
			} elseif(@file_exists('docroot/orders/proofs/'.$this->params->request->id.'.jpg')) {
				$this->addHistory($this->params->request->id, 'pending changes', 'did not approve the proof and is requesting changes');
				rename('docroot/orders/proofs/'.$this->params->request->id.'.jpg',
				'docroot/orders/proofs/'.$this->params->request->id.'_declined_'.time().'.jpg');
				$form->status = 'pending changes';
				$form->save();
			}
			
			$this->view->info->status = $form->status;
		}
		
		$this->view->notes = $this->getNotes($this->params->request->id);
		
		$this->layout->title('Order #'.$this->view->info->order_id.' - '.$this->view->info->event_name.' - '.ucfirst($this->view->info->status));
	}
	
	
	function createAction() {
		if($this->params->post->save) {
			
			if($this->params->post->address == 'new') {
				$address_data = array(
					'customer_id' => $this->session->get('user')->customer_id,
					'to_name'     => $this->params->post->address_name,
					'business_name'     => $this->params->post->address_company,
					'street'     => $this->params->post->address_street,
					'street_cont'     => $this->params->post->address_street2,
					'city'     => $this->params->post->address_city,
					'state'     => $this->params->post->address_state,
					'zip'     => $this->params->post->address_zip,
					'country'     => $this->params->post->address_country
				);
				
				$address = $this->db->table('address')->insert($address_data);
				$address->save();
				$this->params->post->address = $address->insertId();
			}
										
			$gobo_image = null;
			
			if($this->params->post->gobo_design != 'D0') {
				$gobo_image = 'docroot/images/designs/'.$this->params->post->gobo_design.'.jpg';
			}
			
			$data = array(
				'customer_id' => $this->session->get('user')->customer_id,
				'shipping_id' => $this->params->post->address,
				'event_name'  => $this->params->post->event_name,
				'gobo_type'  => $this->params->post->gobo_type,
				'gobo_fixture'  => $this->params->post->gobo_fixture,
				'gobo_text'  => $this->params->post->gobo_text,
				'gobo_font'  => $this->params->post->gobo_font,
				'gobo_image'  => $gobo_image,
				'gobo_instructions'  => $this->params->post->gobo_instructions,
				'quantity'  => $this->params->post->qty,
				'deliver_by'  => strtotime($this->params->post->deliver_by),
				'est_price'   => $this->params->post->est_price,
				'status'  => 'queued',
			);
			
			$dba = $this->addOrder($data);
			if($dba->saved) {
				$db = $this->db->table('order');
				$db = $db->select()->where('order_id = ?', $dba->insertId())->fetchOne();
				
				if($this->params->file->gobo_image && is_null($gobo_image)) {
					$gobo_image = 'docroot/orders/uploads/'.$db->order_id.'.'.strtolower($this->params->file->gobo_image->ext);
					move_uploaded_file($this->params->file->gobo_image->tmp_name, $gobo_image);
					$db->gobo_image = $gobo_image;
					$db->save();
				}
				
				$this->redirect->url('customer','order','view','id='.$db->order_id);
			}
			
		}
		
		$this->view->addresses = $this->getAddresses();
	}
	
	
}