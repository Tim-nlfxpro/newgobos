<?php
class BaseView {
    /**
     * Build Framework Links
     * 
     * @param string $module
     * @param string $controller
     * @param string $view
     * @param string $querystring
     * @return string
     */
    public function link() {
        global $runtime;

	$count = func_num_args();
	if($count === 0) {
		$module = $runtime->layout->module;
		$controller = $runtime->layout->controller;
		$view = $runtime->layout->view;
	} elseif($count === 1) {
		$arg = func_get_args();
		if(is_array($arg[0])) {
			$arg = $arg[0];
			$module = $arg[0];
			$controller = (isset($arg[1]) ? $arg[1] : 'default');
			$view = (isset($arg[2]) ? $arg[2] : 'default');
			$querystring = (isset($arg[3]) ? $arg[3] : '');
		} else {
			$module = $runtime->layout->module;
			$controller = $runtime->layout->controller;
			$view = $arg[0];	
		}
	} else {
		$arg = func_get_args();
		$module = $arg[0];
		$controller = (isset($arg[1]) ? $arg[1] : 'default');
		$view = (isset($arg[2]) ? $arg[2] : 'default');
		$querystring = (isset($arg[3]) ? $arg[3] : '');
	}
	
        if($runtime->config->links->usemodrewrite) {
            return '/'.$module.'/'.$controller.'/'.$view.(strlen($querystring) > 0 ? '/?'.$querystring : '');
        }
        
        return 'index.php?'.$runtime->config->requestvars->module.'='.$module.'&'.$runtime->config->requestvars->controller.'='.$controller.'&'.$runtime->config->requestvars->view.'='.$view.(strlen($querystring) > 0 ? '&'.$querystring : '');
    }
    
    
    /**
     * Include View Script
     * @param string $viewName
     * @throws warning
     * @return bool
     */
    public function load($viewName) {
        global $runtime;
        $extname = $extfile = strtolower($viewName);
        
        // Folder Organization
        if(preg_match('/\./',$extname)) {
            $extname = str_replace('.','_',$extname);
            $extfile = str_replace('.','/',$extfile);
        }
        
        if(@file_exists($runtime->config->directories->dependencies.'/views/'.$extfile.'.phtml')) {
            include($runtime->config->directories->dependencies.'/views/'.$extfile.'.phtml');
            return true;
        } else {
            $this->error->warn($viewName.' could not be loaded');
            return false;
        }
    }
}
