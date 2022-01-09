<?php

include __DIR__ . "/header.php";

if(isset($_POST['submit'])) {
    $postcode = $_POST ['postcode'];
    $password = $_POST ['password'];

    // Validate password strength
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);
    $viercijferstweeletters = preg_match('/[1-9][0-9]{3} ?[ ][A-Za-z]{2}$/', $postcode);

    if (!$viercijferstweeletters) {
        print 'Het is geen geldige postcode! Type hem als volg 1234 AB';
    } else {

        
            if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
                echo 'Het wachtwoord moet minimaal 8 tekens lang zijn en moet minimaal één hoofdletter, één cijfer en één speciaal teken bevatten.';
            } else{
                if($_POST['password'] == $_POST['password2']){
                $customer = insertCustomer($_POST['name'], md5($_POST['password']), $_POST['phone'], $_POST['adress'], $_POST['postcode'], $databaseConnection);
            }
            else{
                print "<h1>  Wachtwoorden komen niet overeen</h1>";
            }
        }
            if (isset($customer)) {
            print "Account is toegevoegd";
        }
    }

}
?>
<div class="container-login">
    <form method="post">
        <input type="text" name="name" placeholder="Naam" required/>
        <input type="password" name="password" placeholder="Password" required/>
        <input type="password" name="password2" placeholder="Herhaal password" required/>
        <input type="text" name="phone" placeholder="Telefoonnummer" required/>
        <input type="text" name="adress" placeholder="Adress" required/>
        <input type="text" name="postcode" placeholder="Postcode" required/>
        <div><input type="checkbox" name="privacy" required/>
            <label for="privacy">Ik ga akkoord met het privacybeleid</label></div>
        <input type="submit" name="submit" value="Registreren"/>
    </form>
</div>
