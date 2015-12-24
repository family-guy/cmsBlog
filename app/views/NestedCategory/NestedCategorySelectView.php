<?php
/*
Copyright (C) 2015  Guy R. King

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>
*/

class NestedCategorySelectView {
	private $nested_category_controller;
	
	public function __construct(NestedCategoryController $nested_category_controller) {
		$this->nested_category_controller = $nested_category_controller;
	}
	
	/*
	* Methods
	*/
	
	public function output() {
		if ($this->nested_category_controller->get_nested_category()->get_valid_id()) {
			$nested_category_name = $this->nested_category_controller->get_nested_category()->get_name();
			$nested_category_id = $this->nested_category_controller->get_nested_category()->get_id();
			$nested_category_children = $this->nested_category_controller->get_nested_category()->get_children();
			require_once dirname(dirname(dirname(__FILE__))) . '/templates/categorySelectedHome.php';
		}
		else {
			require_once dirname(dirname(dirname(__FILE__))) . '/templates/404Error.php';
		}
		
	}
}
?>