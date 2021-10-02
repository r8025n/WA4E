<?php
	session_start();
	require_once "pdo.php";
	$sql="SELECT name FROM Institution WHERE name LIKE :prefix";
	$stmt=$pdo->prepare($sql);
	$stmt->execute(array(':prefix' => $_REQUEST['term']."%"));

	$schools=array();
	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	  $schools[] = $row['name'];
	}

	header('Content-Type: application/json');
	echo(json_encode($schools, JSON_PRETTY_PRINT));
?>