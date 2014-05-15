<?php
/**
 * Security Worker
 * @version 1.0
 * @author Michael Stowes
 */

/**
 * Security Class
 */
class security {
    
	const modelNotAllowed = 'Permission denied for Model Inclusion';
	
	/**
	 * Init
	 * @param BootStrap $runtime
	 * @return void
	 */
    function init($runtime) {
       global $model;
       $this->activeModel = $model;
    }

    /**
     * Most Secure
     */
    function allowedModels() {
    	if(func_num_args() == 1) {
			$allowed = func_get_arg(0);
			if(is_array($allowed)) {
				if(!in_array($this->activeModel,$allowed)) {
					$this->kill(modelNotAllowed);
				}
			}
			if($allowed != $this->activeModel) {
				$this->kill(modelNotAllowed);
			}
		} else {
			if(!in_array($this->activeModel,func_get_args())) {
				$this->kill(modelNotAllowed);
			}
		}
		return true;
    }
    
    /**
     * Better than nothing
     */
    function deniedModels() {
    	if(func_num_args() == 1) {
			$allowed = func_get_arg(0);
			if(is_array($allowed)) {
				if(in_array($this->activeModel,$allowed)) {
					$this->kill(modelNotAllowed);
				}
			}
			if($allowed == $this->activeModel) {
				$this->kill(modelNotAllowed);
			}
		} else {
			if(in_array($this->activeModel,func_get_args())) {
				$this->kill(modelNotAllowed);
			}
		}
		return true;
    }
    
    
    function kill($message) {
    	die($message);
    }
    
    
}   