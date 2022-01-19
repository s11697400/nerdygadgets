<!-- dit bestand bevat alle code die verbinding maakt met de database -->
<?php

function connectToDatabase()
{
    $Connection = null;

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Set MySQLi to throw exceptions
    try {
        $Connection = mysqli_connect("localhost", "root", "", "nerdygadgets");
        mysqli_set_charset($Connection, 'latin1');
        $DatabaseAvailable = true;
    } catch (mysqli_sql_exception $e) {
        $DatabaseAvailable = false;
    }
    if (!$DatabaseAvailable) {
        ?><h2>Website wordt op dit moment onderhouden.</h2><?php
        die();
    }

    return $Connection;
}

function getHeaderStockGroups($databaseConnection)
{
    $Query = "
                SELECT StockGroupID, StockGroupName, ImagePath
                FROM stockgroups 
                WHERE StockGroupID IN (
                                        SELECT StockGroupID 
                                        FROM stockitemstockgroups
                                        ) AND ImagePath IS NOT NULL
                ORDER BY StockGroupID ASC";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
    $HeaderStockGroups = mysqli_stmt_get_result($Statement);
    return $HeaderStockGroups;
}

function getStockGroups($databaseConnection)
{
    $Query = "
            SELECT StockGroupID, StockGroupName, ImagePath
            FROM stockgroups 
            WHERE StockGroupID IN (
                                    SELECT StockGroupID 
                                    FROM stockitemstockgroups
                                    ) AND ImagePath IS NOT NULL
            ORDER BY StockGroupID ASC";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
    $Result = mysqli_stmt_get_result($Statement);
    $StockGroups = mysqli_fetch_all($Result, MYSQLI_ASSOC);
    return $StockGroups;
}

function getStockItem($id, $databaseConnection)
{
    $Result = null;

    $Query = " 
           SELECT SI.StockItemID, 
            (RecommendedRetailPrice*(1+(TaxRate/100))) AS SellPrice, 
            StockItemName,
            QuantityOnHand,
            SearchDetails, 
            (CASE WHEN (RecommendedRetailPrice*(1+(TaxRate/100))) > 50 THEN 0 ELSE 6.95 END) AS SendCosts, MarketingComments, CustomFields, SI.Video,
            (SELECT ImagePath FROM stockgroups JOIN stockitemstockgroups USING(StockGroupID) WHERE StockItemID = SI.StockItemID LIMIT 1) as BackupImagePath   
            FROM stockitems SI 
            JOIN stockitemholdings SIH USING(stockitemid)
            JOIN stockitemstockgroups ON SI.StockItemID = stockitemstockgroups.StockItemID
            JOIN stockgroups USING(StockGroupID)
            WHERE SI.stockitemid = ?
            GROUP BY StockItemID";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $ReturnableResult = mysqli_stmt_get_result($Statement);
    if ($ReturnableResult && mysqli_num_rows($ReturnableResult) == 1) {
        $Result = mysqli_fetch_all($ReturnableResult, MYSQLI_ASSOC)[0];
    }

    return $Result;
}
function startTransaction($databaseConnection){
    // $databaseConnection->begin_transaction();
    $databaseConnection->autocommit(FALSE);
}
function getStockItemImage($id, $databaseConnection)
{

    $Query = "
                SELECT ImagePath
                FROM stockitemimages 
                WHERE StockItemID = ?";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $R = mysqli_stmt_get_result($Statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC);

    return $R;
}
function getStockItemName($id, $databaseConnection)
{

    $Query = "
                SELECT StockItemName
                FROM stockitems 
                WHERE StockItemID = ?";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $R = mysqli_stmt_get_result($Statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC);

    return $R;
}

