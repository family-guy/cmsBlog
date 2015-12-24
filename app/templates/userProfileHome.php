<!-->
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
-->
<!DOCTYPE html>
<html lang='en'>
<meta charset="UTF-8">
<head>
	<title>My profile</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<?php echo "<script src=" . Config::$configuration['rootpath'] . "/js/forms.js></script>";?>
</head>
<body>
	<?php
	// reset nested category stored in session
	$sess_nested_cat = new NestedCategory();
	$_SESSION['nested_category'] = serialize($sess_nested_cat); 
	?>
	<p><?php echo "<strong>Welcome " . strtolower($username) . "</strong>";?></p>
	<p>Here is your profile page</p>
	<!--Account administrator actions dropdown-->
	<label>My account</label>
	<form action="" method="get">
		<input type="hidden" name="controller" value="user" /> 
		<select name="action">
	  	    <option value="delete">Delete account</option>
	  	    <option value="edit_email">Change email</option>
	  	    <option value="edit_pw">Change password</option>
		</select>
		<input type="submit" value="Select"> 
	</form>
	<br />
	<br />
	<br />
	<!--Display categories in tree structure that collapses and expands-->
	My categories
	<ul class="list">
	<?php
	if (isset($nested_categories)) { 
		foreach ($nested_categories as $nested_category) {
			if ($nested_category['parent_id'] == null) {
				$roots[] = $nested_category;
			}
		}
		foreach ($roots as $root) {
			$parent_id = $root['id'];
			$name = $root['name'];
			$result = display_nested_list($nested_categories, $parent_id);
			$item = '<li>';
			if ($result === '') {
				$href = Config::$configuration['rootpath'] . "/index.php?controller=nested_category&amp;action=show_posts&amp;nested_category_id={$parent_id}";
				$item .= "<a href='{$href}'>";
			}
			else {
				$item .= '<a>';
			}
			$item .= "{$name}</a>{$result}</li>";
			echo $item;
		}
	}
	/**
	* Recursively generates list of sub-categories of a particular category (specified by id)
	* @param array of associative arrays (keys are 'name', 'id', 'parent_id', 'gauche', 'droite') <code>$nested_categories</code>, string <code>$parent_id</code>
	* @return string
	*/
	function display_nested_list($nested_categories, $parent_id) {
		foreach ($nested_categories as $nested_category) {
			if ($nested_category['parent_id'] == $parent_id) {
				$filtered_nested_categories[] = $nested_category;
			}
		}
		if (isset($filtered_nested_categories)) {
			foreach ($filtered_nested_categories as $row) {
				$item = '<li>';
				if ($row['droite'] - $row['gauche'] == 1) { // stopping condition will be satisfied on next recursive call
					$href = Config::$configuration['rootpath'] . "/index.php?controller=nested_category&amp;action=show_posts&amp;nested_category_id={$row['id']}";
					$item .= "<a href='{$href}'>";
				}
				else {
					$item .= '<a>';
				}
				$item .= "{$row['name']}</a>" . display_nested_list($nested_categories, $row['id']) . '</li>';
				$items[] = $item;
			}
			return '<ul>' . implode('', $items) . '</ul>';
		}
		else { // stopping condition
			return '';
		}
	}
	?>
	</ul>
	<!--jquery-->
	<script type="text/javascript">
	$('.list > li a').click(function() {
	    $(this).parent().find('ul').toggle();
	});
	</script>
	<br />
	<br />
	<br />
	<!--Select category dropdown-->
	<label>Select category</label>
	<form action="" method="get">
		<input type="hidden" name="controller" value="nested_category" /> 
		<input type="hidden" name="action" value="select" />
		<select name="nested_category_id">
			<?php
			foreach ($roots as $root) {
				echo "<option value={$root['id']}>{$root['name']}</option>";
			}
			?>
		</select>
		<input type="submit" value="Select"> 
	</form>
	<br />
	<br />
	<br />	
	<!--Add new sub-category text box-->	
	<label>Add new category </label>
	<script type="text/javascript">
	var rootPath = '<?php echo Config::$configuration["rootpath"]; ?>';
	var body = document.body;
	var addCategory = makeForm('post', rootPath + '/index.php?controller=nested_category&action=add', ['new_category_name'], ['text'], [''], 'Add');
	body.appendChild(addCategory);
	</script>
	<br />
	<br />
	<br />
	<!--Delete category dropdown-->
	<label>Delete category</label>
	<form action="" method="get">
		<input type="hidden" name="controller" value="nested_category" /> 
		<input type="hidden" name="action" value="delete" />
		<select name="nested_category_id">
			<?php
			foreach ($roots as $root) {
				echo "<option value={$root['id']}>{$root['name']}</option>";
			}
			?>
		</select>
		<input type="submit" value="Delete"> 
	</form>
	<br />
	<br />
	<br />
	<a href=<?php echo Config::$configuration['rootpath'] . "/index.php?controller=user&amp;action=logout"; ?>>Logout</a>
</body>
</html>