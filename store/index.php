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
require_once("../php/classes/store.php");
require_once("../php/classes/profile.php");
require_once("../php/classes/location.php");
require_once("../php/classes/storelocation.php");
require_once("../php/classes/orderproduct.php");
require_once("../php/classes/categoryproduct.php");
require_once("../php/classes/category.php");

// path for the config file
$configFile = "/etc/apache2/capstone-mysql/farmtoyou.ini";

mysqli_report(MYSQLI_REPORT_STRICT);

try {

	// get the credentials information from the server and connect to the database
	$configArray = readConfig($configFile);

	$mysqli = new mysqli($configArray["hostname"], $configArray["username"], $configArray["password"],
	$configArray["database"]);

	// get the product id from the current url
	if(!@isset($_GET['store'])) {
		header('Location: ../php/lib/404.php');
	} else {
		$storeId = $_GET['store'];
	}

	$storeId = filter_var($storeId, FILTER_SANITIZE_NUMBER_INT);
	$store = Store::getStoreByStoreId($mysqli, $storeId);

	// get all the products of the current product store
	$products = Product::getAllProductsByStoreId($mysqli, $store->getStoreId());

	$categoryProducts = [];
	if($products !== null) {
		foreach($products as $product) {
			$resultCategoryProducts = CategoryProduct::getCategoryProductByProductId($mysqli, $product->getProductId());

			if($resultCategoryProducts !== null) {
				$categoryProducts = array_merge($categoryProducts, $resultCategoryProducts);
			}
		}
	}

	// get all the categories
	$categories = [];
	foreach($categoryProducts as $categoryProduct) {
		$categories[] = Category::getCategoryByCategoryId($mysqli, $categoryProduct->getCategoryId());
	}

	// delete duplicates
	$categories = array_unique($categories, SORT_REGULAR);

	// get all the locations from the current store
	$storeLocations = StoreLocation::getAllStoreLocationsByStoreId($mysqli, $store->getStoreId());

	// get the store owner
	$storeOwner = Profile::getProfileByProfileId($mysqli, $store->getProfileId());

	$mysqli->close();

} catch(Exception $exception) {
	echo 'Exception: '. $exception->getMessage() .'<br/>';
	echo $exception->getFile(). ':' .$exception->getLine();
}

$storeBaseUrl  = CONTENT_ROOT_URL . 'images/store/';
$storeBasePath = CONTENT_ROOT_PATH . 'images/store/';
$storeImageSrc  = basename($store->getImagePath());
$bannerImagePlaceHolderSrc  = '../images/banner-placeholder.png';

?>

