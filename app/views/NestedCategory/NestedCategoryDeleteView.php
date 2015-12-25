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

class NestedCategoryDeleteView extends View {

	/*
	* Methods
	*/
	
	public function output() {
		$nested_category_id = $this->controller->get_nested_category()->get_id();
		if (isset($nested_category_id)) {
			header("Location: " . Config::$configuration["baseurl"] . "/index.php?controller=nested_category&action=select&nested_category_id={$nested_category_id}");
		}
		else {
			$username = $this->controller->get_username();
			$nested_categories = $this->controller->get_user_nested_categories();
			require_once dirname(dirname(dirname(__FILE__))) . '/templates/userProfileHome.php';
		}
	}
}
?>