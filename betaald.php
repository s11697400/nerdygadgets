<?php 
include __DIR__ . "/header.php";
if(!isset($_SESSION)){
    session_start();
}
if(!isset($_SESSION['betaald'])){
    die("<h1 style='text-align:center;'>FORBIDDEN</h1>");
}

?>
<h1 style="text-align: center;">Uw bestelling word verwerkt. <br/>
Bedrag: <?= $_SESSION['totaalPrijs'] ?></h1>
