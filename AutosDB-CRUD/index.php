<?php
	session_start();

	require_once "pdo.php";

	if(isset($_SESSION['name'])){
		$stmt=$pdo->query("SELECT autos_id,make,model,year,mileage FROM autos");
		$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
	}
?>




<!DOCTYPE html>
<html>
<head>
<title>Hasin Raihan Shovon</title>
</head>

<body>
<div class="container">
<h1>Welcome to the Automobile Database</h1>

<?php

	if(isset($_SESSION['success'])){
		echo('<p style="color:green;">'.$_SESSION['success']."</p>\n");
		unset($_SESSION['success']);
	}

	if(isset($_SESSION['name'])){

		if(empty($rows)){
			echo('<p>No rows found</p>');
		}

		else{
			echo('<table border="1">');
			echo('<tr>');
			echo('<th>Make</th>');
			echo('<th>Model</th>');
			echo('<th>Year</th>');
			echo('<th>Mileage</th>');
			echo('<th>Action</th>');
			echo('</tr>');

			foreach($rows as $row){
				echo('<tr>');
				echo('<td>');
				echo(htmlentities($row['make']));
				echo('</td>');
				echo('<td>');
				echo(htmlentities($row['model']));
				echo('</td>');
				echo('<td>');
				echo(htmlentities($row['year']));
				echo('</td>');
				echo('<td>');
				echo(htmlentities($row['mileage']));
				echo('</td>');
				echo('<td>');
				echo('<a href="edit.php?autos_id='.$row['autos_id'].'">Edit</a>');
				echo('/');
				echo('<a href="delete.php?autos_id='.$row['autos_id'].'">Delete</a>');
				echo('</td>');
				echo('</tr>');
			}

			echo('</table>');
		}

		echo('<p><a href="add.php">Add New Entry</a></p>');
		echo('<p><a href="logout.php">Logout</a></p>');
	}

	else{
		echo('<p>');
		echo('<a href="login.php">Please log in</a>');
		echo('</p>');
		echo('<p>');
		echo('Attempt to <a href="add.php">add data</a> without logging in');
		echo('</p>');
	}

?>

</div>
</body>
</html>
