<?php
/**
 * Error Cache
 * @version 1.0
 * @author Michael Stowes
 */

/**
 * Cache Class
 */
class cache {
    
    function init($runtime) {
    	$this->runtime = $runtime;
    	if(!$runtime->config->cache->enabled) { return false; }
    	$this->prefix = $runtime->config->cache->envname;
    	$this->ext = $runtime->ext('cache.'.$runtime->config->cache->defaulttype);
    }
    
    
    function apc() {
    	if(!$runtime->config->cache->enabled) { return false; }
    	if(!isset($this->apc)) {
    		$this->apc = $this->ext('cache.apc',true);
    	}
    	return $this->apc;
    }
    
    
    function memcache() {
    	if(!$runtime->config->cache->enabled) { return false; }
    	if(!isset($this->memcache)) {
    		$this->memcache = $this->ext('cache.memcache',true);
    	}
    	return $this->memcache;
    }

    
    function memcached() {
    	if(!$runtime->config->cache->enabled) { return false; }
    	if(!isset($this->memcached)) {
    		$this->memcached = $this->ext('cache.memcached',true);
    	}
    	return $this->memcached;
    }
    
    
    function custom($type) {
    	if(!$runtime->config->cache->enabled) { return false; }
    	if(!isset($this->$custom)) {
    		$this->$custom = $this->ext('cache.'.$type,true);
    	}
    	return $this->$custom;
    }
    
    
    function connect($host,$port) {
    	return $this->ext->conenct($host,$port);
    }
    
    
    function addServer($host,$port) {
    	return $this->ext->addServer($host,$port);
    }
    
    
    function get($key) {
    	return $this->ext->get($this->prefix.$key);
    }
    
    
    function add($key,$value,$time=0) {
    	return $this->ext->add($this->prefix.$key,$value,$time);
    }
    
    
    function set($key,$value,$time=0) {
    	return $this->ext->set($this->prefix.$key,$value,$time);
    }
    
    
    function cas($key,$old,$new) {
    	return $this->ext->cas($this->prefix.$key,$old,$new);
    }
    
    
    function increase($key,$step=1) {
    	return $this->ext->increase($this->prefix.$key,$step);
    }
    
    
    function decrease($key,$step=1) {
    	return $this->ext->decrease($this->prefix.$key,$step);
    }
    
    function delete($key) {
    	return $this->ext->delete($this->prefix.$key);
    }

}
