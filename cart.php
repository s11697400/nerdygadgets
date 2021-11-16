<?php
include __DIR__ . "/header.php";
include "cartfuncties.php";
?>
    <div class="cart-container">
        <div class="cart">
            <h1>Inhoud Winkelwagen</h1>

            <?php
            $cart = getCart();
            foreach ($cart as $id => $quantity) {
                $StockItem = getStockItem($id, $databaseConnection);

                $name = $StockItem['StockItemName'];

                $StockItemImage = getStockItemImage($id, $databaseConnection);
                $image = $StockItemImage[0]['ImagePath'];

                $sellPrice = round($StockItem['SellPrice'], 2);

                $total = $sellPrice * $quantity;
                ?>
                <div class="product-in-cart">
                    <div class="image">
                        <div id="ImageFrame"
                             style="background-image: url('Public/StockItemIMG/<?= $image ?>'); background-size: 300px; background-repeat: no-repeat; background-position: center;"></div>
                    </div>
                    <div class="description">
                        <div class="name">Product naam: <a href="view.php?id=<?= $id ?>"><?= $name ?></a></div>
                        <div class="price">Prijs: <?= '€' . $sellPrice ?></div>
                        <div class="quantity">Aantal: <?= $quantity ?></div>
                        <div class="total">Totaal: <?= '€' . $total ?></div>
                        <form method="post">
                            <input type="number" name="stockItemID" value="<?php print($StockItem['StockItemID']) ?>" hidden>
                            <input type="submit" name="submit" value="Verwijder uit winkelmandje">
                            <?php
                            if (isset($_POST["submit"])) {
                                $stockItemID = $_POST["stockItemID"];
                                removeProductFromCart($stockItemID);
                                print("Product verwijderd uit <a href='cart.php'> winkelmandje!</a>");
                            }
                            ?>
                        </form>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>


<?php
include __DIR__ . "/footer.php";
?>