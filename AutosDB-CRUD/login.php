<?php
session_start();

require_once "pdo.php";



if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to index.php
    header('Location: index.php');
    return;
}

$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Pw is php123

//$_SESSION['failure'] = false;  // If we have no POST data
$pattern='/@/';


// Check to see if we have some POST data, if we do process it
if ( isset($_POST['email']) && isset($_POST['pass']) ) {


    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION['failure'] = "User name and password are required";
    } 
    else if(preg_match($pattern, $_POST['email'])==0){
        $_SESSION['failure']= " Email must have an at-sign (@)";
    }
    else {
        $check = hash('md5', $salt.$_POST['pass']);
        if ( $check == $stored_hash ) {
            $_SESSION['name']=$_POST['email'];
            // Redirect the browser to view.php
            error_log("Login success ".$_POST['email']);
            header("Location: index.php");
            return;
        } 
        else {
            error_log("Login fail ".$_POST['email']." $check");
            $_SESSION['failure'] = "Incorrect password";
        }
    }
    header("Location: login.php");
    return;
}

// Fall through into the View

?>


<!DOCTYPE html>
<html>
<head>
<title>Hasin Raihan Shovon</title>
</head>

<body>
<h1>Welcome to Automobile Database</h1>
<?php
    if(isset($_SESSION['failure'])){
        echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
        unset($_SESSION['failure']);
    }
?>

<div class="container">
<h2>Please log in</h2>
<form method="POST">
<label for="nam">User Email ID</label>
<input type="text" name="email" id="nam"><br/>
<label for="id_1723">Password</label>
<input type="text" name="pass" id="id_1723"><br/>
<input type="submit" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
</div>
</body>
