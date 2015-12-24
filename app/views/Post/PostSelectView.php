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

class PostSelectView {
	private $post_controller;
	
	public function __construct(PostController $post_controller) {
		$this->post_controller = $post_controller;
	}
	
	/*
	* Methods
	*/
	
	public function output() {
		if ($this->post_controller->get_post()->get_valid_id()) {
			$post_body = $this->post_controller->get_post()->get_body();
			$post_title = $this->post_controller->get_post()->get_title();
			$post_last_modified = $this->post_controller->get_post()->get_date();
			$post_username = $this->post_controller->get_post()->get_username();
			$post_id = $this->post_controller->get_post()->get_id();
			$nested_category_id = $this->post_controller->get_post()->get_nested_category_id();
			if ($this->post_controller->get_post()->get_write()) {
				require_once dirname(dirname(dirname(__FILE__))) . '/templates/postSelectedHomeReadWrite.php';
			}
			else {
				require_once dirname(dirname(dirname(__FILE__))) . '/templates/postSelectedHome.php';
			}
		}
		else {
			require_once dirname(dirname(dirname(__FILE__))) . '/templates/404Error.php';
		}	
	}
}
?>