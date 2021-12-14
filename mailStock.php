<?php

// include "database.php";
$databaseConnection = connectToDatabase();
$mails = getMail($databaseConnection);

$stockItemsID = array();
foreach($mails as $mail){
    // print $mail['StockItemID'];
    if(!in_array($mail['StockItemID'], $stockItemsID)){
        array_push($stockItemsID, $mail['StockItemID']);
    }


}

foreach($stockItemsID as $stockItemID){
    $stocks = getHoldings($stockItemID, $databaseConnection);
    
    foreach($stocks as $stock){
        if($stock['QuantityOnHand'] > 0){
            
            foreach($mails as $mail){
                //&& $mail == 0
                if($mail['StockItemID'] == $stock['StockItemID'] && $mail['active'] == 0 ){

                    $to_email = $mail['mailinstock'];
                    $subject = "Nerdygadgets product is weer beschikbaar";
                    $body = "Dag,\n
                    Het product waar u zich voor heeft opgegeven is weer beschikbaar <a href='nerdygadgets.nl/nerdygadgets/view.php?id=".$mail['StockItemID']."'>Klik hier</a> om het product te bekijken.";
                    $headers = "From: sender\'s email";
                    $headers.="MIME-Version: 1.0 \r\n";
                    $headers.="Content-type: text/html; charset=\"UTF-8\" \r\n";
                    
                     //versturen mail
                    mail($to_email, $subject, $body, $headers);
                    updateMail($mail['StockItemID'], $databaseConnection);
                }
            }
        }
    }
}
?>