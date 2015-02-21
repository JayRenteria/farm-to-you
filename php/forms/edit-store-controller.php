<?php

$currentDir = dirname(__FILE__);
//require_once("../../dummy-session-single.php");
require_once ("../../root-path.php");

require_once("/etc/apache2/capstone-mysql/encrypted-config.php");
require_once("../classes/store.php");
require_once("../classes/location.php");
require_once("../classes/storelocation.php");
require_once("../classes/profile.php");
require_once("../classes/user.php");
require_once("../lib/utils.php");


try {

	mysqli_report(MYSQLI_REPORT_STRICT);
	$configArray = readConfig("/etc/apache2/capstone-mysql/farmtoyou.ini");
	$mysqli = new mysqli($configArray['hostname'], $configArray['username'], $configArray['password'], $configArray['database']);

	$store = Store::getStoreByStoreId($mysqli, 1);
//	var_dump($store);
	$storeName = $store->getStoreName();
	$storeDescription = $store->getStoreDescription();
	$storeImagePath = $store->getImagePath();
	echo 'storeImagePath';

	var_dump($storeImagePath);

	$storeId = $store->getStoreId();

	if($_POST['editStoreName'] !== '') {
		$storeName = $_POST['editStoreName'];
//		$_SESSION['store'] ['name'] = $_POST['editStoreName'];
		$store->setStoreName($storeName);
	}

	if ($_POST['editStoreDescription'] !== ''){
		$storeDescription = $_POST['editStoreDescription'];
//		$_SESSION['store'] ['description'] = $_POST['editStoreDescription'];
		$store->setStoreDescription($storeDescription);
	} else {
		$storeDescription = '';
		$store->setStoreDescription($storeDescription);
	}

//	var_dump($store);
//	var_dump(@isset($_FILES['editInputImage']));
//	var_dump($_FILES);

//	if($_FILES['editInputImage']['error'] !== 0) {
//		$_FILES['editInputImage'] = null;
//	}

	if(@isset($_FILES['editInputImage']) === true) {

		$imageExtension = checkInputImage($_FILES['editInputImage']);
		$imageFileName = 'store-' . $storeId . '.' . $imageExtension;
//		$_SESSION['store'] ['image'] = $_FILES['editInputImage'];
		echo 'imageFileName';
		var_dump($imageFileName);
		$store->setImagePath($imageFileName);
	}
	$store->update($mysqli);

	echo "<p class=\"alert alert-success\">" . $store->getStoreName() . " updated!</p>";

} catch(Exception $exception) {
	echo "<p class=\"alert alert-danger\">Exception: " . $exception->getMessage() . "</p>";?>
	<!--<form class="form-inline" id="back" method="post" action="../../store/index.php">-->
	<!--	<button type="submit">Back</button>-->
	<!--</form>-->
<?php }
