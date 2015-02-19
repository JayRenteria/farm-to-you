<?php
session_start();
$currentDir = dirname(__FILE__);

require_once("../dummy-session-single.php");
require_once("../root-path.php");
require_once("../php/lib/header.php");

// classes
require_once("../php/classes/store.php");

?>

<script src="../js/store.js"></script>

<div class="row-fluid">
	<div class="col-sm-12">
		<h2>Add Store</h2>
			<form class="form-inline" id="storeController" method="post" action="../php/forms/store-controller.php" enctype="multipart/form-data">
				<div class="form-group">
					<label for="storeName">Store Name</label>
					<input type="text" id="storeName" name="storeName" value="Pass Farms">
				</div>
					<br>
				<div class="form-group">
					<label for="storeDescription">Store Description</label>
					<input type="text" id="storeDescription" name="storeDescription">
				</div>
				<br>

				<div class="form-group">
					<label for="inputImage">Store Image</label>
					<input type="file" id="inputImage" name="inputImage">
				</div>
				<br>
				<div class="form-group">
					<label for="locationName">Location Name</label>
					<input type="text" id="locationName" name="locationName" value="Home">
				</div>
				<br>
				<div class="form-group">
					<label for="address1">Address</label>
					<input type="text" id="address1" name="address1" value="1228 W La Entrada">
				</div>
				<br>

				<div class="form-group">
					<label for="address2"></label>
					<input type="text" id="address2" name="address2">
				</div>
				<br>

				<div class="form-group">
					<label for="city">City</label>
					<input type="text" id="city" name="city" value="Corrales">
				</div>
				<div class="form-group">
					<label for="state">State</label>
					<input type="text" id="state" name="state" value="NM">
				</div>
				<div class="form-group">
					<label for="zipCode">Zip Code</label>
					<input type="text" id="zipCode" name="zipCode" value="87048">
				</div>
				<div class="form-group">
					<label for="country">Country</label>
					<input type="text" id="country" name="country">
				</div>

				<br>
				<br>
				<button type="submit">Submit</button>
				<br>
				<br>
				<p id="outputArea"></p>
				<br>
			</form>
		<?php

			$currentDir = dirname(__FILE__);
			require_once("../dummy-session-single.php");
			require_once ("../root-path.php");

			require_once("/etc/apache2/capstone-mysql/encrypted-config.php");

			try {
				mysqli_report(MYSQLI_REPORT_STRICT);
				$configArray = readConfig("/etc/apache2/capstone-mysql/farmtoyou.ini");
				$mysqli = new mysqli($configArray['hostname'], $configArray['username'], $configArray['password'], $configArray['database']);

				$stores = Store::getAllStoresByProfileId($mysqli, $_SESSION['profile']['id']);
				if($stores !== null) {

						echo '<table class="table table-responsive">';
						echo '<tr>';
						echo '<th>Store</th>';
						echo '<th></th>';
						echo '</tr>';
						foreach($stores as $store) {
							$storeId = $store->getStoreId();
							$storeName = $store->getStoreName();
							echo '<tr>';
							echo '<td>'. $storeId ." - " . $storeName . '</td>';

							echo '<td><a href="../edit-store/index.php" id="editButton" class="btn btn-default store-edit">Edit '.$storeId.' </a></td>';
							echo '</tr>';
						}
						echo '</table>';
				}

			} catch(Exception $exception) {
				echo "<p class=\"alert alert-danger\">Exception: " . $exception->getMessage() . "</p>";
			}

				?>
			</table>
		</div>

	</div>
</div>

<?php require_once "../php/lib/footer.php"; ?>