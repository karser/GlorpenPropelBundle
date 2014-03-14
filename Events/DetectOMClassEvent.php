<?php

namespace Glorpen\Propel\PropelBundle\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Arkadiusz Dzięgiel
 */
class DetectOMClassEvent extends Event {
	private $cls, $detectedClass, $row, $col;
	
	public function __construct($cls, $row, $col){
		$this->cls = $cls;
		$this->row = $row;
		$this->col = $col;
	}
	
	public function getClass(){
		return $this->cls;
	}
	
	public function setDetectedClass($cls){
		$this->detectedClass = $cls;
	}
	
	public function isDetected(){
		return $this->detectedClass !== null;
	}
	
	public function getDetectedClass(){
		return $this->detectedClass;
	}
	
	public function getRow(){
		return $this->row;
	}
	
	public function getCol(){
		return $this->col;
	}
}
