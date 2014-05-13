<?php

var_dump($_GET);
var_dump($_POST);

// (host, user, password, database_name)
$mysqli = new mysqli('127.0.0.1', 'codeup', 'password', 'ToDo_list');

if ($mysqli->connect_errno) 
{
    throw new Exception('Failed to connect to MySQL: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// require_once("filestore.php");

// $todo = new Filestore("data/todo_list.txt");
// $archive = new Filestore("data/archive.txt");

// $contents_array = $todo->read_lines();
// $archive_array = $archive->read_lines();

if (!empty($_POST) && $_POST ["todo_item"] !== '')
{
    $new_task = $mysqli->prepare("INSERT INTO todo(task) values (?)");
    $new_task->bind_param("s", $_POST['task']);
    $new_task->execute();
}
	
if (isset($_GET['remove'])) 
{
	$key = $_GET["remove"];
	array_push($archive_array, $contents_array[$key]);
	$archive->write_lines($archive_array);
	unset($contents_array[$key]);
	$todo->write_lines($contents_array);
	header("Location: todo-list.php");
	exit(0);
}

if (count($_FILES) > 0 && $_FILES['file1']['error'] == 0) 
{
    // Set the destination directory for uploads
    $upload_dir = '/vagrant/sites/codeup.dev/public/uploads/';
    // Grab the filename from the uploaded file by using basename
    $temp_file = basename($_FILES['file1']['name']);
    // Create the saved filename using the file's original name and our upload directory
    $saved_filename = $upload_dir . $temp_file;
    // Move the file from the temp location to our uploads directory
    move_uploaded_file($_FILES['file1']['tmp_name'], $saved_filename);
    $newfile = new Filestore($saved_filename);
    $append_list = $newfile->read_lines();
    $contents_array = array_merge ($contents_array, $append_list);
    $todo->write_lines($contents_array);
}

?>

<!DOCTYPE html>
<html>
<head>

	<title>ToDo Stuff</title>
	<link rel="stylesheet" href="/css/site.css">
</head>
<body>

	<h3 style="color:blue;text-decoration:underline;">ToDo List</h3>
<ul>

<?php	
	foreach ($contents_array as $key => $value) {
	echo "<li>" . $value . " <a href = \"?remove=$key\">Remove item</a></li>";
}
?>

</ul>
	<h3>Input item to add to todo list</h3>

<form method="POST" enctype="multipart/form-data" action="todo-list.php">
<p>
     <label for="todo_item">New todo item
     <input id="todo_item" name="todo_item" type="text" autofocus="autofocus">

    </p>
    <h3>Upload file</h3>
   <p>
        <label for="file1">File to upload: </label>
        <input type="file" id="file1" name="file1">
    </p>
    <p>
       <input type="submit">
    </p>
    <p>
</form>
</body>
</html>