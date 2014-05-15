<?php
class layout {
    private $hidelayout = false;
    private $viewdata;
    public $layoutdir = '';
    public $moduledir = '';
    public $viewdir = '';
    private $layout = 'default';
    public $module = 'default';
    public $controller = 'default';
    public $view = 'default';
    public $title = '';
    public $meta = '';
    private $css = array();
    private $js = array();
    
    
    function init($runtime) {
        $this->layoutdir = $runtime->config->directories->layouts;
        $this->moduledir = $runtime->config->directories->modules;
        $this->viewdir = $runtime->config->directories->views;
        $this->meta = new stdClass;
        $this->meta->title &= $this->title;
        $this->title = $runtime->config->meta->title;
        $this->meta->keywords = $runtime->config->meta->keywords;
        $this->meta->description = $runtime->config->meta->description;
        $this->runtime = $runtime;
    }
    
    
    function render() {
        if(!$this->hidelayout) {
        	$view =& $this->runtime->view;
        	$session =& $this->runtime->session;
            require_once($this->layoutdir.'/'.$this->layout.'/index.php');
        } else {
            $this->content();
        }
    }
    
    
    function title($override) {
        if(!$override) {
            return $this->title;
        } else {
            $this->title = $override;
        }
    }
    
    
    function meta($key,$override=false) {
        if(!$override) {
           return $this->meta->$key; 
        } else {
            $this->meta->$key = $override;
        }
    }
    
    
    function content($override=false) {
        if(!$override) {
            $view =& $this->runtime->view;
            $session =& $this->runtime->session;
            require_once($this->moduledir.'/'.$this->module.'/'.$this->viewdir.'/'.$this->controller.'/'.$this->view.'.phtml');
        } else {
            echo $override;
        }
    }
    
    
    function hide() {
        $this->hidelayout = true;
    }
    
    
    function template($template) {
        if(file_exists($this->layoutdir.'/'.$template.'/index.php')) {
            $this->layout = $template;
        }
    }
    
    function link($model,$controller,$view,$querystring) {
    	return $this->runtime->link($model,$controller,$view,$querystring);
    }
    
    function addCSS($file,$type='screen') {
        if(@file_exists($file)) {
            $this->css[] = array($file,$type);
        }
        $this->show = 'CSS';
        return $this;
    }

    function addJavaScript($file) {
        if(@file_exists($file)) {
            $this->js[] = $file;
        }
        $this->show = 'JavaScript';
        return $this;
    }
    
    function show() {
        return $this->show{$this->show}();
    }
    
    function showCSS() {
        foreach($this->css as $css) {
            echo '<link rel="stylesheet" media="'.$css[1].'" type="text/css" href="'.$css[0].'" />'."\n";
        }
    }
    
    function showJavaScript() {
        foreach($this->js as $js) {
            echo '<script type="text/javascript" src="'.$js.'" /></script>'."\n";
        }
    }
    
}
