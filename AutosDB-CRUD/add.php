<?php
session_start();
require_once "pdo.php";

if(!isset($_SESSION['name'])){
	die('ACCESS DENIED');
}

if(isset($_POST['cancel'])){
	header('Location: index.php');
	return;
}


if(isset($_POST['make']) && isset($_POST['model']) && isset($_POST['year']) && isset($_POST['mileage'])){
	
	if(strlen($_POST['make'])<1 || strlen($_POST['model'])<1 || strlen($_POST['year'])<1 || strlen($_POST['mileage'])<1){
		$_SESSION['failure']="All fields are required";
	}
	else if(!is_numeric($_POST['year'])){
		$_SESSION['failure']="Year must be an integer";
	}
	else if(!is_numeric($_POST['mileage'])){
		$_SESSION['failure']="Mileage must be an integer";
	}
	else{

		$_SESSION['success']="Record added";
		$sql="INSERT INTO autos (make,model,year,mileage) VALUES(:make,:model,:year,:mileage)";
		$stmt=$pdo->prepare($sql);
		$stmt->execute(array(
			':make'=>$_POST['make'],
			':model'=>$_POST['model'],
			':year'=>$_POST['year'],
			':mileage'=>$_POST['mileage']
		));

		header('Location: index.php');
		return;
	}

	header('Location: add.php');
	return;
}


?>


<!DOCTYPE html>
<html>
<head>
<title>Hasin Raihan Shovon</title>
</head>

<body>
<div class="container">
<h1>Tracking Autos</h1>

<?php
if(isset($_SESSION['failure'])){
	echo('<p style="color: red;">'.$_SESSION['failure']."</p>\n");
	unset($_SESSION['failure']);
}
?>

<form method="post">
<p>Make:
<input type="text" name="make" size="60"/></p>
<p>Model:
<input type="text" name="model"/></p>
<p>Year:
<input type="text" name="year"/></p>
<p>Mileage:
<input type="text" name="mileage"/></p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="cancel">
</form>

</div>
</body>
</html>
