<?php
 
// var_dump($_GET);
 // var_dump($_POST);
 
$errorMessage = null;
$successMessage = null;
 
// connect to the db
$mysqli = new mysqli('127.0.0.1', 'codeup', 'password', 'codeup_mysqli_test_db');
 
// Check for errors
if ($mysqli->connect_errno)
{
    throw new Exception('Failed to connect to MySQL: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
 if (!empty($_POST))
{
	// check for new todo
	if (isset($_POST['todo']))
	{
		if ($_POST['todo'] != "")
		{
			$todo = substr($_POST['todo'], 0, 200);
 
			// add to db
			$stmt = $mysqli->prepare("INSERT INTO todos (item) VALUES (?);");
			$stmt->bind_param("s", $todo);
			$stmt->execute();
 
			$successMessage = "Todo item was added successfully.";
		}
		else
		{
			// show error message
			$errorMessage = "Please input a todo item.";
		}
	}
	else if (!empty($_POST['remove']))
	{
		// remove item from db
		$stmt = $mysqli->prepare("DELETE FROM todos WHERE id = ?;");
		$stmt->bind_param("i", $_POST['remove']);
		$stmt->execute();
 
		$successMessage = "Todo item was removed successfully.";
	}
}
$itemsPerPage = 10;
$currentPage = !empty($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;
 
$todos = $mysqli->query("SELECT * FROM todos LIMIT $itemsPerPage OFFSET $offset;");
$allTodos = $mysqli->query("SELECT * FROM todos;");
 
$maxPage = ceil($allTodos->num_rows / $itemsPerPage);
 
$prevPage = $currentPage > 1 ? $currentPage - 1 : null;
$nextPage = $currentPage < $maxPage ? $currentPage + 1 : null;
 
?>
<html>
<head>
	<title>Todo List</title>
 
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
 
	<!-- Optional theme -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
 
	<!-- Latest compiled and minified JavaScript -->
	<!-- <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script> -->
</head>
<body>
 
<div class="container">
 
	<? if (!empty($successMessage)): ?>
		<div class="alert alert-success"><?= $successMessage; ?></div>
	<? endif; ?>
	<? if (!empty($errorMessage)): ?>
		<div class="alert alert-danger"><?= $errorMessage; ?></div>
	<? endif; ?>
 
	<h1>Todo List</h1>
 
	<table class="table table-striped">
	<? while ($todo = $todos->fetch_assoc()): ?>
		<tr>
			<td><?= $todo['item']; ?></td>
			<td><button class="btn btn-danger btn-sm pull-right" onclick="removeById(<?= $todo['id']; ?>)">Remove</button></td>
		</tr>
	<? endwhile; ?>
	</table>
 
	<div class="clearfix">
		<? if ($prevPage != null): ?>
			<a href="?page=<?= $prevPage; ?>" class="pull-left btn btn-default btn-sm">&lt; Previous</a> 
		<? endif; ?>
 
		<? if ($nextPage != null): ?>
			<a href="?page=<?= $nextPage; ?>" class="pull-right btn btn-default btn-sm">Next &gt;</a> 
		<? endif; ?>
	</div>
 
	<h2>Add Items</h2>
	<form class="form-inline" role="form" action="todo_listb.php" method="POST">
		<div class="form-group">
			<label class="sr-only" for="todo">Todo Item</label>
			<input type="text" name="todo" id="todo" class="form-control" placeholder="Enter todo item" autofocus="autofocus">
		</div>
		<button type="submit" class="btn btn-default">Add Todo</button>
	</form>
</div>
 
<form id="removeForm" action="todo_listb.php" method="post">
	<input id="removeId" type="hidden" name="remove" value="">
</form>
<script>
	var form = document.getElementById('removeForm');
	var removeId = document.getElementById('removeId');
 
	function removeById(id) 
	{
		if (confirm('Are you sure you want to remove item # ' + id + '?')) 
		{
			removeId.value = id;
			form.submit();
		}
	}
 </script>
 </body>
</html>