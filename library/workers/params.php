<?php
class params {
	
	function init() {
		$this->post = new stdClass;
		$this->get  = new stdClass;
		$this->cookie = new stdClass;
		$this->request = new stdClass;
		$this->files = new stdClass;
		$this->file =& $this->files;
		$this->isSpam = false;
		$this->spamScore = 0;
		
		foreach($_POST as $k=>$v) {
			$this->post->$k = $v;
		}
		
	    foreach($_GET as $k=>$v) {
			$this->get->$k = $v;
		}
		
		foreach($_COOKIE as $k=>$v) {
			$this->cookie->$k = $v;
		}
		
	    foreach($_REQUEST as $k=>$v) {
			$this->request->$k = $v;
		}
		
		if(!isset($_FILES)) {
			return;
		}
		
		foreach($_FILES as $k=>$v) {
			if(!filetype($v['tmp_name']) == 'file') { continue; }
			$this->file->$k = ObjectifyArray($v);
			$this->file->$k->isFlagged = false;
			$this->file->$k->warning = false;
			
			// Get Additional Information
			$exts = explode('.', $v['name']);
			$this->file->$k->ext = array_pop($exts);
			
			if($this->file->$k->ext == 'exe') {
				$this->file->$k->isFlagged = true;
				$this->file->$k->warning = 'Executable Program Found';
			}
			
			// Scan for Viruses if phpClamAV installed
			if(function_exists('cl_scanfile')) {
				$ret = cl_scanfile($v['tmp_file'], $virus);
				if($ret == CL_VIRUS) {
					$this->file->$k->tmp_name = false;
					$this->file->$k->isFlagged = true;
					$this->file->$k->error = 'File Quarantined';
					$this->file->$k->warning = 'Virus "' . $virus . '" found!';
					continue;
				}
			}
			
			// Check for Image Piggy-backing
			if(preg_match('/image/i', $v['type'])) {
				$img = @getimagesize($v['tmp_name']);
				$this->file->$k->isImage = (bool) $img;
				
				if($img) {
					$this->file->$k->width = $img[0];
					$this->file->$k->height = $img[1];
					
					$suggested_size = $img[0] * $img[1] * 6; // 6 bytes per pixel for RGB48
					if($suggested_size >= $v['size']) {
						$this->file->$k->isFlagged = true;
						$this->file->$k->warning = 'Probable Piggy-Back File on Image';
					}
				} else {
					// Claimed to be an image but isn't
					$this->file->$k->tmp_name = false;
					$this->file->$k->isFlagged = true;
					$this->file->$k->error = 'File Quarantined';
					$this->file->$k->warning = 'File Not Consistent with Declareed Type (image)';
				}
			}
		}
	}
	
	function files($key) {
		return $this->file($key);
	}
	
	function file($key) {
		return $this->file->$key;
	}
	
	function get($key) {
		return $this->get->$key;
	}
	
	
	function post($key) {
		return $this->post->$key;
	}
	
	
	function cookie($key) {
		return $this->cookie->$key;
	}
	
	function param($key) {
		return $this->request->$key;
	}
	
	
	function request($key) {
		return $this->request->$key;
	}
	
	
	function isPost() {
		return (bool) count($_POST);
	}

	
	function postClean($key) {
		return $this->_clean($_POST[$key]);
	}
	
	
	function getClean($key) {
		return $this->_clean($_GET[$key]);
	}
	
	
	function cookieClean($key) {
		return $this->_clean($_COOKIE[$key]);
	}
	
	
	function paramClean($key) {
		return $this->_clean($_REQUEST[$key]);
	}
	
	
	function requestClean($key) {
		return $this->_clean($_REQUEST[$key]);
	}
	
	
	function postFlagged($key) {
		return $this->_isFlagged($_POST[$key]);
	}
	
	
	function getFlagged($key) {
		return $this->_isFlagged($_GET[$key]);
	}
	
	
	function cookieFlagged($key) {
		return $this->_isFlagged($_COOKIE[$key]);
	}
	
	
	function paramFlagged($key) {
		return $this->_isFlagged($_REQUEST[$key]);
	}
	
	
	function requestFlagged($key) {
		return $this->_isFlagged($_REQUEST[$key]);
	}
	
	function filesFlagged($key) {
		return $this->file($key)->isFlagged;
	}
	
	function fileFlagged($key) {
		return $this->file($key)->isFlagged;
	}
	
	
	/**
	 * Provide Sanitized Value
	 * @param string $value
	 * @return string
	 */
	private function _clean($value) {
		if($this->isFlagged($value)) {
			
		}
		return $value;
	}
	
	
	/**
	 * Check if Value contains dangerous code
	 * @param string $value
	 * @return bool
	 */
	private function _isFlagged($value) {
		return false;
	}
	
}