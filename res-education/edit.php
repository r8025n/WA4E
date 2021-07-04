<?php
session_start();

require_once "pdo.php";
require_once "util.php";

if(!isset($_SESSION['name'])){
	die('ACCESS DENIED');
}

if(isset($_POST['cancel'])){
	header('Location: index.php');
	return;
}


if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])){
	
	$msg=validateProfile();
	if(is_string($msg)){
		$_SESSION['failure']=$msg;
		header('Location: edit.php?profile_id='.$_POST['profile_id']);
		return;
	}

	$msg=validatePosition();
	if(is_string($msg)){
		$_SESSION['failure']=$msg;
		header('Location: edit.php?profile_id='.$_POST['profile_id']);
		return;
	}
	
	$msg=validateEducation();
	if(is_string($msg)){
		$_SESSION['failure']=$msg;
		header('Location: edit.php?profile_id='.$_POST['profile_id']);
		return;
	}

	
	$sql="UPDATE Profile SET first_name=:fname, last_name=:lname, email=:em, headline=:he,summary=:su WHERE profile_id=:pid";
	$stmt=$pdo->prepare($sql);
	$stmt->execute(array(
		':fname'=>$_POST['first_name'],
		':lname'=>$_POST['last_name'],
		':em'=>$_POST['email'],
		':he'=>$_POST['headline'],
		':su'=>$_POST['summary'],
		':pid'=>$_POST['profile_id']
		
	));

	//delete all position entry of that profile id
	$sql="DELETE FROM Position WHERE profile_id=:pid";
	$stmt=$pdo->prepare($sql);
	$stmt->execute(array(':pid'=>$_POST['profile_id']));

	//delete all edeucational entry of that profile id
	$sql="DELETE FROM Education WHERE profile_id=:pid";
	$stmt=$pdo->prepare($sql);
	$stmt->execute(array(':pid'=>$_POST['profile_id']));

	//reinsert all positions and educations including new ones if exists
	insertEducations($pdo,$_POST['profile_id']);
	insertPositions($pdo,$_POST['profile_id']);

	$_SESSION['success']="Profile updated";
	header('Location: index.php');
	return;

}

if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['failure'] = "Missing Profile ID";
  header('Location: index.php');
  return;
}

$sql="SELECT * FROM Profile WHERE profile_id=:id AND user_id=:uid";
$stmt=$pdo->prepare($sql);
$stmt->execute(array(
	':id'=>$_GET['profile_id'],
	':uid'=>$_SESSION['user_id']
));
$row=$stmt->fetch(PDO::FETCH_ASSOC);


if ($row === false) {
    $_SESSION['failure'] = 'Could not load profile';
    header('Location: index.php') ;
    return;
}


$positions=loadPosition($pdo,$_GET['profile_id']);
$educations=loadEducation($pdo,$_GET['profile_id']);

?>


<!DOCTYPE html>
<html>
<head>

<title>Hasin Raihan Shovon</title>
<?php require_once "head.php"; ?>

</head>

<body>
<div class="container">
<h1>Editing Profile for <?= $_SESSION['name'] ?></h1>

<?php
flashMessage();

$fname=htmlentities($row['first_name']);
$lname=htmlentities($row['last_name']);
$email=htmlentities($row['email']);
$headline=htmlentities($row['headline']);
$summary=htmlentities($row['summary']);
$pid=htmlentities($row['profile_id']);

?>

<form method="post">
<p>First Name:
<input type="text" name="first_name" value="<?= $fname ?>" size="60"/></p>
<p>Last Name:
<input type="text" name="last_name" value="<?= $lname ?>" size="60"/></p>
<p>Email:
<input type="text" name="email" value="<?= $email ?>" size="30"/></p>
<p>Headline:<br/>
<input type="text" name="headline" value="<?= $headline ?>" size="80"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"><?= $summary ?></textarea></p>

<input type="hidden" name="profile_id" value="<?= $pid ?>">

<?php
	$edu=0;

	echo('Education: <input type="submit" id="addEdu" value="+">'."\n");
	echo('<div id="education_fields">'."\n");
	if(!empty($educations)){
		foreach($educations as $education){
			$edu++;
			echo('<div id="edu'.$edu.'">'."\n");
			echo('<p>'."\n");
			echo('Year: <input type="text" name="edu_year'.$edu.'" value="'.$education['year'].'">'."\n");
			echo('<input type="button" value="-" onclick="$(\'#edu'.$edu.'\').remove();return false;">'."\n");
			echo("</p>\n");
			echo('<p>'."\n");
			echo('School: <input type="text" size="80" name="edu_school'.$edu.'" value="'.$education['name'].'">'."\n");
			echo("</p>\n");
			echo("</div>\n");
		}
	}

	echo('</div>'."\n");


	$pos=0;
	echo('<p>Position: <input type="submit" id="addPos" value="+"></p>'."\n");
	echo('<div id="position_fields">'."\n");
	
	if(!empty($positions)){
		foreach($positions as $position){
			$pos++;
			echo('<div id="position'.$pos.'">'."\n");
			echo('<p>'."\n");
			echo('Year: <input type="text" name="year'.$pos.'" value="'.$position['year'].'">'."\n");
			echo('<input type="button" value="-" onclick="$(\'#position'.$pos.'\').remove();return false;">'."\n");
			echo('</p>'."\n");
			echo('<textarea name="desc'.$pos.'" rows="8" cols="80">'."\n");
			echo($position['description']);
			echo("</textarea>\n");
			echo("</div>\n");
		}
	}

	echo("</div>\n");

?>



<p>
<input type="submit" value="Save">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>

<script>

	countPos=<?= $pos ?>;
	countEdu=<?= $edu ?>;

	$(document).ready(function(){
		window.console && console.log('Document ready to be called');

		$('#addPos').click(function(event){

			event.preventDefault();

			if(countPos>=9){
				alert("Maximum nine positions exceeded");
				return;
			}

			countPos++;
			window.console && console.log("Adding new entry for position "+ countPos);
			
			$('#position_fields').append(
				'<div id="position'+countPos+'">\
				<p>\
				Year:<input type="text" name="year'+countPos+ '" value=""/> \
				<input type="button" value="-" onclick="$(\'#position'+countPos+'\').remove();return false;">\
				</p> \
				<textarea name="desc'+countPos+'" rows="8" cols="80"></textarea> \
				</div>');
		});

		$('#addEdu').click(function(event){
			event.preventDefault();
			if(countEdu>=9){
				alert("Maximum limit excedded");
				return;
			}

			countEdu++;
			window.console && console.log("adding entry for countEdu"+countEdu);

			$('#education_fields').append(
				'<div id="edu'+countEdu+'">\
	            <p>\
	            Year: <input type="text" name="edu_year'+countEdu+'" value=""> \
	            <input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;">\
	            </p>\
	            <p>\
	            School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="">\
	            </p>\
	            </div>');

			$('.school').autocomplete({
				source: "school.php"
			});

		});

	});
</script>

</div>
</body>
</html>
