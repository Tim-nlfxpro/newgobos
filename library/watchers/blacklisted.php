<?php
class blacklistedWatcher extends BaseWatcher {
	
	function init($runtime) {
		$this->config = $runtime->config->watchers;
		
		if(file_exists($this->config->bannedips)) {
			$bannedips = @file_get_contents($this->config->bannedips);
			if(preg_match('/'.$runtime->visitor->getIP().'/',$bannedips)) {
				$runtime->error->fatal('Your IP has been banned.');
			}
		}
	}
	
}