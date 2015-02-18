<?php
session_start();
$currentDir = dirname(__FILE__);

require_once("../dummy-session.php");
require_once ("../root-path.php");
require_once("../php/lib/header.php");
?>

	<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery.form/3.51/jquery.form.min.js"></script>
	<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.12.0/jquery.validate.min.js"></script>
	<script src="../js/add-product.js"></script>

<!--Form for adding a new product-->
<div class="container">
	<h2>Add Product</h2>

	<form id="addProduct" class="form-inline" method="post" action="../php/forms/add-product-controller.php" novalidate>

		<div class="form-group">
			<label for="inputProductName">Product Name:</label>
			<input type="text" class="form-control" name="inputProductName" id="inputProductName" placeholder="Product Name">
		</div>

		<br>

		<div class="form-group">
			<label for="inputProductPrice">Product Price:</label>
			<input type="text" class="form-control" name="inputProductPrice" id="inputProductPrice" placeholder="Price">
		</div>

		<br>

		<div class="form-group">
			<label for="inputProductDescription">Product Description:</label>
			<textarea class="form-control" name="inputProductDescription" id="inputProductDescription" placeholder="Write Product Description here."></textarea>
		</div>

		<br>

		<div class="form-group">
			<label for="inputProductPriceType">Product Price Type:</label>
			<input type="radio" class="form-control" name="inputProductPriceType" id="inputProductPriceType" value="w">By Weight
			<input type="radio" class="form-control" name="inputProductPriceType" id="inputProductPriceType" value="u">Per Unit
		</div>

		<br>

		<div class="form-group">
			<label for="inputProductWeight">Product Weight:</label>
			<input type="text" class="form-control" name="inputProductWeight" id="inputProductWeight" placeholder="Weight">
		</div>

		<br>

		<div class="form-group">
			<label for="inputStockLimit">Current Stock Amount:</label>
			<input type="number" class="form-control" name="inputStockLimit" id="inputStockLimit" step="1">
		</div>

		<br>

		<div class="form-group">
			<label for="inputProductImage">Product Image:</label>
			<input type="file" class="form-control" name="inputProductImage" id="inputProductImage" value="">
		</div>

		<br>

		<div class="form-group">
			<input type="submit" class="form-control" id="inputSubmit" name="inputSubmit" value="Submit">
		</div>

		<p id="outputArea" style=""></p>

	</form>

	<?php
require_once("../php/lib/footer.php")
?>