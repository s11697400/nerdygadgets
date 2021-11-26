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
                    <?php if (!empty($_POST["submitCount"])) {
                        $stockItemID = $_POST["stockItemID"];
                        updateCart($stockItemID, $_POST["aantal"]);
                        $quantity = $_POST['aantal'];
                        $total = $sellPrice * $quantity;
                    }
                    ?>

                    <div class="description">
                        <div class="name">Product naam: <a
                                    href="view.php?id=<?php print $id ?>"><?php print $name ?></a></div>
                        <div class="price">Prijs: <?php print '€' . $sellPrice ?></div>
                        <div class="quantity">Aantal:
                            <form method="post" class="submited">
                                <input type="number" name="stockItemID"
                                       value="<?php print($StockItem['StockItemID']) ?>" hidden><input type="number"
                                                                                                       name="aantal"
                                                                                                       value="<?php print $quantity ?>"
                                                                                                       min="1"/> <input
                                        name="submitCount" type="submit" value="Aanpassen"/>

                            </form>
                        </div>
                        <div class="total">Totaal: <?php print '€' . $total ?></div>
                        <?php
                        if (isset($_POST["submit"])) {
                            $stockItemID = $_POST["stockItemID"];
                            removeProductFromCart($stockItemID);

                            print("Product verwijderd uit <a href='cart.php'> winkelmandje!</a>");
                        }
                        ?>
                        <form method="post">
                            <input type="number" name="stockItemID" value="<?php print($StockItem['StockItemID']) ?>"
                                   hidden>
                            <input type="submit" name="submit" value="Verwijder uit winkelmandje">
                        </form>
                    </div>
                </div>
                <?php
            }
            if (!empty($total)) {
                $totalPrice += $total;
                print "<p>Totaalprijs Winkelmandje: €" . $totalPrice . "</p>";
            }
            ?>
        </div>
    </div>


<?php
include __DIR__ . "/footer.php";
?>