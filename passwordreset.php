<?php
include __DIR__ . "/header.php";

if(!isset($_SESSION)){
    session_start();
}


//
if (isset($_POST["submit"])) {
    if (empty(trim($_POST["submit"]))) {
        print ("Please enter username.");
    } else {
        print("   Er is een e-mail verstuurd indien een geldig email adres is opgegeven. ");
    }
}
?>



<div class="container-login">
    <form method="post">
        <p>Wachtwoord opnieuw aanvragen.</p>
        <input name="submit" placeholder="E-mail adres">
        <input type="submit" name="submit" value="Verzend e-mail">
    </form>
    <a href="/nerdygadgets/index.php"> Homepage</a> | <a href="/nerdygadgets/login.php"> Login</a> | <a href="/nerdygadgets/register.php">Register</a>
</div>