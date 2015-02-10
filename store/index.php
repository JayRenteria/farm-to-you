<?php
//
//require_once('../php/lib/header.php');
//
//?>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/2.7.5/idangerous.swiper.min.css"/>
<link rel="stylesheet" href="../css/main.css"/>

<div class="container">
	<h2>Add Store</h2>
		<form class="form-inline" id="tweetController" method="post" action="../php/forms/controller-store.php">
			<div class="form-group">
				<label for="storeName">Store Name</label>
				<input type="text" id="storeName" name="storeName">
			</div>
				<br>
			<div class="form-group">
				<label for="storeDescription">Store Description</label>
				<input type="text" id="storeDescription" name="storeDescription">
			</div>
			<br>

			<div class="form-group">
				<label for="InputImage">Store Image</label>
				<input type="file" id="InputImage" name="InputImage">
			</div>
			<br>
<br>
			<button type="submit">Submit</button>
			<br>
		</form>
		<p id="outputArea"></p>
		</div>
