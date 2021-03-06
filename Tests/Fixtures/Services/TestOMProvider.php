<?php

namespace Glorpen\Propel\PropelBundle\Tests\Fixtures\Services;

use Glorpen\Propel\PropelBundle\Provider\OMClassProvider;

class TestOMProvider implements OMClassProvider {

	protected $to, $map;
	
	public function __construct(\Closure $to, $map){
		$this->to = $to;
		$this->map = $map;
	}
	
	public function getOMClass($row, $col) {
		return call_user_func($this->to, $row, $col);
	}

	public function getMapping() {
		return $this->map;
	}

}
