<?php
session_start();
require_once "pdo.php";


if(isset($_POST['delete']) && isset($_POST['profile_id'])){

	$sql= "DELETE FROM Profile WHERE profile_id=:pid";
	$stmt=$pdo->prepare($sql);
	$stmt->execute(array(":pid"=>$_POST['profile_id']));

	$_SESSION['success']="Profile Deleted";
	header('Location: index.php');
	return;
}



if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['failure'] = "Missing autos_id";
  header('Location: index.php');
  return;
}


$sql="SELECT first_name,last_name FROM Profile WHERE profile_id=:id";
$stmt=$pdo->prepare($sql);
$stmt->execute(array(':id'=> $_GET['profile_id']));
$row=$stmt->fetch(PDO::FETCH_ASSOC);

if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
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
<h1>Deleting Profile</h1>
<p>Confirm Deleting Profile</p>

<?php

$fname= htmlentities($row['first_name']);
$lname= htmlentities($row['last_name']);
$pid= htmlentities($_GET['profile_id']);

?>

<p><strong>First Name:</strong><?= ' '.$fname ?></p>

<p><strong>Last Name:</strong><?= ' '.$lname ?></p>

<form method="post">
<input type="hidden" name="profile_id" value="<?= $pid ?>">
<input type="submit" name="delete" value="Delete">
<a href="index.php">Cancel</a>
</form>

</div>
</body>
</html>