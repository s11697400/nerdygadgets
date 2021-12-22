<!-- dit bestand bevat alle code voor de pagina die één product laat zien -->
<?php
include __DIR__ . "/header.php";
// include "cartfuncties.php";
include "viewfunctions.php";

$StockItem = getStockItem($_GET['id'], $databaseConnection);
$StockItemImage = getStockItemImage($_GET['id'], $databaseConnection);
$Review = getReviews($_GET['id'], $databaseConnection);
$StarsAvarage = getAvarageStars($_GET['id'], $databaseConnection);

$id = $_GET['id'];
$maxelements = 5;
if (isset($id) && $id <> "")

    if(!isset($_SESSION)) { 
        session_start(); 
      }
if (!isset($_SESSION["lastviewed"])) {
$_SESSION["lastviewed"] = array();
}

if (in_array($id, $_SESSION["lastviewed"])) {

$_SESSION["lastviewed"] = array_diff($_SESSION["lastviewed"], array($id));

}

if (count($_SESSION["lastviewed"]) >= $maxelements) {

$_SESSION["lastviewed"] = array_slice($_SESSION["lastviewed"], 1);

array_push($_SESSION["lastviewed"], $id);

} else {
array_push($_SESSION["lastviewed"], $id);

}
$criteria = (isset($_SESSION["lastviewed"]) ? implode(", ", $_SESSION["lastviewed"]) : "-1");
$nummer = 0;



if (!in_array($_GET['id'], $_SESSION["lastviewed"])) {

   array_push($_SESSION['lastviewed'], $_GET['id']);

}

?>
<?php
 ?>
