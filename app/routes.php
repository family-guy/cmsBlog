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

/**
* @param string <code>$template</code>; UserController, PostController or NestedCategoryController object <code>$controller</code>
*/
function render($template, $controller) {
	require_once dirname(__FILE__) . $template;
}

/**
* Converts underscore_case to camelCase
* @param string <code>$name</code>
* @return string
*/
function uscore_to_cam($name) {
	$exploded = explode('_', $name);
	foreach ($exploded as &$x) {
		$x = ucfirst($x);
	}
	return implode($exploded);
}

/**
* MVC-type wrapper function. Accepts variable number of arguments
* @param string <code>$controller_type</code>; UserController, PostController or NestedCategoryController object <code>$controller</code>; string <code>$action</code>; Db object <code>$db</code>
*/
function set_render_view($controller_type, $controller, $action, $db) {
	call_user_func_array([$controller, $action], array_slice(func_get_args(), 3)); // array_slice might return empty array
	$view_name = uscore_to_cam($controller_type) . uscore_to_cam($action) . 'View';
	$view = new $view_name($controller);
	$view->output(); 
}

$controllers = ['user' => ['login', 'sign_up', 'logout', 'delete', 'edit_email', 'edit_pw'],
				'nested_category' => ['show_posts', 'select', 'edit_name', 'add', 'delete'],
				'post' => ['select', 'add', 'edit', 'delete', 'index']]; 
				
$templates = ['404_error' => '/templates/404Error.php',
			  'new_email' => '/templates/newEmail.php',
			  'new_pw' => '/templates/newPw.php',
		  	  'edit_post' => '/templates/editPost.php'];

