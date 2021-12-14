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

function insertOrderLines($stockItemID, $description, $packageTypeID, $quantity,
                          $unitPrice, $pickedQuantity, $lastEditedBy, $databaseConnection)
{

    $Query = "INSERT INTO orderlines (OrderID, StockItemID, Description, PackageTypeID, Quantity,
                         UnitPrice, TaxRate, PickedQuantity, PickingCompletedWhen, LastEditedBy)
              VALUES (73588, $stockItemID, $description, $packageTypeID, $quantity,
                        $unitPrice, 15.000, $pickedQuantity, $lastEditedBy)";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
}

//function insertOrders($databaseConnection) {}

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

function testDatabaseFunction($val1, $val2, $val3, $val4, $val5, $val6, $val7, $val8, $databaseConnection)
{
    $Query = "
    INSERT INTO `cities`(`CityID`, `CityName`, `StateProvinceID`, `Location`, `LatestRecordedPopulation`, `LastEditedBy`, `ValidFrom`, `ValidTo`) 
    VALUES ($val1, $val2, $val3, $val4, $val5, $val6, $val7, $val8);
    ";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
}