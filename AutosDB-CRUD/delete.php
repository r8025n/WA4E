<?php
session_start();
require_once "pdo.php";


if(isset($_POST['delete']) && isset($_POST['autos_id'])){

	$sql= "DELETE FROM autos WHERE autos_id=:autos_id";
	$stmt=$pdo->prepare($sql);
	$stmt->execute(array(":autos_id"=>$_POST['autos_id']));

	$_SESSION['success']="Record Deleted";
	header('Location: index.php');
	return;
}



if ( ! isset($_GET['autos_id']) ) {
  $_SESSION['failure'] = "Missing autos_id";
  header('Location: index.php');
  return;
}


$sql="SELECT autos_id,make FROM autos WHERE autos_id=:id";
$stmt=$pdo->prepare($sql);
$stmt->execute(array(':id'=> $_GET['autos_id']));
$row=$stmt->fetch(PDO::FETCH_ASSOC);

if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for user_id';
    header( 'Location: index.php' ) ;
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
	<h1>Deleting Entry</h1>
	<p>Confirm: Deleting <?= htmlentities($row['make'])?></p>

	<form method="post">
		<input type="hidden" name="autos_id" value="<?= $row['autos_id'] ?>">
		<input type="submit" name="delete" value="Delete">
		<a href="index.php">Cancel</a>
	</form>
</div>
</body>
</html>