<?php
/**
 * @author Florian Goussin <florian.goussin@gmail.com>
 */

// header
$currentDir = dirname(__FILE__);
require_once '../root-path.php';
require_once '../php/lib/header.php';

// model
require_once '/etc/apache2/capstone-mysql/encrypted-config.php';
require_once '../php/classes/orderproduct.php';
require_once '../php/classes/product.php';
require_once '../php/classes/order.php';
require_once '../php/classes/profile.php';
require_once '../php/classes/user.php';

try {
	mysqli_report(MYSQLI_REPORT_STRICT);

	// get the credentials information from the server
	$configFile = "/etc/apache2/capstone-mysql/farmtoyou.ini";
	$configArray = readConfig($configFile);

	// connection
	$mysqli = new mysqli($configArray["hostname"], $configArray["username"], $configArray["password"],
		$configArray["database"]);

	$products = Product::getAllProducts($mysqli);
	$orderProducts = OrderProduct::getAllOrderProducts($mysqli);
	$mysqli->close();
//var_dump($products);
} catch(Exception $exception) {
	echo "Exception: " . $exception->getMessage() . "<br/>";
	echo $exception->getFile() .":" . $exception->getLine();
}

?>

<div class="row-fluid">
	<div class="col-sm-12">
		<h2>Shopping cart</h2>

		<table class="table">
			<thead>
				<tr>
					<th></th>
					<th>product description</th>
					<th>price</th>
					<th>quantity</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach($orderProducts as $orderProduct) {
					echo '<tr>';
					echo '<td>' . 'photo' . '</td>';
					echo '<td>' . 'description' . '</td>';
					echo '<td>' . 'price' . '</td>';
					echo '<td>' . $orderProduct->getProductQuantity() . '</td>';
					echo '</tr>';
				}

				?>
			</tbody>
		</table>
	</div><!-- end col-sm-12 -->
</div><!-- end row-fluid -->

<?php require_once "../php/lib/footer.php"; ?>