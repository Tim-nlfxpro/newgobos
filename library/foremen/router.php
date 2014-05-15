<?php
/**
 * Setup Route and Call Model, View, and Controllers
 * 
 * @version 1.0
 * @author Michael Stowe
 */

/**
 * Autoload
 * Automatically include interfaces, traits, and classes
 * from the library/dependencies folder.
 * 
 * Note: when loading a class, $this->load($className) is 
 * the preferred method from inside of models and controllers
 * 
 * @param string $className
 * @throws fatal error
 */
function __autoload($className) {
	global $config, $runtime;
	if(preg_match('/Interface/',$className)) {
		$folder = 'interfaces';
	} elseif(phpversion() >= 5.4 && preg_match('/Trait/',$className)) {
		$folder = 'traits';
	} else {
		$folder = 'classes';
	}
	$class = $config->directories->dependencies.'/'.$folder.'/'.$className.'.php';
	if(!file_exists($class)) {
		$runtime->error->fatal($className.' does not exist in '.$folder);
	}
	require_once($class);
}


// Find Model, Controller, View
if(!isset($_REQUEST[$config->requestvars->module])) {
	$module = 'default';
} else {
	$module = strtolower($_REQUEST[$config->requestvars->module]);
}

if(!isset($_REQUEST[$config->requestvars->controller])) {
	$model = 'default';
} else {
	$model = strtolower($_REQUEST[$config->requestvars->controller]); 
}

if(!isset($_REQUEST[$config->requestvars->view])) {
    $view = 'default';
} else {
    $view = strtolower($_REQUEST[$config->requestvars->view]);
}

$controller = $model.'Controller';
$modeln = $model.'Model';

require_once($config->directories->modules.'/'.$module.'/'.$config->directories->models.'/'.$modeln.'.php');
require_once($config->directories->modules.'/'.$module.'/'.$config->directories->controllers.'/'.$controller.'.php');
$runtime = new $controller;

$runtime->__bootstrap_init();

// Layout setup through the Bootstrap
$runtime->layout->module = $module; 
$runtime->layout->controller = $model;
$runtime->layout->view = $view;

// Run Init if it Exists
if(method_exists($runtime,'init')) {
    $runtime->init();
}

// Run View Action if it Exists
$viewfunction = $view.'Action';
if(method_exists($runtime,$viewfunction)) {
    $runtime->$viewfunction();
}

$runtime->layout->render();
