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

class PHPADD_ClassFinder
{
	/**
	 * @var string
	 */
	private $path;
	
	/**
	 * @var PHPADD_Filterable
	 */
	private $directoryFilter;
	
	/**
	 * @var PHPADD_Filterable
	 */
	private $classFilter;

	/**
	 * @param string $path Path of the directory to scan
	 * @param PHPADD_Filterable $directoryFilter Filter used to exclude directories
	 * @param PHPADD_Filterable $classFilter Filter used to exclude classes
	 */
	public function __construct($path, PHPADD_Filterable $directoryFilter,
		PHPADD_Filterable $classFilter
	) {
		$this->path = $path;
		$this->directoryFilter = $directoryFilter;
		$this->classFilter = $classFilter;
	}

	/**
	 * Get the classes in the files of the given path.
	 *
	 * @return array
	 */
	public function getList()
	{
		$directory = new RecursiveDirectoryIterator($this->path);
		$iterator = new RecursiveIteratorIterator($directory);
		$files = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

		$classes = array();

		foreach ($files as $file) {
			$fileName = $file[0];
			if (!$this->directoryFilter->isFiltered($fileName)) {
				$classes[$fileName] = $this->processFile($fileName);
			}
		}

		return $classes;
	}

	/**
	 * Get the classes inside the given file.
	 * 
	 * @param string $fileName Path of the file to process
	 * @return array
	 */
	private function processFile($fileName)
	{
		$classes = array();
		$namespace = '';
		$tokens = token_get_all(file_get_contents($fileName));
		
		foreach ($tokens as $i => $token) {
			if ($token[0] == T_NAMESPACE) {
				$namespace = $this->getNamespaceName($tokens, $i);
			}
			if ($token[0] == T_CLASS) {
				$className = $this->getClassName($tokens, $i);
				if (!$this->classFilter->isFiltered($className)) {
					$classes[] = $namespace.$className;
				}
			}
		}

		return $classes;
	}

	/**
	 * Get the class name in an array of tokens
	 * 
	 * @param array $tokens
	 * @param int $i Ignore tokens before i (token[i] is T_CLASS)
	 * @return string class name
	 */
	private function getClassName(array $tokens, $i)
	{
		for ($i; $i < count($tokens); $i++) {
			if ($tokens[$i][0] == T_STRING) {
				return $tokens[$i][1];
			}
		}
	}
	
	private function getNamespaceName(array $tokens, $i)
	{
		$ns = '';
		
		for ($i; $i < count($tokens); $i++) {
			
			if ($tokens[$i] === ';') return $ns . '\\';;
			
			switch ($tokens[$i][0]) {
				case T_STRING:
					if ($ns === '') {
						$ns = '\\';
					}
					$ns .= $tokens[$i][1];
					break;
					
				case T_NS_SEPARATOR:
					$ns .= $tokens[$i][1];
					break;
			}
		}
	}
}
