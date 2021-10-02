<?php
session_start();

require_once "pdo.php";



if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to index.php
    header('Location: index.php');
    return;
}

$salt = 'XyZzy12*_';
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

        $stmt=$pdo->prepare("SELECT user_id,name FROM users WHERE email=:em AND password=:pw");
        $stmt->execute(array(':em'=>$_POST['email'],':pw'=>$check)); 
        $row=$stmt->fetch(PDO::FETCH_ASSOC);


        if($row!==false){
            $_SESSION['name']=$row['name'];
            $_SESSION['user_id']=$row['user_id'];
            //redirect the browser to index.php
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

<?php require_once "head.php"; ?>
</head>

<body>
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
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" name="cancel" value="Cancel">

<script>
    
function doValidate(){
    try{
        mail=document.getElementById('nam').value;
        pass=document.getElementById('id_1723').value;

        if(mail==null || pass== null || mail=="" || pass==""){
            alert("Both fields must be filled");
            return false;
        }

        if(mail.indexOf('@')==-1){
            alert("Invalid email address");
            return false;
        }

        return true;
    }catch(e){
        return false;
    }
    return false;

}
</script>

</form>
</div>
</body>
