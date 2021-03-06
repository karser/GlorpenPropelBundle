<?php
/***
* PropelEventBundle provides dynamic class extending.
* Copyright (C) 2011  Arkadiusz Dzięgiel
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

namespace Glorpen\Propel\PropelBundle\Events;

use Symfony\Component\EventDispatcher\Event;
use \ModelCriteria;

/**
 * Event sent form Query object.
 * @author Arkadiusz Dzięgiel
 */
class QueryEvent extends Event {
	private $query;
	
	public function __construct(ModelCriteria $query){
		$this->query = $query;
	}
	
	public function getQuery(){
		return $this->query;
	}
}