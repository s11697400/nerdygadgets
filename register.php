<?php

include __DIR__ . "/header.php";

if(isset($_POST['submit'])) {
    $postcode = $_POST ['postcode'];

    $viercijferstweeletters = preg_match('/^[1-9][0-9]{3} ?[a-zA-Z]{2}$/', $postcode);

    if (!$viercijferstweeletters) {
        print 'Het is geen geldige postcode!';
    } else {
        $customer = insertCustomer($_POST['name'], $_POST['password'], $_POST['phone'], $_POST['adress'], $_POST['postcode'], $databaseConnection);
        if ($customer) {
            print "Account is toegevoegd";
        }
    }

}
?>
<div class="container-login">
    <form method="post">
        <input type="text" name="name" placeholder="Naam" required/>
        <input type="password" name="password" placeholder="Password" required/>
        <input type="text" name="phone" placeholder="Telefoonnummer" required/>
        <input type="text" name="adress" placeholder="Adress" required/>
        <input type="text" name="postcode" placeholder="Postcode" required/>
        <div><input type="checkbox" name="privacy" required/>
            <label for="privacy">Ik ga akkoord met het privacybeleid</label></div>
        <input type="submit" name="submit" value="Registreren"/>
    </form>
</div>
