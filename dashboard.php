<?php 
include __DIR__ . "/header.php";
if(!isset($_SESSION)){
    session_start();
}
if(!isset($_SESSION['login'])){
    die("<h1 style='text-align:center;'>FORBIDDEN PLEASE LOGIN</h1>");
}
$orders = getOrders($_SESSION['id'], $databaseConnection);

?>
<div class="container-dashboard">
    <h1> DASHBOARD </h1>
    <h2><?= $_SESSION['name'] ?></h2>
    <h3><?= $_SESSION['adress'] ?></h3>
    <h3><?= $_SESSION['postcode'] ?></h3>
    <div class="container-aankopen">
<?php
if(!empty($orders)){
foreach($orders as $order){
    print "<div class='order-container'>";
    $orderlines = getOrderlines($order['OrderID'], $databaseConnection);
    print "<div>ID: ".$order['OrderID']."</div>";
    print "<div>Datum: ".$order['OrderDate']."</div><hr style='border-color: white;width: 200px;'/>";
    foreach($orderlines as $orderline){
        print "<div>ID: ". $orderline['OrderLineID'] . "</div>";
        $stockitem = getStockItemName($orderline['StockItemID'], $databaseConnection);
        print "<div>Naam: ".$stockitem[0]['StockItemName']."</div>";
        print "<div>Aantal ". $orderline['PickedQuantity'] . "</div>";
        print "<div>Prijs: ". $orderline['UnitPrice'] . "</div>";
        print "<hr style='border-color: white;width: 200px;'/><br/>";

    }
    print "</div>";
}
}
?>
    </div>
</div>