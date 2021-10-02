<?php
session_start();

require_once "pdo.php";
require_once "util.php";

$stmt=$pdo->query("SELECT profile_id,first_name,last_name,headline FROM Profile");
$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
<title>Hasin Raihan Shovon</title>
</head>

<body>
<div class="container">
<h1>Hasin Raihan Shovon's Resume Registry</h1>


<?php


flashMessage();

if(isset($_SESSION['name'])){
	echo('<p>');
	echo('<a href="logout.php"> Logout </a>');
	echo('</p>');
}
else{
	echo('<p>');
	echo('<a href="login.php">Please log in</a>');
	echo('</p>');
}

if(!empty($rows)){
	echo('<table border="1">');
	echo('<tr>');
	echo('<th>Name</th>');
	echo('<th>Headline</th>');

	if(isset($_SESSION['name'])){
		echo('<th>Action</th>');
	}

	echo('</tr>');

	foreach($rows as $row){
		echo ('<tr>');
		echo ('<td>');
		echo ('<a href="view.php?profile_id='.htmlentities($row['profile_id']).'">');
		echo (htmlentities($row['first_name']));
		echo (' ');
		echo (htmlentities($row['last_name']));
		echo ('</a></td>');
		echo ('<td>');
		echo (htmlentities($row['headline']));
		echo ('</td>');

		if(isset($_SESSION['name'])){
			echo ('<td>');
			echo ('<a href="edit.php?profile_id='.htmlentities($row['profile_id']).'"> EDIT </a>');
			echo ('  ');
			echo ('<a href="delete.php?profile_id='.htmlentities($row['profile_id']).'"> DELETE </a>');
			echo ('</td>');
		}

		echo ('</tr>');
	}
	echo('</table>');
}

if(isset($_SESSION['name'])){
	echo ('<p>');
	echo ('<a href="add.php"> Add New Entry </a>');
	echo ('</p>');
}

?>


</div>
</body>
</html>
