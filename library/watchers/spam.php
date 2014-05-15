<?php
class spamWatcher extends BaseWatcher {
	
	function init($runtime) {
		$this->config = $runtime->config->watchers;
		if($this->config->askimet && $_POST) {
			// Check post data against askimet
			$vals = array();
			foreach($_POST as $itm) {
				$k = strlen($itm);
				$vals[$k] = array($k,$itm);
			}
			ksort($vals);
			$testinf = array_pop($vals);
			if($testinf[0] < 30) {
				// not enough data
				return;
			}
			
			$result = $this->test($testinf[1]);
			
			$score = $runtime->session->get('spamWatcher');
			$score = $score + $result;
			
			if($score > 4) {
				// Flagged for Spam 5 Times in One Session
				$this->ban($runtime,'Repeatedly flagged for Spam');
			}
		}
		
	}
	
	function test($str) {
		
	}
	
}