function insertOrderLines($orderID, $stockItemID, $description, $packageTypeID, $quantity,
                          $unitPrice, $taxRate, $pickedQuantity, $pickingCompletedWhen,
                          $lastEditedBy, $lastEditedWhen, $databaseConnection)
{

    $Query = "INSERT INTO `orderlines`(`OrderID`, `StockItemID`, `Description`, `PackageTypeID`, `Quantity`,
                         `UnitPrice`, `TaxRate`, `PickedQuantity`, `PickingCompletedWhen`, 
                         `LastEditedBy`, `LastEditedWhen`)
              VALUES (?, ?, ?, ?, ?,
                        ?, ?, ?, ?, 
                      ?, ?)";

    $stmt = $databaseConnection->prepare($Query);
    $stmt->bind_param("iisiiddisss",$orderID, $stockItemID, $description, $packageTypeID, $quantity,
        $unitPrice, $taxRate, $pickedQuantity, $pickingCompletedWhen,
        $lastEditedBy, $lastEditedWhen);
try{
    $stmt->execute();

    return "<h1> Betaald! De bestelling wordt verwerkt!</h1>";
    
} catch (Exception $e){
    $databaseConnection->rollback();
}

}

//function insertOrders($databaseConnection) {}
function dbCommit($databaseConnection){
    $databaseConnection->commit();
}
function updateQuantity($id, $voorraad, $quantity, $databaseConnection)
{
    $Query = "
    UPDATE stockitemholdings
    SET QuantityOnHand=$voorraad - $quantity
    WHERE StockItemID=$id
    ";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
}

 


function getReviews($id, $databaseConnection)
{

    $Query = "
                SELECT Author, Tekst, stars
                FROM reviews 
                WHERE StockItemID = ?";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $R = mysqli_stmt_get_result($Statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC);

    return $R;
}
function insertReviews($id, $name, $sterren, $tekst, $databaseConnection)
{

    $Query = "INSERT INTO reviews (StockItemID, Author, stars, Tekst) VALUES (?, ?, ?, ?)";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "isis", $id, $name, $sterren, $tekst);
    mysqli_stmt_execute($Statement);
    $R = mysqli_stmt_get_result($Statement);
    // $R = mysqli_fetch_all($R, MYSQLI_ASSOC);

    return $R;
}
function getAvarageStars($id, $databaseConnection)
{

    $Query = "
                SELECT avg(stars)
                FROM reviews 
                WHERE StockItemID = ?";

                $Statement = mysqli_prepare($databaseConnection, $Query);
                mysqli_stmt_bind_param($Statement, "i", $id);
                mysqli_stmt_execute($Statement);

                $result = mysqli_stmt_get_result($Statement);
                $result = $result->fetch_array();
                return $result;
            }

function getUnitPrice($id, $databaseConnection)
{
    $Query = "
    SELECT UnitPrice 
    FROM `stockitems` 
    WHERE StockItemID=$id
    ";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);

    $result = mysqli_stmt_get_result($Statement);

    if (mysqli_num_rows($result) > 0) {
        while ($rowData = mysqli_fetch_array($result)) {
            return $rowData['UnitPrice'];
        }
    }

}

function insertOrder($id, $databaseConnection)
{

    $Query = "INSERT INTO `orders`(`CustomerID`, `SalespersonPersonID`, `ContactPersonID`, `LastEditedBy`)
              VALUES (?, 2, 2, 25)";

    $Statement = $databaseConnection->prepare($Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
  

    try {

    $Statement->execute();
    $last_id = mysqli_insert_id($databaseConnection);
   return $last_id;
   
} catch (Exception $e){
    $databaseConnection->rollback();
}

}


function checkUser($username, $password, $databaseConnection)
{

    $Query = "SELECT PostalPostalCode, DeliveryAddressLine2, CustomerID, CustomerName, Password FROM `customers` WHERE CustomerName = ? AND Password = ?";

    $stmt = $databaseConnection->prepare($Query);
    


    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "ss", $username, $password);
    mysqli_stmt_execute($Statement);

    $result = mysqli_stmt_get_result($Statement);

    if (mysqli_num_rows($result) > 0) {
        
        while ($rowData = mysqli_fetch_array($result)) {
            return $result;
        }
    }
    
}

