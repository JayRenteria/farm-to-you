<?php
session_start();

$currentDir = dirname(__FILE__);
require_once("../../dummy-session.php");
require_once ("../../root-path.php");

require_once("../classes/product.php");
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");

// MAY HAVE TO CHANGE THIS TO NOT BE REQUIRED SINCE THIS IS JUST AN UPDATE. WILL TEST THIS ALONG WITH JS
// verify the form values have been submitted
if(@isset($_POST["inputProductName"]) === false || @isset($_POST["inputProductPrice"]) === false
	|| @isset($_POST["inputProductDescription"]) === false || @isset($_POST["inputProductWeight"]) === false || @isset($_POST["inputStockLimit"]) === false)  {
	echo "<p class=\"alert alert-danger\">Form values not complete. Verify the form and try again.</p>";
}

try {
	//
	mysqli_report(MYSQLI_REPORT_STRICT);
	$configArray = readConfig("/etc/apache2/capstone-mysql/farmtoyou.ini");
	$mysqli = new mysqli($configArray['hostname'], $configArray['username'], $configArray['password'], $configArray['database']);
	// 1 and 59 are place holders for product id and profile id that already exists
	if(@isset($_POST["inputProductImage"])) {
		$product = new Product(808, 886, $_POST["inputProductImage"], $_POST["inputProductName"], $_POST["inputProductPrice"], $_POST["inputProductDescription"], $_POST["inputProductPriceType"], $_POST["inputProductWeight"], $_POST["inputStockLimit"]);
	} else {
		$product = new Product(808, 886, null, $_POST["inputProductName"], $_POST["inputProductPrice"], $_POST["inputProductDescription"], $_POST["inputProductPriceType"], $_POST["inputProductWeight"], $_POST["inputStockLimit"]);
	}

	$product->update($mysqli);

	echo "<p class=\"alert alert-success\">Product (id = " . $product->getProductId() . ") posted!</p>";
} catch(Exception $exception) {
	echo "<p class=\"alert alert-danger\">Exception: " . $exception->getMessage() . "</p>";
}