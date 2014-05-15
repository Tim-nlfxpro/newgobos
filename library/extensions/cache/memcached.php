<?php
class cache_memcachedExtension {
	
	function test() {
		return class_exists('Memcached');
	}
	
	function connect($host='localhost',$port='11211') {
		$this->conn = new Memcached;
		return $this->conn->addServer($host, $port);
	}
	
	function addServer($host,$port) {
		if(!isset($this->conn)) { $this->connect(); }
		return $this->conn->addServer($host, $port);
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