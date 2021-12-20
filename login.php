<?php 
include __DIR__ . "/header.php";

if(!isset($_SESSION)){
    session_start();
}

if(isset($_POST['submit'])){
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = md5(trim($_POST["password"]));
        
    }
     $result = checkUser($username, $password, $databaseConnection);
     if(!empty($result)){
     foreach($result as $result){
        $_SESSION['login'] = true;
        $_SESSION['id'] = $result['CustomerID'];
        $_SESSION['name'] = $result['CustomerName'];
        $_SESSION['adress'] = $result['DeliveryAddressLine2'];
        $_SESSION['postcode'] = $result['PostalPostalCode'];
        header("Location: dashboard.php");
     }
     }
    if(!empty(trim($_POST["username"])) && !empty(trim($_POST["password"]))){
        
        // if(password_verify($password, checkUser($username, $password, $databaseConnection))){
        //     print "<h1>GOEDGEKEURD</h1>";
        // }
    }
  //  print password_hash($password, PASSWORD_DEFAULT);
    
}
?>
<div class="container-login">
    <form method="post">
        <input name="username" placeholder="Username" />
        <input name="password" type="password" placeholder="Password" />
        <input type="submit" name="submit" value="Login">
    </form>
</div>