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

class NestedCategoryController {
	private $nested_category;
	private $username; // when new nested category is added, edited or deleted, store user's username here
	private $user_nested_categories; // when new nested category added, edited or deleted, store user's nested categories here

	public function __construct(NestedCategory $nested_category) {
		$this->nested_category = $nested_category;
	}
	
	/*
	* Methods
	*/
	
	/**
	* @return NestedCategory object or null
	*/
	public function get_nested_category() {
		return $this->nested_category;
	}
	/**
	* @param NestedCategory object <code>$nested_category</code>
	*/
	public function set_nested_category(NestedCategory $nested_category) {
		$this->nested_category = $nested_category;
	}
	/**
	* @return string or null
	*/
	public function get_username() {
		return $this->username;
	}
	/**
	* @return null or array of associative arrays; keys are 'name', 'id', 'parent_id', 'gauche', 'droite'
	*/
	public function get_user_nested_categories() {
		return $this->user_nested_categories;
	}
	/**
	* Updates <code>$posts</code> property of <code>$nested_category</code> property and saves NestedCategory object into Session variable
	* @param Db object <code>$db</code>, string <code>$id</code>
	*/
	public function show_posts(Db $db, $id) {
		$id_clean = filter_var($id, FILTER_SANITIZE_STRING);
		$this->nested_category->set_id((int)$id_clean);
		$this->nested_category->posts($db);
		$_SESSION['nested_category'] = serialize($this->nested_category);
	}
	/**
	* Updates <code>$id</code> property of <code>$nested_category</code> property. If given id is valid, saves NestedCategory object into Session variable
	* @param Db object <code>$db</code>, string <code>$id</code>
	*/
	public function select(Db $db, $id) {
		$id_clean = filter_var($id, FILTER_SANITIZE_STRING);
		$this->nested_category->set_id((int)$id_clean);
		$this->nested_category->valid_id($db);
		if ($this->nested_category->get_valid_id()) {
			$this->nested_category->db_props($db);
			$this->nested_category->children($db);
			$_SESSION['nested_category'] = serialize($this->nested_category);
		}
	}
	/**
	* Deletes nested category and all descendant categories from database as per given <code>$id</code>
	* Checks if id of nested category being deleted belongs to user. If so, updates <code>$nested_categories</code> property of <code>$user</code> property and saves User object into Session variable
	* @param Db object <code>$db</code>, string <code>$id</code>, UserController object <code>$user_controller</code>
	*/
	public function delete(Db $db, $id, UserController $user_controller) {
		$id_clean = filter_var($id, FILTER_SANITIZE_STRING);
		$user = $user_controller->get_user();
		$user_nested_categories = $user->get_nested_categories();
		$deletion_valid = false;
		foreach ($user_nested_categories as $user_nested_category) {
			if ($user_nested_category['id'] == $id_clean) {
				$deletion_valid = true;
			}
		}
		if ($deletion_valid) {
			$nested_cat_to_del = new NestedCategory();
			$nested_cat_to_del->set_id((int)$id_clean);
			$nested_cat_to_del->db_props($db);
			$nested_cat_to_del->delete($db);
			// delete all descendant categories
			$nested_cat_to_del->children($db);
			$nested_cat_to_del->delete_descendants($db);
			// adjust values in <code>nested_categories</code> table accordingly
			if (null !== $nested_cat_to_del->get_droite() && null !== $nested_cat_to_del->get_gauche()) {
				$diff = $nested_cat_to_del->get_droite() - $nested_cat_to_del->get_gauche();
				$nested_cat_to_del->nested_cats_to_update_after_del($db);
				$nested_cats_to_update = $nested_cat_to_del->get_nested_cats_to_update_after_del();
				if (isset($nested_cats_to_update)) {
					foreach ($nested_cats_to_update as $x) {
						$gauche = $x['gauche'];
						$droite = $x['droite'];
						$nested_cat_to_update = new NestedCategory();
						$nested_cat_to_update->set_id((int)$x['id']);
						if ($gauche > $nested_cat_to_del->get_droite()) {
							$nested_cat_to_update->set_gauche($gauche - $diff - 1);
						}
						$nested_cat_to_update->set_droite($droite - $diff - 1);
						$nested_cat_to_update->save($db);
					}
				}
			}
			// userProfileHome.php
			$this->username = $user->get_username(); 
			$user->nested_categories($db); 
			$this->user_nested_categories = $user->get_nested_categories(); 
			$_SESSION['user'] = serialize($user);
		}
	}	
	/**
	* Edits name of nested category and updates database
	* Checks if id of nested category being edited belongs to user. If so, updates <code>$nested_categories</code> property of <code>$user</code> property and saves User object into Session variable
	* @param Db object <code>$db</code>, string <code>$name</code>, UserController object <code>$user_controller</code>
	*/
	public function edit_name(Db $db, $name, UserController $user_controller) {
		$name_clean = filter_var($name, FILTER_SANITIZE_STRING);
		$id = $this->nested_category->get_id();
		$user = $user_controller->get_user();
		$user_nested_categories = $user->get_nested_categories();
		$edit_valid = false;
		foreach ($user_nested_categories as $user_nested_category) {
			if ($user_nested_category['id'] == $id) {
				$edit_valid = true;
			}
		}
		if ($edit_valid) {
			$this->nested_category->set_name($name_clean);
			$this->nested_category->save($db);
		}
		// userProfileHome.php
		$this->username = $user->get_username();
		$user->nested_categories($db); 
		$this->user_nested_categories = $user->get_nested_categories(); 
		$_SESSION['user'] = serialize($user);
	}
	
