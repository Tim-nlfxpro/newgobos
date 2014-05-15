<?php
/**
 * 
 * Loader is only used if autoload is set to false
 * @author Michael Stowe
 * @version 1.0
 *
 */
class Loader extends BaseLoader {
	
	protected function _initDb() {
		$this->getFile('db.php');
		return new db();
	}
	
}
?>