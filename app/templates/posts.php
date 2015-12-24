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
	<title>Edit category</title>
	<?php echo "<script src=" . Config::$configuration['rootpath'] . "/js/forms.js></script>";?>
</head>
<body>
	<label>Select post</label>
	<form action="" method="get">
		<input type="hidden" name="controller" value="post" /> 
		<input type="hidden" name="action" value="select" />
		<select name="id">
			<?php
			foreach ($posts as $post) {
				echo "<option value={$post['id']}>{$post['title']}</option>";
			}
			?>
		</select>
		<input type="submit" value="Select"> 
	</form>
	<br />
	<br />
	<br />
	<a href=<?php echo Config::$configuration['rootpath'] . '/index.php?controller=post&amp;action=add'; ?>>Add post</a>
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