function getOrders($id, $databaseConnection)
{

    $Query = "SELECT * FROM `orders` WHERE CustomerID = ? ORDER BY OrderDate";

    $stmt = $databaseConnection->prepare($Query);
    


    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);

    $result = mysqli_stmt_get_result($Statement);

    if (mysqli_num_rows($result) > 0) {
        
        while ($rowData = mysqli_fetch_array($result)) {
            return $result;
        }
    }
}
function getOrderlines($id, $databaseConnection)
{

    $Query = "SELECT * FROM `orderlines` WHERE OrderID = ?";

    $stmt = $databaseConnection->prepare($Query);
    


    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $R = mysqli_stmt_get_result($Statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC);

    return $R;
}

function addMail($mail,$id, $databaseConnection)
{

    $Query = "
                INSERT INTO mailinstock (mailinstock, StockItemID)
                VALUES(?, ?)";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "si", $mail, $id);
    mysqli_stmt_execute($Statement);
    // $R = mysqli_stmt_get_result($Statement);
    // $R = mysqli_fetch_all($R, MYSQLI_ASSOC);

    return True;
}

function getMail($databaseConnection)
{

    $Query = "
                SELECT * FROM mailinstock WHERE active = 0";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    // mysqli_stmt_bind_param($Statement, "si", $mail, $id);
    mysqli_stmt_execute($Statement);
    $R = mysqli_stmt_get_result($Statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC);
    return $R;
}

function relevanteProducten($id, $databaseConnection)
{

    $Query = "select * from stockitemstockgroups
where StockItemID <> ? AND StockGroupID IN (select StockGroupID
from stockitemstockgroups
where StockItemID = ?)";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "ii", $id, $id);
    mysqli_stmt_execute($Statement);
    $R = mysqli_stmt_get_result($Statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC);

    return $R;
}
function getHoldings($id, $databaseConnection)
{

    $Query = "
                SELECT * FROM stockitemholdings WHERE StockItemID = ?";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $R = mysqli_stmt_get_result($Statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC);

    return $R;
}
function updateMail($id, $databaseConnection)
{

    $Query = "
                UPDATE mailinstock 
                SET active = 1
                WHERE StockItemID = ?";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);


    return true;
}


 

function insertCustomer($name, $password, $phone, $adress, $postcode, $databaseConnection){
    $Query = "INSERT INTO Customers (CustomerName, Password, PhoneNumber, DeliveryAddressLine2, PostalPostalCode, CustomerCategoryID, PrimaryContactPersonID, DeliveryMethodID, DeliveryCityID, PostalCityID, LastEditedBy, BillToCustomerID)
    VALUES (?,?,?,?,?, 3, 3, 3, 38186, 38186, 20, 1)";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "sssss", $name, $password, $phone, $adress, $postcode);
    mysqli_stmt_execute($Statement);

    $result = mysqli_stmt_get_result($Statement);
    return $result;
}
function UserAdress($id, $databaseConnection){
    $Query = "SELECT PostalPostalCode, DeliveryAddressLine2 FROM Customers
    WHERE CustomerID = ?";
        $Statement = mysqli_prepare($databaseConnection, $Query);
        mysqli_stmt_bind_param($Statement, "i", $id);
        mysqli_stmt_execute($Statement);
    
        $result = mysqli_stmt_get_result($Statement);
    
        if (mysqli_num_rows($result) > 0) {
            while ($rowData = mysqli_fetch_array($result)) {
                return $result;
            }
        }
}


function getTemprature($databaseConnection)
{

    $Query = "
                SELECT Temperature
                FROM coldroomtemperatures 
                WHERE ColdRoomSensorNumber = 3";

                $Statement = mysqli_prepare($databaseConnection, $Query);
                
                mysqli_stmt_execute($Statement);

                $result = mysqli_stmt_get_result($Statement);
                $result = $result->fetch_array();
                return $result;
            }