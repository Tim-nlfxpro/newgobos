<?php 
class emailExtension {
	function init($runtime) {
		$this->runtime = $runtime;
		$this->to = array();
		$this->cc = array();
		$this->bcc = array();
		$this->subject = '';
		$this->message = false;
		$this->template = false;
		$this->data = new stdClass();
		$this->from = '';
		$this->type = 'text';
	}
	
	function to($email, $name = '') {
		$this->to[] = ($name != '' ? '"' . $name . '" ' : '') . '<' . $email . '>';
		return $this;
	}
	
	function cc($email, $name = '') {
		$this->cc[] = ($name != '' ? '"' . $name . '" ' : '') . '<' . $email . '>';
		return $this;
	}
	
	function bcc($email, $name = '') {
		$this->bcc[] = ($name != '' ? '"' . $name . '" ' : '') . '<' . $email . '>';
		return $this;
	}
	
	function from($email, $name = '') {
		$this->from = ($name != '' ? '"' . $name . '" ' : '') . '<' . $email . '>';
		return $this;
	}
	
	function subject($subject) {
		$this->subject = $subject;
		return $this;
	}
	
	function template($template) {
		$this->template = $template;
		return $this;
	}
	
	function message($message) {
		$this->message = $message;
		return $this;
	}
	
	function data($data) {
		$this->data = $data;
		return $this;
	}
	
	function send() {
		$mailto = '';
		$mailcc = '';
		$mailbcc = '';
		
		foreach($this->to as $to) {
			$mailto = $to.',';
		}
		
		foreach($this->cc as $cc) {
			$mailcc = $cc.',';
		}
		
		foreach($this->bcc as $bcc) {
			$mailbcc = $bcc.',';
		}
		
		$headers = 'FROM: '. $this->from . "\r\n";
		$headers .= 'CC: ' . $mailcc . "\r\n";
		$headers .= 'BCC: ' . $mailbcc . "\r\n";
		
		if($this->message) {
			$message = $this->message;
		} elseif ($this->template) {
			$data = $this->data;
			ob_start();
			require_once('email_templates/'.$this->template.'.phtml');
			$message = ob_get_contents();
			ob_end_clean();
		} else {
			return false;
		}
		
		return mail($mailto, $this->subject, $message, $headers);
		
	}
	
	/*
	function send($to, $from, $subject, $template, $data) {
		
		ob_start();
		require_once('email_templates/'.$template.'.phtml');
		$template = ob_get_contents();
		ob_end_clean();
		
		mail($to, $subject, $template, 'FROM: '.$from);
	
	}*/
	
}
