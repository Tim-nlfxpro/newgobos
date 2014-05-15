<?php
/**
 * Redirect Worker
 * @version 1.0
 * @author Michael Stowes
 */

/**
 * Redirect Class
 */
class redirect {
    
	/*
	 * Init Function
	 * 
	 * @param Bootstrap $runtime
	 * @param bool      $new        Is created by self
	 * @return void
	 */
    function init($runtime, $new = false) {
    	$this->runtime = $runtime;
    	$this->code = false;
    	$this->headers = array();
    	$this->exit = true;
    	$this->new = $new;
    }
    
	/*
     * Set Exit - Default True
     * 
     * @param bool $exit
     * @return redirect
     */
    function setExit($exit = true) {
    	$temp = $this;
    	if(!$this->new) {
    		$temp = new redirect();
    		$temp->init($this->runtime, true);
    	}
    	
    	$temp->exit = $exit;
    	return $temp;
    }
    
	/*
     * Add Headers
     * 
     * @param string $code
     * @return redirect
     */
    function addHeader($header) {
    	$temp = $this;
    	if(!$this->new) {
    		$temp = new redirect();
    		$temp->init($this->runtime, true);
    	}
    	
    	$temp->headers[] = $header;
    	return $temp;
    }
    
    /*
     * Remove Headers
     * 
     * @param string $code
     * @return redirect
     */
    function removeHeader($header) {
    	$key = array_search($header, $this->headers);
    	unset($this->headers[$key]);
    	return $this;
    }
    
    
    /*
     * Set Header Code
     * 
     * @param string $code
     * @return redirect
     */
    function setCode($code = 303) {
    	$temp = $this;
    	if(!$this->new) {
    		$temp = new redirect();
    		$temp->init($this->runtime, true);
    	}
    	
    	$this->makeHeader($code);
    	return $temp;
    }
    
    function makeHeader($code) {
    	$this->code = 'HTTP/1.0 ';
    	if(preg_match('/fastcgi/i', $_ENV["REDIRECT_HANDLER"])) {
    		$this->code = 'Status: ';
    	}
    	
    	switch($code) {
    		case 201:
    			$this->code .= '201 Created';
    		break;
    		
    		case 300:
    			$this->code .= '300 Multiple Choices';
    		break;
    		
    		case 301:
    			$this->code .= '301 Moved Permanently';
    		break;
    		
    		case 302:
    			$this->code .= '302 Found';
    		break;
    		
    		case 303:
    			$this->code .= '303 See Other';
    		break;
    		
    		case 304:
    			$this->code .= '304 Not Modified';
    		break;

    		case 305:
    			$this->code .= '305 Use Proxy';
    		break;
    		
    		case 306:
    			$this->code .= '306 Switch Proxy';
    		break;
    		
    		case 307:
    			$this->code .= '307 Temporary Redirect';
    		break;
    		
    		case 308:
    			$this->code .= '308 Resume Incomplete';
    		break;
    		
    		default:
    			$this->code .= '303';
    		break;
    	}
    }
    
    
    /*
     * Url Function
     * 
     * @param string|array $input
     * @return void
     */
    function url() {
    	foreach($this->headers as $header) {
    		header($header);
    	}
    	
    	if($this->code) {
    		header($this->code);
    	}
    	
    	$args = func_get_args();
    	
    	if(func_num_args() > 1) {
		    header('location: '.$this->runtime->link($args[0],$args[1],$args[2],$args[3]));
		    if($this->exit) { exit(0); }
		    return;
    	}
    	
    	$input = $args[0];
    	
    	if(is_array($input)) {
            header('location: '.$this->runtime->link($input[0],$input[1],$input[2],$input[3]));
            if($this->exit) { exit(0); }
        } elseif(is_string($input)) {
            header('location: '.$input);
            if($this->exit) { exit(0); }
        } else {
            header('location: index.php');
            if($this->exit) { exit(0); }
        }
    }
    
    
}
