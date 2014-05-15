<?php
class orderController extends orderModel {
       
	function init() {
		if(!$this->session->get('user')->is_admin) {
			$this->redirect->url(0);
		}   
	}

	function defaultAction() {
		// Create a New Order
		$this->layout->title('My Orders');
		$this->view->myorders = $this->getOrders();
	}
	
	function viewAction() {
		$this->view->status = self::status_codes();
		
		if(!$this->params->request->id) {
			$this->redirect->url(array('customer','default','default'));
		}
		
		$this->view->info = $this->getOrderById($this->params->request->id);
		
		if($this->params->post->save_status) {
			$this->addHistory($this->params->request->id, 'proof accepted', 'updated the status of your order to "'.ucwords($this->params->post->status).'"');
			$this->view->info->status = $this->params->post->status;
			$this->view->info->save();
		}
		
		if($this->params->post->save_tracking) {
			$this->addHistory($this->params->request->id, 'shipped', 'has shipped your order');
			$this->view->info->tracking_number = $this->params->post->tracking;
			$this->view->info->status = 'shipped';
			$this->view->info->save();
			
		}
		
		if($this->params->post->save_po) {
			$this->view->info->purchase_order = $this->params->post->po;
			$this->view->info->save();
		}
		
		if($this->params->post->upload_proof) {
			$this->addHistory($this->params->request->id, 'proof', 'uploaded a proof for review');
			$this->view->info->status = 'proof';
			$this->view->info->save();
			move_uploaded_file($this->params->file->proof->tmp_name, 'docroot/orders/proofs/'.$this->view->info->order_id.'.'.$this->params->file->proof->ext);
		}
		
		if($this->params->post->save_note) {
			$this->addNote($this->params->post->id,$this->params->post->note, $this->params->post->visible);
		}
		
		if($this->params->post->save_proof) {
			if($this->params->post->accept == 'yes') {
				$this->addHistory($this->params->request->id, 'proof accepted', 'accepted the proof for production');
				$this->view->info->status = 'proof accepted';
				$this->view->info->save();
			} elseif(@file_exists('docroot/orders/proofs/'.$this->params->request->id.'.jpg')) {
				rename('docroot/orders/proofs/'.$this->params->request->id.'.jpg',
				'docroot/orders/proofs/'.$this->params->request->id.'_declined_'.time().'.jpg');
				$this->view->info->status = 'pending';
				$this->view->info->save();
			}
		}
		
		$this->view->notes = $this->getNotes($this->params->request->id);
		
		$this->layout->title('Order #'.$this->view->info->order_id.' - '.$this->view->info->event_name.' - '.ucfirst($this->view->info->status));
	}
	
	function createAction() {
		$this->view->status = self::status_codes();
		$this->view->customers = $this->getCustomers();
		
		if($this->params->post->save) {
			
			$address = $this->db->table('address')->select('address_id')
								->where('customer_id = ?', $this->params->post->customer_id)
								->orderBy('address_id DESC')
								->fetchOne();
										
			$gobo_image = null;
			
			$data = array(
				'customer_id' => $this->params->post->customer_id,
				'shipping_id' => $address->address_id,
				'event_name'  => $this->params->post->event_name,
				'gobo_type'  => $this->params->post->gobo_type,
				'gobo_fixture'  => $this->params->post->gobo_fixture,
				'gobo_text'  => $this->params->post->gobo_text,
				'gobo_image'  => $gobo_image,
				'gobo_instructions'  => $this->params->post->gobo_instructions,
				'quantity'  => $this->params->post->qty,
				'deliver_by'  => strtotime($this->params->post->deliver_by),
				'purchase_order'  => $this->params->post->po,
				'status'  => $this->params->post->status,
			);
			
			$dba = $this->addOrder($data);
			if($dba->saved) {
				$db = $this->db->table('order');
				$db = $db->select()->where('order_id = ?', $dba->insertId())->fetchOne();
				
				if($this->params->file->gobo_image) {
					$gobo_image = 'docroot/orders/uploads/'.$db->order_id.'.'.$this->params->file->gobo_image->ext;
					move_uploaded_file($this->params->file->gobo_image->tmp_name, $gobo_image);
					$db->gobo_image = $gobo_image;
					$db->save();
				}
				
				$this->redirect->url('admin','order','view','id='.$db->order_id);
			}
		}
	}
}