if (array_key_exists($controller, $controllers) && in_array($action, $controllers[$controller])) {
	// initialise models. Use Session variables if available
	if (isset($_SESSION['user'])) {
		$user = unserialize($_SESSION['user']);
	}
	else {
		$user = new User();
	}
	if (isset($_SESSION['post'])) {
		$post = unserialize($_SESSION['post']);
	}
	else {
		$post = new Post();
	}
	if (isset($_SESSION['nested_category'])) {
		$nested_category = unserialize($_SESSION['nested_category']);
	}
	else {
		$nested_category = new NestedCategory();
	}
	// initialise controllers
	$user_controller = new UserController($user);
	$post_controller = new PostController($post);
	$nested_category_controller = new NestedCategoryController($nested_category);
	// route appropriate response as per values of <code>$controller</code> and <code>$action</code> set in index.php
	switch ($controller) {
		case 'user':
			switch ($action) {
				case 'login':
					if ($user_controller->get_user()->get_auth()) { // user already authorised. No need to recontact database
						$user_login_view = new UserLoginView($user_controller);
						$user_login_view->output();
					}
					elseif (isset($_POST['ident']) && isset($_POST['pw'])) {
						set_render_view($controller, $user_controller, $action, $db, $_POST['ident'], $_POST['pw']);
					}
					else {
						render($templates['404_error'], $user_controller);
					}
					break;
				case 'logout':
					set_render_view($controller, $user_controller, $action);
					break;
				case 'sign_up':
					if (isset($_POST['username']) && isset($_POST['email_address']) && isset($_POST['new_pw'])) {
						set_render_view($controller, $user_controller, $action, $db, $_POST['username'], $_POST['email_address'], $_POST['new_pw']);
					}	
					else {
						render($templates['404_error'], $user_controller);			
					}
					break;
				case 'delete':
					if ($user_controller->get_user()->get_auth()) { 
						set_render_view($controller, $user_controller, $action, $db);
					}
					break;
				case 'edit_email':
					if ($user_controller->get_user()->get_auth()) {
						if (isset($_POST['new_email']) && isset($_POST['pw'])) {
							set_render_view($controller, $user_controller, $action, $db, $_POST['new_email'], $_POST['pw']);
						}
						else {
							render($templates['new_email'], $user_controller);
						}
					}
					else {
						render($templates['404_error'], $user_controller);		
					}
					break;
				case 'edit_pw':
					if ($user_controller->get_user()->get_auth()) {
						if (isset($_POST['current_pw']) && isset($_POST['new_pw']) && isset($_POST['confirm_new_pw'])) {
							set_render_view($controller, $user_controller, $action, $db, $_POST['current_pw'], $_POST['new_pw'], $_POST['confirm_new_pw']);
						}
						else {
							render($templates['new_pw'], $user_controller);
						}
					}
					else {
						render($templates['404_error'], $user_controller);		
					}
					break;
				default:
					render($templates['404_error'], $user_controller);		
			}
			break;
			
		case 'nested_category':
			switch ($action) {
				case 'show_posts':
					if (isset($_GET['nested_category_id']) && is_numeric($_GET['nested_category_id'])) {
						set_render_view($controller, $nested_category_controller, $action, $db, $_GET['nested_category_id']);
					}
					else {
						render($templates['404_error'], $nested_category_controller);	
					}
					break;
				case 'select':
					if (isset($_GET['nested_category_id']) && is_numeric($_GET['nested_category_id'])) {
						set_render_view($controller, $nested_category_controller, $action, $db, $_GET['nested_category_id']);
					}
					else {
						render($templates['404_error'], $nested_category_controller);	
					}
					break;
				case 'edit_name':
					if (isset($_POST['new_category_name'])) {
						set_render_view($controller, $nested_category_controller, $action, $db, $_POST['new_category_name'], $user_controller);
					}
					else {
						render($templates['404_error'], $nested_category_controller);	
					}
					break;
				case 'add':
					if (isset($_POST['new_category_name'])) {
						set_render_view($controller, $nested_category_controller, $action, $db, $_POST['new_category_name'], $user_controller);
					}
					else {
						render($templates['404_error'], $nested_category_controller);	
					}
					break;
				case 'delete':
					if (isset($_GET['nested_category_id']) && is_numeric($_GET['nested_category_id'])) {
						set_render_view($controller, $nested_category_controller, $action, $db, $_GET['nested_category_id'], $user_controller);
					}
					else {
						render($templates['404_error'], $nested_category_controller);	
					}
					break;
				default:
					render($templates['404_error'], $nested_category_controller);	
			}
			break;
			
		case 'post':
			switch ($action) {
				case 'index':
					set_render_view($controller, $post_controller, $action, $db);
					break;
				case 'select':
					if (isset($_GET['id']) && is_numeric($_GET['id'])) {
						set_render_view($controller, $post_controller, $action, $db, $_GET['id'], $user_controller, $nested_category_controller);
					}
					else {
						render($templates['404_error'], $post_controller);	
					}
					break;
				case 'add':
					if ($user_controller->get_user()->get_auth()) {
						set_render_view($controller, $post_controller, $action, $db, $user_controller, $nested_category_controller);
					}
					else {
						render($templates['404_error'], $post_controller);	
					}
					break;
				case 'edit':
					if (isset($_POST['new_title']) && isset($_POST['new_body'])) {
						set_render_view($controller, $post_controller, $action, $db, $_POST['new_title'], $_POST['new_body'], $user_controller); 
					}
					else {
						render($templates['edit_post'], $post_controller);
					}
					break;
				case 'delete':
					if (isset($_GET['id']) && is_numeric($_GET['id'])) {
						set_render_view($controller, $post_controller, $action, $db, $_GET['id'], $nested_category_controller);
					}
					else {
						render($templates['404_error'], $post_controller);	
					}
					break;
				default:
					render($templates['404_error'], $post_controller);	
			}
			break;
			
		default:
			$controller = null;
			render($templates['404_error'], $controller);		
	}
}
else {
	$controller = null;
	render($templates['404_error'], $user_controller);
}
?>