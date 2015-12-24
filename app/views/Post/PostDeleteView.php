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

class PostDeleteView { 
	private $post_controller;
	
	public function __construct(PostController $post_controller) {
		$this->post_controller = $post_controller;
	}
	
	/*
	* Methods
	*/
	
	public function output() {
		$nested_category_id = $this->post_controller->get_post()->get_nested_category_id();
		header("Location: " . Config::$configuration['baseurl'] . "/index.php?controller=nested_category&action=show_posts&nested_category_id={$nested_category_id}");
	}
}
?>