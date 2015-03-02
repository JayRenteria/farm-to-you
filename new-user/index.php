<?php

$currentDir = dirname(__FILE__);
require_once '../root-path.php';
require_once('../php/lib/header.php');

?>

<div class="home-top">
	<div class="home-top-search-area hidden-xs">
		<h1 class="heading">Delicious products and fair trades directly from the farmers</h1>
		<form class="mt30" action="../php/forms/search-controller.php" id="search" method="post">
			<div class="input-group">
				<input class="form-control search-field" type="text" id="inputSearch" name="inputSearch" placeholder="What are looking for today?" />
				<input type="hidden" value="yes" name="searching">
				<span class="input-group-btn">
				  <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-search"></span></button>
				</span>
			</div>
		</form>
		<p class="outputArea"></p>
	</div>

	<div class="container-fluid visible-xs">
		<div class="row">
			<div class="col-xs-12">
				<h1 class="heading">Delicious products and fair trades directly from the farmers</h1>
				<form class="mt30" action="../php/forms/search-controller.php" id="search" method="post">
					<div class="input-group">
						<input class="form-control search-field" type="text" id="inputSearch" name="inputSearch" placeholder="What are looking for today?" />
						<input type="hidden" value="yes" name="searching">
						<span class="input-group-btn">
						  <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-search"></span></button>
						</span>
					</div>
				</form>
				<p class="outputArea"></p>
			</div>
		</div>
	</div>

</div>



<div class="home-main">

</div>

<?php require_once('../php/lib/footer.php'); ?>