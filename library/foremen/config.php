<?php
/**
 * Setup Configs
 * 
 * @version 1.0
 * @author Michael Stowe
 */

 /**
  * Objectify Array
  * 
  * @param array $array
  * @return stdClass|bool
  */
function ObjectifyArray($array) {
    if(!is_array($array)) {
        return $array;
    }
    $object = new stdClass();
    if (is_array($array) && count($array) > 0) {
      foreach ($array as $name=>$value) {
         $name = strtolower(trim($name));
         if (!empty($name)) {
            $object->$name = ObjectifyArray($value);
         }
      }
      return $object;
    }
    else {
      return false;
    }
}

$config = ObjectifyArray(parse_ini_file($config_path,true));
unset($config_path);

// Set Timezone
date_default_timezone_set($config->system->timezone);