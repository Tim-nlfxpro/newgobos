<?php
class defaultController extends defaultModel {
       
	function init() {
		if(!$this->session->get('user')) {
			$this->redirect->url(0);
		}   
	}
	
	function defaultAction() {
		$this->layout->title('My Orders');
		$this->view->myorders = $this->getOrders();
	}
}