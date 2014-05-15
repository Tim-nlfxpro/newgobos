<?php
class db_firebirdExtension extends db {
	function init($runtime) {
		parent::init($runtime);
	}
	
	function connect() {
		$this->connection = ibase_connect($this->runtime->config->database->dbname,
		                                  $this->runtime->config->database->username,
		                                  $this->runtime->config->database->password) 
		                                  or $runtime->error->warn($this->error());
		
		if($this->connection) {
			define('DBCONNECTED',true);
		}
	}
	
	function quote($input) {
		return str_replace("'","\\'\\'",$input);
	}
	
	function execute($getOldData=true) {
		if(!isset($this->oldData) && $getOldData) {
			$this->select = '*';
			$this->oldData = $this->fetchOne();
		}
		
		$query = ($this->action == 'insert'?'INSERT':'UPDATE').' '.$this->table.' SET';
		
	    if(isset($this->_smart_update) && $this->_smart_update) {
	    	$this->data = array();
			foreach($this->_smart_data as $k=>$v) {
				if($this->$k != $this->oldData->$k) {
					$this->data[$k] = $this->$k;
				}
			}
		}
		
		
		if(is_array($this->data) && count($this->data) > 0) {
			foreach($this->data as $k=>$v) {
				$query .= ' '.$k.' = "'.$v.'",';
				if($getOldData && $this->oldData->$k != $v) {
					$this->changed[$k] = array('old'=>$this->oldData->$k, 'new'=>$v);
				}
			}
			$query = substr($query,0,-1);
		} elseif (is_string($this->data)) {
			$query .= ' '.$this->data;
		} else {
			return false;
		}
		
		if($this->action != 'insert') {
			if(is_array($this->where)) {
				$query .= ' WHERE';
					foreach($this->where as $where) {
						$query .= ' '.$where;
					}
				}
				
			if(isset($this->limit) && $this->limit) {
				$query .= ' LIMIT '.$this->limit;
			}
		}
		
		$result = ibase_query($this->connection,$query);
		
		if($this->action == 'insert') {
			$this->insertId = null;
		}
		
		$this->affectedRows = ibase_affected_rows($this->connection);
		
		
		return $result;
		
	}
	
	function dofetch() {
		$this->doQuery();
		
		if($this->format == 'object') {
			$this->oldData = new stdClass;
			$this->_smart_data = ibase_fetch_array($this->query);
			foreach($this->_smart_data as $dataKey => $dataItem) {
				$this->$dataKey = $dataItem;
				$this->oldData->$dataKey = $dataItem;
			}
		} else {
			$result = array();
			while($tmp = ibase_fetch_object($this->query)) {
				$result[] = $tmp;
			}
			return $result;
		}
	}
	
	function count() {
		if(!isset($this->query)) { return false; }
		$cnt = ibase_fetch_array($this->query);
		return count($cnt);
	}
	
	function doRawQuery($raw) {
		$this->query = ibase_query($this->connection,$raw);
	}
	
	function doQuery() {
		if(!isset($this->query)) {
			$query = 'SELECT '.$this->select.' FROM '.$this->table;
			if(!is_null($this->tablenicename)) { $query .= ' as '.$this->tablenicename; }
			
			if(is_array($this->join)) {
				foreach($this->join as $join) {
					$query .= ' '.$join[3].' join '.$join[0].' as '.$join[1];
					if(strstr($join[2],'=')) {
						$query .= ' ON '.$join[2];
					} else {
						$query .= ' USING('.$join[2].')';
					}
				}
			}
			
			if(is_array($this->where)) {
			$query .= ' WHERE';
				foreach($this->where as $where) {
					$query .= ' '.$where;
				}
			}
			
			if(is_array($this->groupby)) {
			    $query .= ' GROUP BY';
				foreach($this->groupby as $groupby) {
					$query .= ' '.$groupby.',';
				}
			    $query = substr($query,0,-1);
			}
			
			
		       if(is_array($this->orderby)) {
			    $query .= ' ORDER BY';
			    foreach($this->orderby as $orderby) {
					$query .= ' '.$orderby.',';
			    }
			    $query = substr($query,0,-1);	
			}
		
			
			if(isset($this->limit) && $this->limit) {
				$query .= ' LIMIT '.$this->limit;
			}
			
			$this->debug = $query;
			$this->query = ibase_query($this->connection,$query);
		}
	}
	
	function error() {
		return ibase_errmsg();
	}
	
}
?>
