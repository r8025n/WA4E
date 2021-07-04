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



if(isset($_POST['autos_id']) && isset($_POST['make']) && isset($_POST['model']) && isset($_POST['year']) && isset($_POST['mileage'])){
	
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

		$_SESSION['success']="Record updated";
		$sql="UPDATE autos SET make=:make, model=:model, year=:year, mileage=:mileage WHERE autos_id=:autos_id";
		$stmt=$pdo->prepare($sql);
		$stmt->execute(array(
			':make' => $_POST['make'],
			':model' => $_POST['model'],
			':year' => $_POST['year'],
			':mileage' => $_POST['mileage'],
			':autos_id' => $_POST['autos_id']
		));

		header('Location: index.php');
		return;
	}

	header("Location: edit.php?autos_id=".$_POST['autos_id']);
	return;
}

if ( ! isset($_GET['autos_id']) ) {
  $_SESSION['failure'] = "Missing autos_id";
  header('Location: index.php');
  return;
}

$stmt=$pdo->prepare("SELECT * FROM autos where autos_id= :id");
$stmt->execute(array(":id"=>$_GET['autos_id']));
$row=$stmt->fetch(PDO::FETCH_ASSOC);

if ($row === false) {
    $_SESSION['failure'] = 'Bad value for user_id';
    header('Location: index.php') ;
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
<h1>Editing Automobile Data</h1>

<?php
if(isset($_SESSION['failure'])){
	echo('<p style="color: red;">'.$_SESSION['failure']."</p>\n");
	unset($_SESSION['failure']);
}

$mk=htmlentities($row['make']);
$mdl=htmlentities($row['model']);
$yr=htmlentities($row['year']);
$mil=htmlentities($row['mileage']);
$id=htmlentities($row['autos_id']);

?>

<form method="post">
<p>Make:
<input type="text" name="make" value="<?= $mk ?>" size="60"/></p>
<p>Model:
<input type="text" name="model" value="<?= $mdl ?>"/></p>
<p>Year:
<input type="text" name="year" value="<?= $yr ?>"/></p>
<p>Mileage:
<input type="text" name="mileage" value="<?= $mil ?>"/></p>
<input type="hidden" name="autos_id" value="<?= $id ?>">
<input type="submit" value="Save">
<input type="submit" name="cancel" value="cancel">
</form>

</div>
</body>
</html>
