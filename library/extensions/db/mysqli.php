<?php
class db_mysqliExtension extends db {
	function init($runtime) {
		parent::init($runtime);
	}
	
	function connect() {
		$this->connection = mysqli_connect($this->runtime->config->database->host,
		                                  $this->runtime->config->database->username,
		                                  $this->runtime->config->database->password) 
		                                  or $runtime->error->warn($this->error());
		                                  
		$this->db = mysqli_select_db($this->connection,$this->runtime->config->database->dbname)
										or $runtime->error->warn($this->error());
		
		if($this->db) {
			define('DBCONNECTED',true);
		}
	}
	
	function quote($input) {
		return mysqli_real_escape_string($input);
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
		
		$result = mysqli_query($this->connection,$query);
		
		if($this->action == 'insert') {
			$this->insertId = mysqli_insert_id($this->connection);
		}
		
		$this->affectedRows = mysql_affected_rows($this->connection);
		
		return $result;
		
	}
	
	function dofetch() {
		$this->doQuery();
		
		if($this->format == 'object') {
			$this->oldData = new stdClass;
			$this->_smart_data = mysqli_fetch_array($this->query);
			foreach($this->_smart_data as $dataKey => $dataItem) {
				$this->$dataKey = $dataItem;
				$this->oldData->$dataKey = $dataItem;
			}
		} else {
			$result = array();
			while($tmp = mysqli_fetch_object($this->query)) {
				$result[] = $tmp;
			}
			return $result;
		}
	}
	
	function count() {
		if(!isset($this->query)) { return false; }
		$cnt = mysqli_num_rows($this->query);
		return $cnt;
	}
	
	function doRawQuery($raw) {
		$this->query = mysqli_query($this->connection,$raw);
	}
	
	function doQuery() {
		if(!isset($this->query)) {
			$query = 'SELECT '.$this->select.' FROM '.$this->table;
			
			if(is_array($this->join)) {
				foreach($this->join as $join) {
					$query .= ' '.$join[3].' join '.$join[0];
					if(strstr($join,'=')) {
						$query .= ' as '.$join[1].' ON '.$join[2];
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
			    $query .= ' ORDERBY';
				foreach($this->orderby as $orderby) {
					$query .= ' '.$orderby.',';
			    }
			    $query = substr($query,0,-1);	
			}
		
			
			if(isset($this->limit) && $this->limit) {
				$query .= ' LIMIT '.$this->limit;
			}
			
			$this->query = mysqli_query($this->connection,$query);
		}
	}
	
	function error() {
		return mysql_error();
	}
	
}
?>