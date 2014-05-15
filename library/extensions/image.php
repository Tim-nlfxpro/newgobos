<?php
class imageExtension {
	function init($runtime) {
		$this->runtime = $runtime;
		$this->maxHeight = 0;
		$this->maxWidth = 0;
		$this->height = 0;
		$this->width = 0;
		$this->minHeight = 0;
		$this->minWidth = 0;
		$this->path = './';
		$this->im = false;
	}
	
	function readImage($file_path) {
		return $this->load($file_path);
	}
	
	function image($file_path) {
		return $this->load($file_path);
	}
	
	function load($file_path) {
		if(!class_exists('Gmagick', false)) { return false; }
		
		$this->im = new Gmagick();
		$this->im->readImage($file_path);
		$this->path = $file_path;
		$this->im = true;
		return $this;
	}
	
	function setMaxHeight($height) {
		if(!$this->im) { return false; }
		$this->maxHeight = $height;
		return $this;
	}
	
	function setMaxWidth($width) {
		if(!$this->im) { return false; }
		$this->maxWidth = $width;
		return $this;
	}
	
	function setHeight($height) {
		if(!$this->im) { return false; }
		$this->height = $height;
		return $this;
	}
	
	function setWidth($width) {
		if(!$this->im) { return false; }
		$this->width = $width;
		return $this;
	}
	
	function setMinHeight($height) {
		if(!$this->im) { return false; }
		$this->minHeight = $height;
		return $this;
	}
	
	function setMinWidth($width) {
		if(!$this->im) { return false; }
		$this->minWidth = $width;
		return $this;
	}
	
	function saveAs($path) {
		if(!$this->im) { return false; }
		$this->path = $path;
		return $this->save();
	}
	
	function saveTo($path) {
		return $this->saveAs($path);
	}
	
	function saveImageAs($path) {
		return $this->saveAs($path);
	}
	
	function setNewPath($path) {
		if(!$this->im) { return false; }
		$this->path = $path;
		return $this;
	}
	
	function actualHeight() {
		if(!$this->im) { return false; }
		return $this->im->getImageHeight();
	}
	
	function actualWidth() {
		if(!$this->im) { return false; }
		return $this->im->getImageWidth();
	}
	
	function saveImage() {
		return $this->save();
	}
	
	function save() {
		if(!$this->im) { return false; }
		
		if($this->minWidth > 0 && $this->actualWidth() < $this->minWidth || $this->minHeight > 0 && $this->actualHeight > $this->minHeight) {
			$this->im->resizeImage($this->minWidth, $this->minHeight, Gmagick::FILTER_CATROM, 1);
		}
		
		if($this->maxWidth > 0 && $this->actualWidth() > $this->maxWidth || $this->maxHeight > 0 && $this->actualHeight > $this->maxHeight) {
			$this->im->resizeImage($this->maxWidth, $this->maxHeight, Gmagick::FILTER_CATROM, 1);
		}
		
		if($this->width > 0 && $this->actualWidth() != $this->width || $this->height > 0 && $this->actualHeight != $this->height) {
			$this->im->resizeImage($this->width, $this->height, Gmagick::FILTER_CATROM, 1);
		}
		
		$this->im->writeImage($this->path);
		return $this->reset();
	}
	
	function reset() {
		$this->maxHeight = 0;
		$this->maxWidth = 0;
		$this->height = 0;
		$this->width = 0;
		$this->minHeight = 0;
		$this->minWidth = 0;
		
		return $this;
	}
}