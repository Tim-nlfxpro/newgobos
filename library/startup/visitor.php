<?php
/**
 * Session Worker
 * @version 1.0
 * @author Michael Stowes
 */

/**
 * Session Class
 */
class visitor {
    
	/**
	 * Init
	 * @param BootStrap $runtime
	 * @return void
	 */
    function init($runtime) {
    	if(!$runtime->config->visitor->savegettdata) {
		return;
	}
	
    	// Setup Data Variable
        $this->data = $runtime->session->get('visitor_worker');
        if(!$this->data) {
        	$this->data = new stdClass;
        	
        	// Unique Visitor ID
        	//$this->data->visitorId = preg_replace('/[^0-9]/','',$_SERVER['REMOTE_ADDR']).microtime();
        	
        	// Visitor Start Time (first accessed site)
        	$this->data->starttime = time();
        }
        
        // Visitor Path
        $visitor['time'] = time();
        $visitor['path'] = $_SERVER['REQUEST_URI'];
        if($runtime->config->visitor->savegettdata) { $visitor['get'] = $_GET; }
    	if($runtime->config->visitor->saveposttdata) { $visitor['post'] = $_POST; }
    	if($runtime->config->visitor->savecookietdata) { $visitor['cookie'] = $_COOKIE; }
    	if($runtime->config->visitor->savesessiontdata) { $visitor['session'] = $_SESSION; }

        $this->data->path[] = $visitor;
        
        // Visitor End Time (time of last page load)
        $this->data->endtime = time();
        
        // Save Data to Session
        $runtime->session->set('visitor_worker',$this->data);
    }
    
    function getIP() {
    	return $_SERVER['REMOTE_ADDR'];
    }
    
    function getHost() {
    	return $_SERVER['REMOTE_HOST'];
    }
    
    function getPort() {
    	return $_SERVER['REMOTE_PORT'];
    }
    
    function getAgent() {
    	return $_SERVER['HTTP_USER_AGENT'];
    }
    
    function getEncoding() {
    	return $_SERVER['HTTP_ACCEPT_ENCODING'];
    }
    
    function getCharset() {
    	return $_SERVER['HTTP_ACCEPT_CHARSET'];
    }
    
    function getLanguage() {
    	return $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    }
    
    function getConnection() {
    	return $_SERVER['HTTP_CONNECTION'];
    }
    
    function getFirstActive() {
    	return $this->data->starttime;
    }
    
    function getLastActive() {
    	return $this->data->endtime;
    }
    
	function getCurrentPage() {
		$path = $this->data->path;
		$last = array_pop($path);
    	return $last['path'];
    }
    
	function getPath($details=true) {
    	if($details) {
    		return $this->data->path;
    	} else {
    		$path = array();
    		foreach($this->data->path as $array) {
    			$path[] = $array['path'];
    		}
    		return $path;
    	}
    }
    
    function getBrowser($array=false) {
    	return $this->_getBrowser($array);
    }
    
	function getBrowserAll($array=false) {
    	return $this->_getBrowser($array,true);
    }
    
    private function _getBrowser($array = false, $all = false)
	{	
		$known = array('msie', 'firefox', 'safari', 'webkit', 'opera', 'netscape',
		'konqueror', 'gecko');

		$agent = strtolower($agent ? $agent : $_SERVER['HTTP_USER_AGENT']);
		$pattern = '#(?<browser>' . join('|', $known) .
	    ')[/ ]+(?<version>[0-9]+(?:\.[0-9]+)?)#';
	
		if (!preg_match_all($pattern, $agent, $matches)) return array();
	
		$i = count($matches['browser'])-1;

		$alldata = array();
		
	    if($all) {
	    	$alldata = get_browser(null,true);
	    }
	    
	    $alldata['parent'] = $matches['browser'][$i].' '.$matches['version'][$i];
	    $alldata['browser'] = $matches['browser'][$i];
	    $alldata['version'] = $matches['version'][$i];
	    
	    if($array) {
	    	return $alldata;
	    }
	    
	    $result = new stdClass;
	    foreach($alldata as $k=>$v) {
	    	$result->$k = $v;
	    }
	    
	    return $v;
	}
	
	function isHttps() {
		return (!$_SERVER['https'] || $_SERVER['https'] == 'off'?false:true);
	}
	
	function isSmartPhone() {
		
	}
	
	function isiPad() {
		return preg_match('/ipad/i',$_SERVER['HTTP_USER_AGENT']);
	}
	
	function isiPhone() {
		return preg_match('/iphone|ipod/i',$_SERVER['HTTP_USER_AGENT']);
	}
	
	function gzip() {
		return preg_match('/gzip/i',$_SERVER['HTTP_ACCEPT_ENCODING']);
	}
	
	function keepAlive() {
		return preg_match('/keep\-alive/i',$_SERVER['HTTP_CONNECTION']);
	}
    
   

}
