<?php
include __DIR__ . "/header.php";
include "cartfuncties.php";
?>
    <div class="cart-container">
        <div class="cart">
            <h1>Inhoud Winkelwagen</h1>

            <?php
            $cart = getCart();
            foreach ($cart as $id => $product) {
                $StockItem = getStockItem($id, $databaseConnection);

                $price = sprintf("€ %.2f", $StockItem['SellPrice']);
                $name = $StockItem['StockItemName'];

                $StockItemImage = getStockItemImage($id, $databaseConnection);
                $image = $StockItemImage[0]['ImagePath'];
                ?>
                <div class="product-in-cart">
                    <div class="image">
                        <div id="ImageFrame"
                             style="background-image: url('Public/StockItemIMG/<?php print $image ?>'); background-size: 300px; background-repeat: no-repeat; background-position: center;"></div>
                    </div>
                    <div class="description">
                        <div class="name">Product naam: <a href="view.php?id=<?= $id ?>"><?= $name ?></a></div>
                        <div class="price">Prijs: <?= $price ?></div>
                        <div class="quantity">Aantal: <?= $product ?></div>
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