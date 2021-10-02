<?php
session_start();
require_once "pdo.php";
require_once "view.php";

$sql="SELECT * FROM Profile WHERE profile_id=:pid AND user_id=:uid";
$stmt=$pdo->prepare($sql);
$stmt->execute(array(
	':pid'=>$_GET['profile_id'],
	':uid'=>$_SESSION['user_id']

));
$row=$stmt->fetch(PDO::FETCH_ASSOC);

if($row===false){
	$_SESSION['failure']="Could not load profile";
	header('Location: index.php');
	return;
}

$sql="SELECT * FROM Position WHERE profile_id=:pid";
$stmt=$pdo->prepare($sql);
$stmt->execute(array(':pid'=>$_GET['profile_id']));
$rows1=$stmt->fetchAll(PDO::FETCH_ASSOC);

$sql="SELECT Institution.name, Education.year FROM Institution,Education WHERE (Institution.institution_id=Education.institution_id AND profile_id=:pid)";
$stmt=$pdo->prepare($sql);
$stmt->execute(array(':pid'=>$_GET['profile_id']));
$rows2=$stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html>
<head>
<title>Hasin Raihan Shovon</title>
<?php require_once "head.php"; ?>
</head>

<body>
<div class="container">
<h1>Profile information</h1>

<?php
$fname= htmlentities($row['first_name']);
$lname= htmlentities($row['last_name']);
$email= htmlentities($row['email']);
$head= htmlentities($row['headline']);
$summary= htmlentities($row['summary']);
?>

<p><strong>First Name:</strong><?= ' '.$fname ?></p>

<p><strong>Last Name:</strong><?= ' '.$lname ?></p>

<p><strong>Email:</strong><?= ' '.$email ?></p>

<p><strong>Headline:</strong><?= ' '.$head ?></p>

<p><strong>Summary:</strong><?= ' '.$summary ?><p>

<?php
	if(!empty($rows2)){
		echo('<strong>Education:</strong>');
		echo('<ul>');
		foreach($rows2 as $row){
			echo('<li>');
			echo(htmlentities($row['year']).': '.htmlentities($row['name']));
			echo('</li>');
		}
		echo('</ul>');
	}

	if(!empty($rows1)){
		echo('<strong>Positions:</strong>');
		echo('<ul>');
		foreach($rows1 as $row){
			echo('<li>');
			echo(htmlentities($row['year']).': '.htmlentities($row['description']));
			echo('</li>');
		}
		echo('</ul>');
	}
?>


<p><a href="index.php">Done</a></p>

</div>
</html>