	// based on the way the post controller add method works i.e. create a new object and don't store in session
	/**
	* Creates a new NestedCategory object and saves into database. If parent of new NestedCategory object is a leaf, all posts in parent category are moved into new NestedCategory object. Thus when adding multiple new NestedCategory objects, first new NestedCategory object should correspond to largest number of posts in parent category
	* @param Db object <code>$db</code>, string <code>$name</code>, UserController object <code>$user_controller</code>
	*/
	public function add(Db $db, $name, UserController $user_controller) {
		$user = $user_controller->get_user();
		$parent_id = $this->nested_category->get_id();
		$user_id = $user->get_id();
		$name_clean = filter_var($name, FILTER_SANITIZE_STRING);
		$nested_category = new NestedCategory();
		$nested_category->set_name($name_clean);
		$nested_category->set_user_id($user_id);
		if ($parent_id == null) {
			$max_droite = NestedCategory::max_droite($db, $user_id);
			if ($max_droite == null) {
				$max_droite = 0;
			}
			$nested_category->set_gauche($max_droite + 1);
			$nested_category->set_droite($max_droite + 2);
			$nested_category->save($db);
		}
		else {
			$nested_category->set_parent_id($parent_id);
			$nested_category->parent_gauche_droite($db);
			$parent_gauche = $nested_category->get_parent_gauche_droite()['gauche'];
			$parent_droite = $nested_category->get_parent_gauche_droite()['droite'];
			$nested_category->set_gauche((int)$parent_droite);
			$nested_category->set_droite($parent_droite + 1);
			$nested_category->save($db);
			// add posts to new category
			$post_results = Post::posts_in_nested_category($db, $parent_id);
			if (isset($post_results)) {
				$new_id = NestedCategory::max_id($db, $user_id);
				foreach ($post_results as $post_result) {
					$post = new Post();
					$post->set_id((int)$post_result['id']);
					$post->set_nested_category_id((int)$new_id);
					$post->save($db);
				}
			}
			// increase parent category droite value by two
			$parent_nested_category = new NestedCategory();
			$parent_nested_category->set_id($parent_id);
			$parent_nested_category->set_droite($parent_droite + 2);
			$parent_nested_category->save($db);
			// increase parent category's ancestors droite value by two
			$nested_category->parent_ancestors($db);
			if ($nested_category->get_parent_ancestors() !== null) {
				foreach ($nested_category->get_parent_ancestors() as $x) {
					$parent_ancestor = new NestedCategory();
					$parent_ancestor->set_id((int)$x['id']);
					$parent_ancestor->set_droite($x['droite'] + 2);
					$parent_ancestor->save($db);
				}
			}
			// for all categories where gauche is > parent category's droite value, increase their gauche and droite values by 2
			$nested_category->parent_siblings_and_parent_siblings_children($db);
			if ($nested_category->get_parent_siblings_and_parent_siblings_children() !== null) {
				foreach ($nested_category->get_parent_siblings_and_parent_siblings_children() as $y) {
					$parent_sibling_or_parent_sibling_child = new NestedCategory();
					$parent_sibling_or_parent_sibling_child->set_id((int)$y['id']);
					$parent_sibling_or_parent_sibling_child->set_gauche($y['gauche'] + 2);
					$parent_sibling_or_parent_sibling_child->set_droite($y['droite'] + 2);
					$parent_sibling_or_parent_sibling_child->save($db);
				}	
			}
		}
		// userProfileHome.php
		$this->username = $user->get_username(); 
		$user->nested_categories($db); 
		$this->user_nested_categories = $user->get_nested_categories(); 
		$_SESSION['user'] = serialize($user);
	}
}
?>