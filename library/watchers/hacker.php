<?php
class hackerWatcher extends BaseWatcher {
	
	function init($runtime) {
		$this->config = $runtime->config->watchers;
		
		
	}
	
}