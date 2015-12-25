<?php
/*
Copyright (C) 2015  Guy R. King

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or any 
later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>
*/

class NestedCategoryDeleteView {
	public $nested_category_controller;
	
	public function __construct(NestedCategoryController $nested_category_controller) {
		$this->nested_category_controller = $nested_category_controller;
	}
	
	/*
	* Methods
	*/
	
	public function output() {
		if (null !== $this->nested_category_controller->get_nested_category()->get_id()) {
			$id = $this->nested_category_controller->get_nested_category()->get_id();
			header("Location: " . Config::$configuration["baseurl"] . "/index.php?controller=nested_category&action=select&nested_category_id={$id}");
		}
		else {
			$username = $this->nested_category_controller->get_username();
			$nested_categories = $this->nested_category_controller->get_user_nested_categories();
			require_once dirname(dirname(dirname(__FILE__))) . '/templates/userProfileHome.php';
		}
	}
}
?>