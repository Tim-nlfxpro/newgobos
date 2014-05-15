<?php
/**
 * Session Worker
 * @version 1.0
 * @author Michael Stowes
 */

/**
 * Session Class
 */
class session {
    
	/**
	 * Init
	 * @param BootStrap $runtime
	 * @return void
	 */
    function init($runtime) {
        session_start();
        foreach($_SESSION['session_values'] as $k=>$v) {
            $this->set($k,$v,false);
        }
    }
    
    /**
	 * Getter/ Setter
	 * @param string $key
	 * @param string $value
	 * $param bool   $setsession internal use only
	 * @return void|bool|string
	 */
    function set($key,$value=false,$setsession=true) {
        if(!$value) {
            if(isset($this->$key)) {
                return unserialize($this->$key);
            }
            return false;
        } else {
        	$newvalue = serialize($value);
            if($setsession) {
                $_SESSION['session_values'][$key] = $newvalue;
            }
            $this->$key = $value;
            return false;
        }
    }
    
    /**
     * Getter
     * 
     * @param string $key
     * @return bool|string
     */
    function get($key) {
        return $this->set($key);
    }
    
    function delete($key) {
    	unset($_SESSION['session_values'][$key]);
    	unset($this->$key);
    }

}
