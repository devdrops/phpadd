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

class PHPADD_Publisher_Html extends PHPADD_Publisher_Abstract
{
	/**
	 * Renders the mess in an HTML page.
	 *
	 * @param PHPADD_Result_Analysis $mess
	 */
	public function publish(PHPADD_Result_Analysis $mess)
	{
		$output = $this->getHtmlPage($mess);
		file_put_contents($this->destination, $output);
	}

	private function getHtmlPage(PHPADD_Result_Analysis $mess)
	{
		$output = $this->getHeader();
		$output .= $this->getStats($mess);
		
		if (!$this->statsOnly) {
			foreach ($mess->getDirtyFiles() as $file) {
				$output .= "\t<h1>".$file->getName()."</h1>" . PHP_EOL;

				foreach ($file->getClasses() as $class) {

					$output .= "\t\t<h2>".$class->getName().":".$class->getStartline()."</h2>" . PHP_EOL;

					foreach ($class->getMethods() as $method) {
						$output .= "\t\t\t<h3>Method: " . $method->getName() . "</h3>\t\t\t<ul>" . PHP_EOL;
						$output .= "\t\t\t\t<li>" . implode('</li><li>', $method->toList()) . '</li>' . PHP_EOL;
						$output .= "\t\t\t</ul>\n";
					}
				}
			}
		}

		$output .= $this->getFooter();
		return $output;
	}

	private function getHeader()
	{
		return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>phpadd result</title>
		<style type="text/css">
			h1 {
				font-size: 19px;
				border-bottom: 1px solid #c0c0c0;
				margin-top: 19px;
			}
			h2 {
				font-size: 16px;
				margin: 2px 0 0 9px;
			}
			h3 {
				font-size: 13px;
				margin: 8px 0 0 22px;
			}
			ul {
				list-style-type: circle;
				font-size: 14px;
				margin: 7px 0 13px 0;
			}
			code {
				font-size: 13px;
			}
			.note {
				color: #909090;
			}
		</style>
    </head>
    <body>' . PHP_EOL;
	}

	private function getFooter()
	{
		$powered = '<p class="note">Generated by <a href="http://blog.webmatters.it/phpadd/">PHPADD</a> on '.
					date('Y-m-d H:i:s') . '.</p>' . PHP_EOL;

		return "$powered</body></html>";
	}

	private function getStats(PHPADD_Result_Analysis $mess)
	{
		$helper = new PHPADD_Stats();
		$stats = $helper->getStats($mess);

		return
			"<p class='note'>Scanned {$stats['files-count']} files with {$stats['methods-count']} methods in their classes. " .
			"{$stats['regular-blocks']} methods ({$stats['regular-blocks-percentage']}%) are regular, " .
			"{$stats['missing-blocks']} methods ({$stats['missing-blocks-percentage']}%) have no docblock, " .
			"{$stats['outdated-blocks']} methods ({$stats['outdated-blocks-percentage']}%) have an outdated docblock.</p>" . PHP_EOL;
	}

}