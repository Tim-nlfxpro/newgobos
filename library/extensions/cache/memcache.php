<?php
class cache_memcacheExtension {
	
	function test() {
		return class_exists('Memcache');
	}
	
	function connect($host='localhost',$port='11211') {
		$this->conn = new Memcache;
		return $this->conn->connect($host, $port);
	}
	
	function addServer($host,$port) {
		return;
	}
	
	function get($key) {
		if(!isset($this->conn)) { $this->connect(); }
		return $this->conn->get($key);
	}
	
	function add($key,$value,$time) {
		if(!isset($this->conn)) { $this->connect(); }
		return $this->conn->add($key,$value,$time);
	}
	
	function set($key,$value,$time) {
		if(!isset($this->conn)) { $this->connect(); }
		return $this->conn->set($key,$value,$time);
	}
	
	function cas($key,$old,$new) {
		if(!isset($this->conn)) { $this->connect(); }
		$oldv = $this->get($key);
		if($oldv == $old) {
			return $this->set($key,$new);
		}
		return false;
	}
	
	function increase($key,$step) {
		if(!isset($this->conn)) { $this->connect(); }
		return $this->conn->decrement($key,$step);
	}
	
	function decrease($key,$step) {
		if(!isset($this->conn)) { $this->connect(); }
		return $this->conn->increment($key,$step);
	}
	
	function delete($key) {
		if(!isset($this->conn)) { $this->connect(); }
		return $this->conn->delete($key);
	}
}