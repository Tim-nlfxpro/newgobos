<?php

class htmleExtension {
	

	############################################ FIND PLUGINS
		
	function init($runtime) {
		$this->files = false;
		$this->_plugin_dir = '/htmle_plugins/';
		
		if($runtime->config->extensions->htmlcache) {
			$this->files = $runtime->cache->get('htmle_plugins');
		}
		
		if(!$this->files) {
			$this->files = scandir(dirname(__FILE__).$this->_plugin_dir);
		}
		
		if($runtime->config->extensions->htmlcache) {
			$runtime->cache->add('htmle_plugins',$this->files, $runtime->config->extensions->htmlcachetime);
		}
		
		foreach ($this->files as $file) {
	          if(substr($file,-4) == '.php') { $this->tags[] = substr($file,0,-4); }
		}
	
		$this->find = array('"','}','((','))');
		$this->replace = array('','','="','"');
	}
	
	
	############################################ GET PLUGINS
	
	
	function getPlugins() {
		return $this->files;
	}
	
	
	############################################ GET PLUGIN INFO
	
	
	function getPluginDetails() {

		$info = array();
		$t = 0;
		foreach ($this->files as $file) {
			if(substr($file,0,1) != '.') { 
				$info[$t] = new stdClass();
				$info[$t]->title = strtoupper(substr($file,0,-4));
				
				$example = file_get_contents(dirname(__FILE__).$this->_plugins_dir.$file);
				$parts = explode("\n",$example);
				$i = 1;
				$examplecode = $purpose = $required = $special = '';
				foreach($parts as $part) {
					if(preg_match('/example:/i',$part)) {
						$line = explode(':',$part);
						array_shift($line);
						$info[$t]->example =  htmlentities(trim(implode(':',$line)));
					} elseif(preg_match('/purpose:/i',$part)) {
						$line = explode(':',$part);
						array_shift($line);
						$info[$t]->purpose =  htmlentities(trim(implode(':',$line)));
					} elseif(preg_match('/required:/i',$part)) {
						$line = explode(':',$part);
						array_shift($line);
						$info[$t]->required =  trim(implode(':',$line));
					} elseif(preg_match('/special:/i',$part)) {
						$line = explode(':',$part);
						array_shift($line);
						$info[$t]->special =  trim(implode(':',$line));
					} elseif($i == 20) {
						break;
					}
					$i++;
				}
			}
		}
		
		return $info;

	}
	
	
	############################################ FIX QUOTES (WordPress and CMS)
	
	
	function cleanquote($input) {
		return str_ireplace(array('&#8221;','&#8243;','&quot;','&#8216;','&#8217;'),array('"','"','"','\'','\''),$input);
	}
	
	
	############################################ FIND PLUGIN CALLS
	
	
	function parse($HTMLIN) {
		foreach($this->tags as $tag) {

			if(preg_match_all('/{'.$tag.':[^}]+}[^{]*{\/'.$tag.':[^}]+/i',$HTMLIN,$innersubmatches)) {
				for($i=0; $i < count($innersubmatches[0]); $i++) {
					unset($params);
					$inm_tmp = explode('}',$innersubmatches[0][$i]);
					$tag_parts = explode(':',$inm_tmp[0]);
					$tag = substr($tag_parts[0],1);
					$call_function = $tag_parts[1];
					$inm_tmp = explode('{',$inm_tmp[1]);
					$params['_inner'] = $this->cleanquote($inm_tmp[0]);
					$HTMLIN = str_ireplace($innersubmatches[0][$i].'}',$this->build($tag,$call_function,$params),$HTMLIN);
				}
			}


			if(preg_match_all('/{'.$tag.'}[^{]*{\/'.$tag.'}/i',$HTMLIN,$innermatches)) {
				for($i=0; $i < count($innermatches[0]); $i++) {
					unset($params);
					$inm_tmp = explode('}',$innermatches[0][$i]);
					$tag = $call_function = substr($inm_tmp[0],1);
					$inm_tmp = explode('{',$inm_tmp[1]);
					$params['_inner'] = $this->cleanquote($inm_tmp[0]);
					$HTMLIN = str_ireplace($innermatches[0][$i],$this->build($tag,$call_function,$params),$HTMLIN);
				}
			}

			if(preg_match_all('/{'.$tag.'[^}]*}/i',$HTMLIN,$matches)) {
				for($i=0; $i < count($matches[0]); $i++) {
					unset($params);
					// modified for WordPress
					$tmp = $this->cleanquote($matches[0][$i]);
					// -- end modification
					preg_match_all('/\s[a-z]*="[^"]+/i',$tmp,$tmp);
					for($t=0; $t < count($tmp[0]); $t++) {
						$parts = explode('="',$tmp[0][$t]);
						$params[trim($parts[0])] = trim(str_replace($this->find,$this->replace,$parts[1]));
					}

					if(preg_match('/{[^\s]+:[^\s]+/',$matches[0][$i])) { 
						$tmp_tmp = explode(' ',$matches[0][$i]);
						$tmp_tmp = explode(':',$tmp_tmp[0]);
						$call_function = str_replace(array(':','}'),'',$tmp_tmp[1]);
					} else { 
						$call_function = $tag;
					}

					$HTMLIN = str_ireplace($matches[0][$i],$this->build($tag,$call_function,$params),$HTMLIN);
				}
			}
		}
		return $HTMLIN;
	}
	
	
	############################################ ACTIVATE PLUGINS
	
	
	function build($tag,$call_function,$params,$return='') {
		if(function_exists('runkit_lint_file')) {
			if(!runkit_lint_file(dirname(__FILE__).$this->_plugin_dir.$tag.'.php')) {
				return '<!-- PLUGIN CONTAINS ERRORS -->';
			}
		}

		include_once(dirname(__FILE__).$this->_plugin_dir.$tag.'.php');
		$tagf = 'HTMLe_'.$tag;
		$tagfs = $tagf.'_'.$call_function;

		if($tag != $call_function) {
			if(function_exists($tagfs)) {
				return $tagfs($params);
			} elseif(is_a($this->current_class, $tagf)) {
				if(method_exists($this->current_class,$tagfs)) {
					return $this->current_class->$tagfs($params);
				} elseif(method_exists($this->current_class,$call_function)) {
					return $this->current_class->$call_function($params);
				} else {
					return '<!-- SUB PLUGIN OBJECT FAILED TO LOAD -->';
				}
			} else {
				return '<!-- SUB PLUGIN FAILED TO LOAD -->';
			}
		} else {
			if(function_exists($tagf)) {
				return $tagf($params);
			} elseif(class_exists($tagf)) {
				$this->current_class = new $tagf($params); 
			} else {
				return '<!-- PLUGIN FAILED TO LOAD -->';
			}
		}
	}
	
	
	############################################ END CLASS

}