<div class="container-fluid transparent-form client-view" id="store-view">
	<div class="row">
		<div class="col-sm-3" id="store-menu">

			<div class="list-group hidden-xs">
				<span class="list-group-item">Store Sections</span>
				<a href="#" class="list-group-item active static">Home</a>
				<?php foreach($categories as $category) { ?>
					<a href="#" class="list-group-item"><?php echo $category->getCategoryName(); ?></a>
				<?php } ?>

			</div>

			<div class="dropdown visible-xs">
				<a href="#" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Categories<span class="caret"></span></a>
				<ul class="dropdown-menu search-dropdown">
					<li><a href="#" class="list-group-item active static">Home</a></li>
					<?php foreach($categories as $category) { ?>
						<li><a href="#" class="list-group-item"><?php echo $category->getCategoryName(); ?></a></li>
					<?php } ?>
				</ul>
			</div>

			<div id="store-owner" class="section hidden-xs">
				<h3>Store Owner</h3>
				<a href="" class="avatar"></a>
				<a href=""><?php echo $storeOwner->getFirstName() . ' ' . $storeOwner->getLastName(); ?></a>
			</div>
		</div>
		<div class="col-sm-9" id="store-content">
			<?php
				if($storeLocations === null) {
					throw new Exception('products are null and store location are null');
					exit();
				}
			?>
			<div class="row">
				<div class="col-sm-12">
					<div id="store-id-card">
						<?php $storeLink = SITE_ROOT_URL . 'store/index.php?store='. $store->getStoreId(); ?>

						<div class="store-banner hidden-xs">
							<a href="<?php echo $storeLink; ?>"
								title="<?php echo $store->getStoreDescription(); ?>">
								<img src="<?php echo is_file($storeBasePath.$storeImageSrc)
									? $storeBaseUrl.$storeImageSrc
									: $bannerImagePlaceHolderSrc; ?>"
									  alt="<?php echo $store->getStoreName(); ?>"
									  class="img-responsive"/>
							</a>
						</div><!-- end store-banner -->

						<div class="store-info mt30">
							<div class="row">
								<div class="col-sm-8">
									<h1><?php echo $store->getStoreName(); ?></h1>
								</div>
								<div class="col-sm-4 t-right hidden-xs">
									<?php include('../php/lib/share-view.php'); ?>
								</div>
							</div>
						</div>

						<div class="announcement mt20 t-left">
							<a href="<?php echo $storeLink; ?> id="store-announcement>
								<!-- show only the beginning of the description -->
								<?php echo $store->getStoreDescription(); ?>
							</a>
						</div>
					</div><!-- end store-id-card -->
				</div>
			</div>


			<div class="row mt30" id="products">
				<div class="col-sm-12">
					<ul class="products product-listing t-left">
						<?php if($products !== null) {
									foreach($products as $index => $product) { ?>

							<?php

							$productImageBaseUrl  = CONTENT_ROOT_URL . 'images/product/';
							$productImageBasePath = CONTENT_ROOT_PATH . 'images/product/';
							$productImageSrc      = basename($product->getImagePath());
							$imagePlaceHolderSrc  = '../images/placeholder.png';

							$productUrl = SITE_ROOT_URL.'product/index.php?product='.$product->getProductId();

							$productDescription      = $product->getProductDescription();
							$descriptionLimit        = 28;
							$productCardTitleEllipse = substr($product->getProductName() . ' | ' . $product->getProductDescription(), 0, $descriptionLimit) . '...';
							$productCardTitle        = $product->getProductName() . ' | ' . $productDescription;

							$productPrice = number_format((float)$product->getProductPrice(), 2, '.', '');

							?>

							<li class="thumbnail" id="<?php echo 'product-' . $product->getProductId(); ?>">
								<div class="product-listing-card">
									<a class="product-listing-thumbnail"
										href="<?php echo $productUrl; ?>"
										title="<?php echo $productDescription; ?>"
										>
										<img src="<?php echo ($productImageSrc !== '' && file_exists($productImageBasePath.$productImageSrc)
											? $productImageBaseUrl.$productImageSrc
											: $imagePlaceHolderSrc) ?>"
											  alt="<?php echo $productCardTitle; ?>"
											  class="product-listing-image"/>
									</a>
									<div class="product-listing-detail">
										<a href="<?php echo $productUrl ?>" class="product-listing-card-title"
											title="<?php echo $productCardTitle; ?>">
											<?php

											// content
											echo strlen($productCardTitle) > $descriptionLimit
												? $productCardTitleEllipse
												: $productCardTitle;

											?>
										</a>
										<div class="mt5">
											<span class="product-store">
												<a href="../store/index.php?store=<?php echo $store->getStoreId(); ?>"><?php echo $store->getStoreName(); ?></a>
											</span>
											<span class="product-price">
												$<?php echo ($product->getProductPriceType() === 'u') ? $productPrice : $productPrice . '/lb'; ?>
											</span>
										</div>
									</div>
								</div><!-- end product-listing card -->
							</li>

						<?php } }?><!-- end of the for each store product loop -->
					</ul>
				</div>
			</div><!-- end products -->
		</div><!-- end store-content -->

		<div id="store-owner" class="section visible-xs">
			<h3>Store Owner</h3>
			<a href="" class="avatar"></a>
			<a href=""><?php echo $storeOwner->getFirstName() . ' ' . $storeOwner->getLastName(); ?></a>
		</div>

		<div class="col-sm-4 t-center visible-xs mt40">
			<?php include('../php/lib/share-view.php'); ?>
		</div>

	</div><!-- end row -->
</div><!-- end container-fluid -->

<script src="../js/store.js"></script>

<?php require_once '../php/lib/footer.php' ?>