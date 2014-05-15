<?php
/**
 * Database (DB) Worker
 * @version 1.0
 * @author Michael Stowes
 */

/**
 * DB Class
 */
class db {
    
	/**
	 * Init
	 * @param BootStrap $runtime
	 * @param bool      $auto internal use only
	 * @return void
	 */
    function init($runtime,$auto = true) {
        if(!$auto || $runtime->config->database->autoconnect == 'true') {
            $this->connection = mysql_connect($runtime->config->database->host,$runtime->config->database->username,$runtime->config->database->password) or die('Could not connect to database');
            $this->db = mysql_select_db($runtime->config->database->dbname) or die('could not connect to database');
        }
        $this->runtime = $runtime;
    }
    
    /**
     * Manual Database Connect Call
     * 
     * @return void
     */
    function connect() {
        $this->init($this->runtime,false);
    }
    
    /**
     * Fetch All
     * 
     * @param string $query
     * @return array
     */
    function fetch($query) {
        $q = mysql_query($query);
        $result = array();
        while($i = mysql_fetch_object($query)) {
            $result[] = $i;
        }
        return $result;
    }
    
    /**
     * Fetch One
     * 
     * @param string $query
     * @return MySQL::Object
     */
    function fetchOne($query) {
        $results = $this->fetch($query);
        return $results[0];
    }
    
    /**
     * Query
     * 
     * @param string $query
     * @return bool|MySQL::Resource
     */
    function query($query) {
        return mysql_query($query);
    }
    
}
