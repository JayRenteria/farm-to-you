<?php
require_once("../classes/product.php");
require_once("../classes/store.php");
require_once("../classes/category.php");
require_once("../classes/location.php");
require_once("../classes/categoryproduct.php");
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");

// check if search was entered or not
if(@isset($_POST["inputSubmit"]) && (@isset($_POST["inputSearch"]) != "")) {
	echo "<p class=\"alert alert-danger\">Form values not complete. Verify the form and try again.</p>";
}

// connect to database and filter search
	mysqli_report(MYSQLI_REPORT_STRICT);
	$configArray = readConfig("/etc/apache2/capstone-mysql/farmtoyou.ini");
	$mysqli = new mysqli($configArray['hostname'], $configArray['username'], $configArray['password'], $configArray['database']);

	$searchq = $_POST["inputSearch"];
	$searchq = preg_replace("#[^0-9a-z]#i", "", $searchq);

// query the database. The amount of columns have to match as it currently is. Need more from product and location though
	$result = mysqli_query($mysqli, "SELECT productName, productPrice FROM product WHERE productName LIKE '%$searchq%' OR productDescription LIKE '%$searchq%'
											UNION
												SELECT storeName, imagePath  FROM store WHERE storeName LIKE '%$searchq%'
													UNION
														SELECT locationName, address1 FROM location WHERE locationName LIKE '%$searchq%'");

// check for errors in the search
if (!$result) {
	printf("Error: %s\n", mysqli_error($mysqli));
	exit();
}

// print results
print_r(mysqli_fetch_array($result));
while($row = mysqli_fetch_array($result)){
	echo "{$row['productName']}";
}

// try to print a table
var_dump($result);
//	print '<table border="1">';
//	while($row = $results->fetch_assoc()) {
//		print '<tr>';
//		print '<td>'.$row["id"].'</td>';
//		print '<td>'.$row["product_code"].'</td>';
//		print '<td>'.$row["product_name"].'</td>';
//		print '<td>'.$row["product_desc"].'</td>';
//		print '<td>'.$row["price"].'</td>';
//		print '</tr>';
//	}
//	print '</table>';
