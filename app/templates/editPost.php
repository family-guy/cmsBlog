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
<meta charset='utf-8'>
<meta name='viewport' content='width=device-width, initial-scale=1'>
<head>
	<title>Edit post</title>
	<?php echo "<script src=" . Config::$configuration['rootpath'] . "/vendor/tinymce/js/tinymce/tinymce.min.js></script>";?>
	<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'></script>
	<script src='http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
	<link rel='stylesheet' href='http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css'>
	<script type="text/javascript">
	tinymce.init({
	    selector: "textarea",
	    plugins: [
	        "advlist autolink lists link image charmap print preview anchor",
	        "searchreplace visualblocks code fullscreen",
	        "insertdatetime media table contextmenu paste"
	    ],
	    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
	});
	</script>
</head>
<body>
	<br />
	<br />
	<br />
	<?php
	$post_title = $controller->get_post()->get_title();
	$post_body = $controller->get_post()->get_body();
	$post_id = $controller->get_post()->get_id();
	?>
	<div class='container'>
	  	  <form action='' method='post' class='form-horizontal' role='form'> 
	  	  	<div class='form-group'>
				<?php echo "<input type='text' name='new_title' value='" . $post_title . "'>"; ?>
				<br />
				<br />
				<br />
	  			<textarea name='new_body' class='form-control' rows=20 wrap='soft'><?php echo $post_body;?></textarea>
	  		</div>
	  	  	<button type='submit' class='btn btn-default'>Save</button>
	  	</form>
	</div>
	<br />
	<br />
	<br />	
	<?php echo "<a href='" . Config::$configuration['rootpath'] . "/index.php?controller=post&amp;action=select&amp;id=" . 
	$post_id . "'>Back</a>"; ?>
	<br />
	<br />
	<br />
	<a href=<?php echo Config::$configuration['rootpath'] . "/index.php?controller=user&amp;action=logout"; ?>>Logout</a>
</body>
</html>

