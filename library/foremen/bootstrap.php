<?php
/**
 * BootStrap
 * @version 1.0
 * @author Michael Stowe
 */

class BootStrap {
    /**
     * Construct
     * @return void
     */
    function __bootstrap_init() {
        // Setup Defaults
        global $config;
        
        $this->config =& $config;

        // Setup View Class
        require_once($config->directories->foremen.'/baseview.php');
        $this->view = new BaseView($this);

        // Build
        $this->__bootstrap_build();
    }
    
    /**
     * Load Workers and Extensions
     * @return void
     */
    protected function __bootstrap_build() {
    	if(!$this->config->system->autoloader) {
    		die('doesnt work yet');
    		require_once($this->config->directories->library.'/'.$this->config->directories->foremen.'/baseloader.php');
    		require_once($this->config->directories->library.'/Loader.php');
    		$loader = new Loader();
    		$loader->baseInit(&$this);
    		return;
    	}

        // load workers
        $workers = scandir($this->config->directories->workers);
        foreach($workers as $worker) {
            if(preg_match('/[a-z0-9_\-\.]+\.php/i',$worker)) {
                $name = substr($worker,0,-4);
                require_once($this->config->directories->workers.'/'.$worker);
                $this->{strtolower($name)} = new $name;
                if(method_exists($this->{strtolower($name)},'init')) {
                    $this->{strtolower($name)}->init($this);
                }
            }
        }
        
        // load startup
        $startup = scandir($this->config->directories->startup);
        foreach($startup as $start) {
            if(preg_match('/[a-z0-9_\-\.]+\.php/i',$start)) {
                $name = substr($start,0,-4);
                require_once($this->config->directories->startup.'/'.$start);
                if(isset($this->{strtolower($name)})) {
                	$this->error->fatal($name.' already registered by worker');
                }
                $this->{strtolower($name)} = new $name;
                if(method_exists($this->{strtolower($name)},'init')) {
                    $this->{strtolower($name)}->init($this);
                }
            }
        }

        // load watchers
        $this->watchers = new stdClass;
        require_once($this->config->directories->foremen.'/basewatcher.php');
        $watchers = scandir($this->config->directories->watchers);
        foreach($watchers as $watcher) {
            if(preg_match('/[a-z0-9_\-\.]+\.php/i',$watcher)) {
                $name = substr($watcher,0,-4).'Watcher';
                require_once($this->config->directories->watchers.'/'.$watcher);
                $this->watchers->$name = new $name;
                if(method_exists($this->watchers->$name,'init')) {
                    $this->watchers->$name->init($this);
                } else {
                	$this->error->warn('Watcher '.$name.' does not have an init method and will not be run');
                }
            }
        }
    }
    
    /**
     * Load Extension
     * 
     * @param string $extname the name of the extension
     * @param bool   $new     create new instance
     * @return bool|object
     */
    public function ext($extname,$new=false) {
    	$extname = $extfile = strtolower($extname);
    	
    	// Folder Organization
    	if(preg_match('/\./',$extname)) {
    		$extname = str_replace('.','_',$extname);
    		$extfile = str_replace('.','/',$extfile);
    	}

    	if(!isset($this->ext->$extname) || $new) {
    	    if(!isset($this->ext->$extname) && @file_exists($this->config->directories->extensions.'/'.$extfile.'.php')) {
    	        require_once($this->config->directories->extensions.'/'.$extfile.'.php');
    	    } elseif(!isset($this->ext->$extname)) {
    	        return false;
    	    }
    	    $className = $extname.'Extension';
    	    $this->ext->$extname = new $className;
    	    if(method_exists($this->ext->$extname,'init')) {
               $this->ext->$extname->init($this);
            }
    	}
    	return $this->ext->$extname;
    }
    
    
    public function load($className) {
    	$extname = $extfile = strtolower($className);
    	
    	// Folder Organization
    	if(preg_match('/\./',$extname)) {
    		$extname = str_replace('.','_',$extname);
    		$extfile = str_replace('.','/',$extfile);
    	}
    	
    	if(@file_exists($this->config->directories->dependencies.'/classes/'.$extfile.'.php')) {
    	    require_once($this->config->directories->dependencies.'/classes/'.$extfile.'.php');
    	} else {
    		$this->error->warn($className.' could not be loaded');
    	    return false;
    	}
    	return new $extname;
    }
    
    
    public function link($module,$controller,$view='default',$querystring='') {
    	$args = func_get_args();
    	return call_user_func_array(array(&$this->view,'link'), $args);
    }
}