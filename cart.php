<?php
include __DIR__ . "/header.php";
include "cartfuncties.php";
?>
    <div class="cart-container">
        <div class="cart">
            <h1>Inhoud Winkelwagen</h1>

            <?php
            $cart = getCart();
            $totalPrice = 0;
            foreach ($cart as $id => $quantity) {
                $StockItem = getStockItem($id, $databaseConnection);

                $name = $StockItem['StockItemName'];

                $StockItemImage = getStockItemImage($id, $databaseConnection);

                if (!empty($StockItemImage)) {
                    $image = $StockItemImage[0]['ImagePath'];
                }

                $sellPrice = round($StockItem['SellPrice'], 2);

                $total = $sellPrice * $quantity;
                ?>
                <div class="product-in-cart">
                    <div class="image">
                        <div id="ImageFrame"
                             style="background-image: url('Public/StockItemIMG/<?php print $image ?>'); background-size: 300px; background-repeat: no-repeat; background-position: center;"></div>
                    </div>
                    <?php if (!empty($_POST["submitCount$id"])) {
                        $stockItemID = $_POST["stockItemID"];
                        updateCart($stockItemID, $_POST["aantal"]);
                        $quantity = $_POST['aantal'];
                        $total = $sellPrice * $quantity;
                    }
                    ?>

                    <div class="description">

                        <div class="product name">Product naam: <a href="view.php?id=<?php print $id ?>"><?php print $name ?></a></div>
                        <div class="product price">Prijs: <?php print '€' . $sellPrice ?></div>
                        <div class="product quantity">Aantal: <?php print $quantity ?></div>
                        <div class="product total">Totaal: <?php print '€' . $total ?></div>
                        <div class="product quantity-form">Aantal aanpassen:
                            <form method="post" class="submited">
                                <input type="number" name="stockItemID" value="<?php print $id ?>" hidden>
                                <input type="number" name="aantal" value="<?php print $quantity ?>" min="1"/>
                                <input name="submitCount<?php print $id ?>" type="submit" value="Aanpassen"/>
                            </form>
                        </div>

                        <?php
                        if (isset($_POST["submitDelete$id"])) {
                            $stockItemID = $_POST["stockItemID"];
                            removeProductFromCart($stockItemID);
                            print("Product verwijderd uit <a href='cart.php'> winkelmandje!</a>");
                        }
                        ?>

                        <form method="post">
                            <input type="number" name="stockItemID" value="<?php print $id ?>" hidden>
                            <input name="submitDelete<?php print $id ?>" type="submit" value="Verwijderen uit winkelmand" class="button delete-from-cart"/>
                        </form>

                    </div>

                </div>
                <?php
                $totalPrice += $total;
            }
            if (!empty($totalPrice)) {
                print "<p>Totaalprijs Winkelwagen: €" . $totalPrice . "</p>";
            }
            ?>
        </div>
    </div>

<?php
include __DIR__ . "/footer.php";
?>