<!--
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
	<title><?php echo $nested_category_name;?></title>
	<?php echo "<script src=" . Config::$configuration['rootpath'] . "/js/forms.js></script>";?>
</head>
<body>
	<?php echo 'You are in: <strong>' . strtoupper($nested_category_name) . '</strong>';?>
	<br />
	<br />
	<br />
	<!--Edit category name text box-->
	<label>Edit category name</label>
	<script type="text/javascript">
	var body = document.body;
	var rootPath = '<?php echo Config::$configuration["rootpath"]; ?>';
	var editCategory = makeForm('post', rootPath + '/index.php?controller=nested_category&action=edit_name', ['new_category_name'], ['text'], [''], 'Save');
	body.appendChild(editCategory);
	</script>
	<br />
	<br />
	<br />
	<!--Select sub-category dropdown-->
	<label>Select sub-category</label>
	<form action="" method="get">
		<input type="hidden" name="controller" value="nested_category" /> 
		<input type="hidden" name="action" value="select" />
		<select name="nested_category_id">
			<?php
			foreach ($nested_category_children as $child) {
				echo "<option value={$child['id']}>{$child['name']}</option>";
			}
			?>
		</select>
		<input type="submit" value="Select">
	</form>
	<br />	
	<br />
	<br />
	<!--Add new sub-category text box-->
	<label>Add new sub-category</label>
	<script type="text/javascript">
	var body = document.body;
	var rootPath = '<?php echo Config::$configuration["rootpath"]; ?>';
	var addCategory = makeForm('post', rootPath + '/index.php?controller=nested_category&action=add', ['new_category_name'], ['text'], [''], 'Add');
	body.appendChild(addCategory);
	</script>
	<br />
	<br />
	<br />
	<!--Delete sub-category dropdown-->
	<label>Delete sub-category</label>
	<form action="" method="get">
		<input type="hidden" name="controller" value="nested_category" /> 
		<input type="hidden" name="action" value="delete" />
		<select name="nested_category_id">
			<?php
			foreach ($nested_category_children as $child) {
				echo "<option value={$child['id']}>{$child['name']}</option>";
			}
			?>
		</select>
		<input type="submit" value="Delete"> 
	</form>
	<br />
	<br />
	<br />
	<a href="./">Back</a>
	<br />
	<br />
	<br />
	<a href=<?php echo Config::$configuration['rootpath'] . "/index.php?controller=user&amp;action=logout"; ?>>Logout</a>
</body>
</html>