<!-- dit is het bestand dat wordt geladen zodra je naar de website gaat -->
<?php
include __DIR__ . "/header.php";
if(!isset($_SESSION)){
    session_start();

}
else{
    $sessionIsSet = true;
}
?>

<div class="IndexStyle">
    <div class="col-11">
        <div class="TextPrice">
            <a href="view.php?id=93">
                <div class="TextMain">
                    "The Gu" red shirt XML tag t-shirt (Black) M
                </div>
                <ul id="ul-class-price">
                    <li class="HomePagePrice">€30.95</li>
                </ul>
        </div>
        </a>
        <div class="HomePageStockItemPicture"></div>
    </div>

    <div id="Recentbezocht">
        <h1>Recent Bezochte Producten</h1>
    </div>

    <?php
    if(isset($_SESSION['lastviewed'])){
    $criteria = (isset($_SESSION["lastviewed"])?implode(", ",$_SESSION["lastviewed"]):"-1");
    $nummer= 0;

    $lastViewed = $_SESSION['lastviewed'];

print' <div class="image">
        <div id="Recentplaatje">';
    if (count($_SESSION["lastviewed"]) > 0) {
    while($nummer<count($_SESSION["lastviewed"])) {
    $criterium = $_SESSION["lastviewed"][$nummer];
        $StockItemImage = getStockItemImage($criterium, $databaseConnection);
    $nummer+=1;

    if (!empty($StockItemImage)) {
        $image = $StockItemImage[0]['ImagePath'];
    }



   print '<a class="image" href="view.php?id='. $_SESSION['lastviewed'][0].'"> <img src="Public/StockItemIMG/'. $image .'" /></a>
 ';

        }}
    print '       </div>
    </div>';
        }
include __DIR__ . "/footer.php";
?>