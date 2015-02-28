<?php

/**
* @author Florian Goussin <florian.goussin@gmail.com>
*/

// header
$currentDir = dirname(__FILE__);
require_once('../root-path.php');
require_once('../php/lib/header.php');

// credentials
require_once('/etc/apache2/capstone-mysql/encrypted-config.php');

// paths file
require_once('../paths.php');

// model
require_once("../php/classes/product.php");
require_once("../php/classes/user.php");
require_once("../php/classes/profile.php");
require_once("../php/classes/store.php");
require_once("../php/classes/location.php");
require_once("../php/classes/storelocation.php");
require_once("../php/classes/orderproduct.php");

// path for the config file
$configFile = "/etc/apache2/capstone-mysql/farmtoyou.ini";

mysqli_report(MYSQLI_REPORT_STRICT);

try {

	// get the credentials information from the server and connect to the database
	$configArray = readConfig($configFile);

	$mysqli = new mysqli($configArray["hostname"], $configArray["username"], $configArray["password"],
	$configArray["database"]);

	$user     = User::getUserByUserId($mysqli, 1);
	$profile  = Profile::getProfileByProfileId($mysqli, 1);

	// get the product id from the current url
	if(!@isset($_GET['store'])) {
		header('Location: ../php/lib/404.php');
	} else {
		$storeId = $_GET['store'];
	}

	$storeId = filter_var($storeId, FILTER_SANITIZE_NUMBER_INT);
	$store = Store::getStoreByStoreId($mysqli, $storeId);

	// get all the products of the current product store
	$storeProducts = Product::getAllProductsByStoreId($mysqli, $store->getStoreId());

	// get all the locations from the current store
	$storeLocations = StoreLocation::getAllStoreLocationsByStoreId($mysqli, $store->getStoreId());

	$locations = [];
	if($storeLocations !== null) {
		foreach($storeLocations as $storeLocation) {
			$location = Location::getLocationByLocationId($mysqli, $storeLocation->getLocationId());
			$locations[] = $location;
		}
	}

} catch(Exception $exception) {
	echo 'Exception: '. $exception->getMessage() .'<br/>';
	echo $exception->getFile(). ':' .$exception->getLine();
}

$storeBaseUrl  = CONTENT_ROOT_URL . 'images/store/';
$storeBasePath = CONTENT_ROOT_PATH . 'images/store/';
$storeImageSrc  = basename($store->getImagePath());

?>

<div class="container-fluid">
	<div class="row">
<!--		<div class="col-sm-3">-->
<!--			<div class="list-group">-->
<!--				<span class="list-group-item">Store Sections</span>-->
<!--				<a href="#" class="list-group-item active">Store home</a>-->
<!--				<a href="#" class="list-group-item">Fruits</a>-->
<!--				<a href="#" class="list-group-item">Veggies</a>-->
<!--				<a href="#" class="list-group-item">Nuts</a>-->
<!--				<a href="#" class="list-group-item">Flowers</a>-->
<!--			</div>-->
<!--		</div>-->
		<div class="col-sm-12">
			<div class="row">
				<div class="col-sm-12">
					<?php

					$storeLink = SITE_ROOT_URL . 'store/index.php?store='. $store->getStoreId();

					if(file_exists($storeBasePath . $storeImageSrc)) {
						echo '<a href="' . $storeLink . '"><img src="' . $storeBaseUrl . $storeImageSrc .'" alt="'.
							$store->getStoreName() .'" class="img-responsive"/></a>';
					} else {
						echo '<a href="' . $storeLink . '""><img src="' . $imagePlaceholderSrc . '" alt="'. $store->getStoreName() .
							'" class="img-responsive"/></a>';
					}

					?>
				</div>
			</div>
			<div class="row">

			</div>
		</div>
	</div>
</div>