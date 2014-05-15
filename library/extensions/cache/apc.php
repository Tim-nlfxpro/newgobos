<?php
class cache_apcExtension {
	
	function test() {
		return function_exists('apc_fetch');
	}
	
	function connect($host,$port) {
		return;
	}
	
	function addServer($host,$port) {
		return;
	}
	
	function get($key) {
		return apc_fetch($key);
	}
	
	function add($key,$value,$time) {
		return apc_add($key,$value,$time);
	}
	
	function set($key,$value,$time) {
		return apc_store($key,$value,$time);
	}
	
	function cas($key,$old,$new) {
		return apc_cas($key,$old,$new);
	}
	
	function increase($key,$step) {
		return apc_inc($key,$step);
	}
	
	function decrease($key,$step) {
		return apc_dec($key,$step);
	}
	
	function delete($key) {
		return apc_delete($key);
	}
}