<?php

/**
 * 
 * Database Worker Class
 * example calls:
 * $this->db->table('demo')->select()->where('1=1')->where('AND 2=2')->fetchOne();
 * $this->db->table('demo')->select('id')->where('1=1')->orderby('id asc')->fetchAll();
 * $this->db->table('demo')->select()->orderby('id desc')->fetch(10,20);
 * $this->db->table('names')->insert(array('name'=>'bob'))->save();
 * $this->db->table('meta')->update(array('name'=>'joe'))->where('name = "?"','bob')->save();
 * 
 * $call = $this->db->table('names')->select()->where('name = "?",'joe')->fetchOne();
 * $call->name = 'bob';
 * $call->save();
 * 
 * @author Michael Stowe
 * @version 1.0
 *
 */

class db {
	
	function init($runtime) {
		$this->runtime = $runtime;
		$this->extClass = $runtime->config->database->dbtype;
		
		$this->oldData = array();
		$this->changed = array();
		
		$this->_where_type = '';
	}
	
	function table($tableName,$nicename=null) {
		if(!defined('DBCONNECTED')) {
			$return = $this->runtime->ext('db.'.$this->extClass);
			$return->connect();
		} else {
			$extClass = 'db_'.$this->extClass.'Extension';
			$return = new $extClass;
		}
		$return->table = $tableName;
		$return->tablenicename = $nicename;
		return $return;
	}
	
	function select() {
		unset($this->query);		
		if(func_num_args() == 0) {
			$this->select = '*';
		} elseif(func_num_args() == 1) {
			$this->select = func_get_arg(0);
			if(is_array($this->select)) {
				$this->select = implode(', ',$this->select);
			}
		} else {
			$this->select = implode(', ',func_get_args());
		}
		$this->action = 'select';
		return $this;
	}
	
	function update($data) {
		unset($this->query);
		$this->data = $data;
		$this->action = 'update';
		$this->_smart_update = false;
		return $this;
	}
	
	function insert($data) {
		unset($this->query);
		$this->data = $data;
		$this->action = 'insert';
		$this->_smart_update = false;
		return $this;
	}
	
	function join($table,$nicename,$on,$type='inner') {
		$this->join[] = array($table,$nicename,$on,$type);
		return $this;
	}
	
	function innerjoin($table,$nicename,$on) {
		$this->join($table,$nicename,$on);
	}
	
	function leftjoin($table,$nicename,$on) {
		$this->join($table,$nicename,$on,$type='left');
		return $this;
	}
	
	function rightjoin($table,$nicename,$on) {
		$this->join($table,$nicename,$on,$type='right');
		return $this;
	}
	
	function where() {
		if(func_num_args() == 1) {
			$this->where[] = $this->_where_type . func_get_arg(0);
		} else {
			$args = func_get_args();
			$statement = str_replace('%', '{*^pct!*}', array_shift($args));
			foreach($args as $k=>$v) {
				$args[$k] = $this->quote($v);
			}
			$this->where[] = $this->_where_type . str_replace('{*^pct!*}', '%', vsprintf(str_replace('?','%s',$statement), $args));
		}
		$this->_where_type = '';
		return $this;
	}
	
	function andWhere() {
		$args = func_get_args();
		$this->_where_type = 'AND ';
		return call_user_func_array(array(&$this,'where'), $args);
	}
	
	function orWhere() {
		$args = func_get_args();
		$this->_where_type = 'OR ';
		return call_user_func_array(array(&$this,'where'), $args);
	}
	
	function orderby($orderby) {
		$this->orderby[] = $orderby;
		return $this;
	}
	
	function groupby($groupby) {
		$this->groupby[] = $groupby;
		return $this;
	}
	
	function limit($start,$records=null) {
		if($records == null || !$records) {
			$this->limit = $start;
		} else {
			$this->limit = $start.','.$records;
		}
		return $this;
	}
	
	function save($getOldData=true) {
		$this->saved = $this->execute($getOldData);
		return $this;
	}
	
	function query($query) {
		unset($this->query);
		$this->doRawQuery($query);
		return $this;
	}
	
	function fetch($start,$records=null) {
		$this->limit($start,$records);
		$this->format = 'array';
		return $this->dofetch();
	}
	
	function fetchAll() {
		$this->limit = false;
		$this->format = 'array';
		return $this->dofetch();
	}
	
	function fetchOne() {
		$this->limit = 1;
		$this->format = 'object';
		$this->_smart_update = true;
		$this->dofetch();
		return $this;
	}
	
	function oldData() {
		return $this->oldData;
	}
	
	function changedData($key=null,$oldornew='new') {
		if($key != null) {
			if(!isset($this->changed[$key])) {
				return false;
			}
			return $this->changed[$key][$oldornew];
		}
		return $this->changed;
	}
	
	function insertId() {
		return $this->insertId;
	}
	
	function affectedRows() {
		return $this->affectedRows;
	}
	
	function debug() {
		return $this->debug;
	}
}

?>