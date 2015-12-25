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

class PostController {
	private $post;
	private $nested_category_id; // when new post is added, store nested cat id of post here
	
	public function __construct(Post $post) {
		$this->post = $post;
	}
	
	/*
	* Methods
	*/
	
	/**
	* @return Post object or null
	*/
	public function get_post() {
		return $this->post;
	}
	/**
	* @param Post object <code>$post</code>
	*/
	public function set_post(Post $post) {
		$this->post = $post;
	}
	/**
	* @return integer or null
	*/
	public function get_nested_category_id() {
		return $this->nested_category_id;
	}
	/**
	* Updates <code>$id</code> property of <code>$post</code> property. If given id is valid, saves Post object into Session variable
	* @param Db object <code>$db</code>, string <code>$id</code>, UserController object <code>$user_controller</code>, NestedCategoryController object <code>$nested_category_controller</code>
	*/
	public function select(Db $db, $id, UserController $user_controller, NestedCategoryController $nested_category_controller) {
		$id_clean = filter_var($id, FILTER_SANITIZE_STRING);
		$this->post->set_id((int)$id_clean);
		$this->post->valid_id($db);
		if ($this->post->get_valid_id()) {
			$this->post->db_props($db);
			$this->post->username($db);
			$this->post->write($db, $user_controller);
			$this->post->set_nested_category_id((int)$nested_category_controller->get_nested_category()->get_id());
			$_SESSION['post'] = serialize($this->post);
		}
	}
	/**
	* Attemps to update <code>$post</code> property with new post title and body and then saves Post object into Session variable and database
	* @param Db object <code>$db</code>, string <code>$title</code>, string <code>$body</code>, UserController object <code>$user_controller</code>
	*/
	public function edit(Db $db, $title, $body, UserController $user_controller) {
		if ($this->post->get_write()) {
			$title_clean = filter_var($title, FILTER_SANITIZE_STRING);
			$this->post->set_title($title_clean);
			$this->post->set_body($body); // body may contain html so cannot use FILTER_SANITIZE_STRING
			$this->post->save($db);
			$_SESSION['post'] = serialize($this->post);
		}
	}
	/**
	* Creates a new Post object and saves into database. Default title 'New Post x', x in {1, 2, ...}; default body 'Enter text here'
	* @param Db object <code>$db</code>, UserController object <code>$user_controller</code>, NestedCategoryController object <code>$nested_category_controller</code>
	*/
	public function add(Db $db, UserController $user_controller, NestedCategoryController $nested_category_controller) {
		if (isset($_SESSION['nb_new_posts'])) {
			$_SESSION['nb_new_posts']++;
		}
		else {
			$_SESSION['nb_new_posts'] = 1;
		}
		$post = new Post();
		$post->set_title("New Post {$_SESSION['nb_new_posts']}");
		$post->set_body('Enter text here');
		$post->set_user_id($user_controller->get_user()->get_id());
		$nested_category_id = $nested_category_controller->get_nested_category()->get_id();
		$post->set_nested_category_id($nested_category_id);
		$post->save($db);
		$this->nested_category_id = $nested_category_id; 
	}
	/**
	* Deletes post from database as per given id
	* Checks id of post being deleted matches id of post stored in Session variable
	* Checks if user has permission to edit post
	* @param Db object <code>$db</code>, string <code>$id</code>, NestedCategoryController object <code>$nested_category_controller</code>
	*/
	public function delete($db, $id, NestedCategoryController $nested_category_controller) {
		$id_clean = filter_var($id, FILTER_SANITIZE_STRING);
		if ($this->post->get_id() === (int)$id_clean) { 
			if ($this->post->get_write()) { 
				$this->post->delete($db);
			}
		}
		$this->post->set_nested_category_id($nested_category_controller->get_nested_category()->get_id());
	}
	/**
	* Updates <code>$last_ten_posts</code> property of <code>$post</code> property 
	* @param Db object <code>$db</code>
	*/
	public function index(Db $db) {
		$this->post->last_ten_posts($db);
	}
}
?>