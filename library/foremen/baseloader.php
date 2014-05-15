<?php
class BaseLoader {
	
	/**
	 * Base Loader Init
	 * 
	 * @param BootStrap $runtime
	 */
	public function baseInit($runtime) {
		$methods = get_class_methods($this);
		$this->runtime &= $runtime;
		
		foreach($methods as $method) {
			if(preg_match('/^_init/',$method)) {
				$this->runtime->{strtolower(str_replace('_init','',$method))} = $this->$method();
			}
		}
	}
	
	/**
	 * 
	 * Get Worker or Startup File
	 * @param string $filename
	 */
	protected function getFile($filename) {
		if(file_exists($this->runtime->config->directories->workers.'/'.$filename)) {
			require_once($this->runtime->config->directories->workers.'/'.$filename);
		} elseif (file_exists($this->runtime->config->directories->startup.'/'.$filename)) {
			require_once($this->runtime->config->directories->startup.'/'.$filename);
		} else {
			throw new Exception('Worker '.$filename.' does not exist.');
		}
	}
	
	/**
	 * 
	 * Get Watcher File
	 * @param string $filename
	 * @throws Exception
	 */
	protected function getWatcher($filename) {
		if(file_exists($this->runtime->config->directories->watchers.'/'.$filename)) {
			require_once($this->runtime->config->directories->watchers.'/'.$filename);
		} else {
			throw new Exception('Watcher '.$filename.' does not exist.');
		}
	}
	
	
	
	
	// Base/ Required Workers
	protected function _initError() {
		$this->getFile('error.php');
		return new error();
	}
	
	protected function _initLayout() {
		$this->getFile('layout.php');
		return new layout();
	}
	
	protected function _initParams() {
		$this->getFile('params.php');
		return new params();
	}
	
	protected function _initRedirect() {
		$this->getFile('redirect.php');
		return new redirect();
	}
	
	protected function _initSession() {
		$this->getFile('session.php');
		return new session();
	}
	
}
?>