<div id="CenteredContent">

    <?php
    if ($StockItem != null) {
        ?>
        <?php
        if (isset($StockItem['Video'])) {
            ?>
            <div id="VideoFrame">
                <?php print $StockItem['Video']; ?>
            </div>
        <?php }
        ?>


        <div id="ArticleHeader">
            <?php
            if (isset($StockItemImage)) {
                // één plaatje laten zien
                if (count($StockItemImage) == 1) {
                    ?>
                    <div id="ImageFrame"
                         style="background-image: url('Public/StockItemIMG/<?php print $StockItemImage[0]['ImagePath']; ?>'); background-size: 300px; background-repeat: no-repeat; background-position: center;"></div>
                    <?php
                } else if (count($StockItemImage) >= 2) { ?>
                    <!-- meerdere plaatjes laten zien -->
                    <div id="ImageFrame">
                        <div id="ImageCarousel" class="carousel slide" data-interval="false">
                            <!-- Indicators -->
                            <ul class="carousel-indicators">
                                <?php for ($i = 0; $i < count($StockItemImage); $i++) {
                                    ?>
                                    <li data-target="#ImageCarousel"
                                        data-slide-to="<?php print $i ?>" <?php print (($i == 0) ? 'class="active"' : ''); ?>></li>
                                    <?php
                                } ?>
                            </ul>

                            <!-- slideshow -->
                            <div class="carousel-inner">
                                <?php for ($i = 0; $i < count($StockItemImage); $i++) {
                                    ?>
                                    <div class="carousel-item <?php print ($i == 0) ? 'active' : ''; ?>">
                                        <img src="Public/StockItemIMG/<?php print $StockItemImage[$i]['ImagePath'] ?>">
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- knoppen 'vorige' en 'volgende' -->
                            <a class="carousel-control-prev" href="#ImageCarousel" data-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </a>
                            <a class="carousel-control-next" href="#ImageCarousel" data-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div id="ImageFrame"
                     style="background-image: url('Public/StockGroupIMG/<?php print $StockItem['BackupImagePath']; ?>'); background-size: cover;"></div>
                <?php
            }
            ?>


            <h1 class="StockItemID">Artikelnummer: <?php print $StockItem["StockItemID"]; ?></h1>
            <h2 class="StockItemNameViewSize StockItemName">
                <?php print $StockItem['StockItemName']; ?>
            </h2>
            <h3><?php printStars($StarsAvarage, true);?></h3>
            
            <div class="QuantityText">Vooraad: <?php print $StockItem['QuantityOnHand']; ?></div>
            <div id="StockItemHeaderLeft">
                <div class="CenterPriceLeft">
                    <div class="CenterPriceLeftChild">
                    <?php  if( $StockItem['QuantityOnHand'] > 0){ ?>
                        <p class="StockItemPriceText"><b><?php print sprintf("€ %.2f", $StockItem['SellPrice']); ?></b></p>
                        <h6> Inclusief BTW </h6>

                        <form method="post">
                            <input type="number" name="stockItemID" value="<?php print($StockItem['StockItemID']) ?>"
                                   hidden>
                            <input type="submit" name="submit" value="Voeg toe aan winkelmandje" class="add-to-cart">
                            <?php
                            if (isset($_POST["submit"])) {
                                $stockItemID = $_POST["stockItemID"];
                                addProductToCart($stockItemID);
                                print("Product toegevoegd aan <a href='cart.php'> winkelmandje!</a>");
                            }
                            ?>
                        </form>
                  <?php } else{
                      ?>
                      <p>Het product is tijdelijk niet beschikbaar. 
                          Mail mij als het product beschikbaar is:
                      </p>
                      <form method="post">
                            <input type="number" name="stockItemID" value="<?php print($StockItem['StockItemID']) ?>" hidden>
                            <input type="mail" name="mail" placeholder="Mail">
                            <input type="submit" name="submit" value="Verzenden" class="add-to-cart">
                            <?php
                            if (isset($_POST["submit"])) {
                                $stockItemID = $_POST["stockItemID"];
                                addMail($_POST['mail'], $stockItemID, $databaseConnection);
                            }
                            ?>
                        </form>
                  <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div id="StockItemDescription">
            <h3>Artikel beschrijving</h3>
            <p><?php print $StockItem['SearchDetails']; ?></p>
        </div>
        <div id="StockItemSpecifications">
            <h3>Artikel specificaties</h3>
            <?php
            $CustomFields = json_decode($StockItem['CustomFields'], true);
            if (is_array($CustomFields)) { ?>
                <table>
                <thead>
                <th>Naam</th>
                <th>Data</th>
                </thead>
                <?php
                foreach ($CustomFields as $SpecName => $SpecText) { ?>
                    <tr>
                        <td>
                            <?php print $SpecName; ?>
                        </td>
                        <td>
                            <?php
                            if (is_array($SpecText)) {
                                foreach ($SpecText as $SubText) {
                                    print $SubText . " ";
                                }
                            } else {
                                print $SpecText;
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
                </table><?php
            } else { ?>

                <p><?php print $StockItem['CustomFields']; ?>.</p>
                <?php
            }
            ?>
        </div>
        <div class="reviews">
            <h2 class="heading">Reviews</h2><br/>
            <div class="review-grid-container">
                <?php
                printStars($Review, false);
                ?>
            </div>


            <?php
            if (!empty($_POST['verzenden']) && isset($_SESSION['submit'])) {
                insertReviews($_GET['id'], $_POST['name'], $_POST['sterren'], $_POST['review'], $databaseConnection);
                echo "<script> alert('De review is geupload dankuwel!); </script>";
                unset($_SESSION['submit']);
                unset($_POST['verzenden']);
            } else {
                if (isset($_POST['verzenden'])) {
                    $_SESSION['submit'] = $_POST['verzenden'];
                }
            }

            ?>
            <div class="review-form">
                <h2 class="heading">Laat hier uw review achter</h2><br/>
                <form method="post" action="">
                    <input type="name" placeholder="Vul hier uw naam in" name="name"/>
                    <input type="num" placeholder="Aantal sterren" max=5 min=0 name="sterren"/>
                    <textarea rows="10" name="review" placeholder="Laat uw review van het product achter"></textarea>
                    <input type="submit" name="verzenden"/>
                </form>
            </div>
            <h1>Relevante producten:</h1>
            <div class="relevante-producten-container">
            <?php $producten = relevanteProducten($_GET['id'], $databaseConnection);
                    for ($i=0; $i <= 4; $i++){
                        $itemProduct = getStockItem($producten[$i]['StockItemID'], $databaseConnection);
                        $image = getStockItemImage($producten[$i]['StockItemID'], $databaseConnection);
                        print "<a href='/nerdygadgets/view.php?id=".$producten[$i]['StockItemID']."'><img src='Public/StockItemIMG/". $image[0]['ImagePath']."' /><h5><strong>".$itemProduct['StockItemName']."</strong></h5></a>";
                    }
            ?>
        </div>
        </div>
        <?php
    } else {
        ?><h2 id="ProductNotFound">Het opgevraagde product is niet gevonden.</h2><?php
    } ?>
</div>
<?php
@session_start();
$criteria = (isset($_SESSION["lastviewed"])?implode(", ",$_SESSION["lastviewed"]):"-1");
$nummer= 0;

While($nummer<count($_SESSION["lastviewed"])) {
    $criterium = $_SESSION["lastviewed"][$nummer]??"";
    $nummer += 1;
    $StockItemImage = getStockItemImage($criterium, $databaseConnection);
    if (!empty($StockItemImage)) {
        $image = $StockItemImage[0]['ImagePath'];
    }
}
    ?>
