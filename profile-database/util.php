<?php

function flashMessage(){
	if(isset($_SESSION['failure'])){
		echo('<p style="color: red;">'.$_SESSION['failure']."</p>\n");
		unset($_SESSION['failure']);
	}

	if(isset($_SESSION['success'])){
		echo('<p style="color:green;">'.$_SESSION['success']."</p>\n");
		unset($_SESSION['success']);
	}
}

function validateProfile(){
	$pattern='/@/';

	if(strlen($_POST['first_name'])<1 || strlen($_POST['last_name'])<1 || strlen($_POST['email'])<1 || strlen($_POST['headline'])<1 || strlen($_POST['summary'])<1){

		return "All fields are required";
	}
	else if(preg_match($pattern, $_POST['email'])==0){
        return " Email address must contain @";
    }
    else
    	return true;
}



function validatePosition(){
	for($i=1;$i<=9;$i++){
		if(! isset($_POST['year'.$i]))
			continue;
		if(! isset($_POST['desc'.$i]))
			continue;

		$year=$_POST['year'.$i];
		$desc=$_POST['desc'.$i];

		if(strlen($year)==0 || strlen($desc)==0){
			return "All fields are required";
		}
		if(!is_numeric($year)){
			return "Position year must be numeric";
		}
	}
	return true;
}

function validateEducation(){
	for($i=0;$i<=9;$i++){
		if(!isset($_POST['edu_year'.$i])){
			continue;
		}
		if(!isset($_POST['edu_school'.$i])){
			continue;
		}

		$year=$_POST['edu_year'.$i];
		$school=$_POST['edu_school'.$i];

		if(strlen($year)==0 || strlen($school)==0){
			return "All fields are required";
		}
		if(!is_numeric($year)){
			return "Education year must be numeric";
		}
	}

	return true;
}

function loadPosition($pdo,$profile_id){
	$sql="SELECT * FROM Position WHERE profile_id=:pos_id ORDER BY rank";
	$stmt=$pdo->prepare($sql);
	$stmt->execute(array(':pos_id'=>$profile_id));

	/*$positions=array();
	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
		$positions[]=$row;
	}*/
	$positions=$stmt->fetchALL(PDO::FETCH_ASSOC);

	return $positions;
}

function loadEducation($pdo,$profile_id){
	$sql="SELECT Institution.name, Education.year FROM Institution,Education WHERE (Institution.institution_id=Education.institution_id AND profile_id=:pid) ORDER BY rank";
	$stmt=$pdo->prepare($sql);
	$stmt->execute(array(':pid'=>$profile_id));

	$educations=$stmt->fetchALL(PDO::FETCH_ASSOC);

	return $educations;

}

function insertPositions($pdo,$profile_id){
	$rank=1;

	for($i=1;$i<=9;$i++){
		if(!isset($_POST['year'.$i]))
			continue;
		if(!isset($_POST['desc'.$i]))
			continue;

		$year=$_POST['year'.$i];
		$desc=$_POST['desc'.$i];

		$sql="INSERT INTO Position (profile_id,rank,year,description) VALUES (:pid,:rank,:year,:des)";
		$stmt=$pdo->prepare($sql);
		$stmt->execute(array(
			':pid'=>$profile_id,
			':rank'=>$rank,
			':year'=>$year,
			':des'=>$desc
		));

		$rank++;

	}
}

function insertEducations($pdo,$profile_id){
	$rank=1;

	for($i=1;$i<=9;$i++){
		if(!isset($_POST['edu_year'.$i]))
			continue;
		if(!isset($_POST['edu_school'.$i]))
			continue;

		$year=$_POST['edu_year'.$i];
		$school=$_POST['edu_school'.$i];

		//lookup the school name if it is already in the list
		$institution_id=false;

		$sql= "SELECT * FROM Institution WHERE name=:sname";
		$stmt=$pdo->prepare($sql);
		$stmt->execute(array(':sname'=>$school));
		$row= $stmt->fetch(PDO::FETCH_ASSOC);

		if(!empty($row)){
			$institution_id=$row['institution_id'];
		}
		else{
			$sql="INSERT INTO Institution (name) VALUES (:name)";
			$stmt=$pdo->prepare($sql);
			$stmt->execute(array(':name'=>$school));
			$institution_id=$pdo->lastInsertId();
		}

		$sql="INSERT INTO Education (profile_id,institution_id,rank,year) VALUES (:pid,:iid,:rank,:year)";
		$stmt=$pdo->prepare($sql);
		$stmt->execute(array(
			':pid'=> $profile_id,
			':iid'=> $institution_id,
			':rank'=>$rank,
			':year'=>$year
		));

		$rank++;
	}
}