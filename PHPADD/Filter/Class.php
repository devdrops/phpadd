<?php

/**
 * phpadd - abandoned docblocks detector
 * Copyright (C) 2010 Francesco Montefoschi <francesco.monte@gmail.com>
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
 *
 * @package phpadd
 * @author  Francesco Montefoschi
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL 3.0
 */

class PHPADD_Filter_Class implements PHPADD_Filterable
{
	/**
	 * @var array
	 */
	private $classes;
	
	/**
	 * @param array $classes List of regexp of excluded class names
	 */
	public function __construct(array $classes)
	{
		$this->classes = $classes;
	}
	
	/**
	 * Returns true if the given class has NOT to be scanned.
	 * 
	 * @param string $class
	 * @return bool
	 */
	public function isFiltered($class)
	{
		foreach ($this->classes as $path) {
			if (preg_match("/$path/", $class)) {
				return true;
			}
		}
		
		return false;
	}
}