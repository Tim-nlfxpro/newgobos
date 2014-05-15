<?php
/**
 * Error Worker
 * @version 1.0
 * @author Michael Stowes
 */

/**
 * Errro Class
 */
class error {
    
    
    function init($runtime) {
    	$this->runtime = $runtime;
    }
    
    
    /*
     * Fatal Error Function
     * 
     * @param string $error
     * @param bool   $log
     * @return void
     */
    function fatal($error,$log=true) {
    	if($this->runtime->config->debug) {
    		throw new Exception($error);
    	} else {
    		exit(1);
    	}
    }
    
	/*
     * Fatal Error Function
     * 
     * @param string $error
     * @param bool   $log
     * @return void
     */
    function warning($error,$log=true) {
    	if($this->runtime->config->debug) {
    		echo $error;
    	}
    }
    
    
    /*
     * Warning Alias
     * 
     * @param string $error
     * @param bool   $log
     * @return void
     */
    function warn($error,$log=true) {
    	return $this->warning($error,$log);
    }
    

    /*
     * Log Errors
     * 
     * @param $error
     * @return void
     */
    function log($error) {
    	return true;
    }
    
    
}
