<?php

$currentDir = dirname(__FILE__);
require_once("../../root-path.php");
require_once("../lib/header.php");
require_once("../classes/profile.php");
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");
require_once("../classes/user.php");
require_once("../../dummy-user-session.php");
require_once("../lib/footer.php");
require_once("../lib/utils.php");



// verify the form values have been submitted
if(@isset($_POST["inputFirstname"]) === false || @isset($_POST["inputLastname"]) === false
	|| @isset($_POST["inputType"]) === false || @isset($_POST["inputPhone"]) === false)  {
	echo "<p class=\"alert alert-danger\">Form values not complete. Verify the form and try again.</p>";
}


try {
	//insert the new profile
	mysqli_report(MYSQLI_REPORT_STRICT);
	$configArray = readConfig("/etc/apache2/capstone-mysql/farmtoyou.ini");
	$mysqli = new mysqli($configArray['hostname'], $configArray['username'], $configArray['password'], $configArray['database']);

		$profile = new Profile(null, $_POST["inputFirstname"], $_POST["inputLastname"], $_POST["inputPhone"], $_POST["inputType"], "012345", null, $user->getUserId());

	if(@isset($_FILES['inputImage'])) {
		$imageBasePath = '/var/www/html/farm-to-you/images/profile/';
		$imageExtension = checkInputImage($_FILES['inputImage']);
		$profile->insert($mysqli);
		$profileId = $profile->getProfileId();
		$imageFileName = $imageBasePath . 'profile-' . $profileId . '.' . $imageExtension;
		$profile->setImagePath($imageFileName);
		$profile->update($mysqli);
		move_uploaded_file($_FILES['inputImage']['tmp_name'], $imageFileName);
	} else {
		$profile->setImagePath(null);
		$profile->insert($mysqli);
		$profileId = $profile->getProfileId();
	}

	echo "<p class=\"alert alert-success\">Profile (id = " . $profile->getProfileId() . ") posted!</p>";
} catch(Exception $exception) {
	echo "<p class=\"alert alert-danger\">Exception: " . $exception->getMessage() . "</p>";
}

// make a table/list to show their new profile
if($exception = false) {
	echo "<div class=\"container\">";
	echo "<h2>My Profile</h2>";
	echo "<ul class=\"list-group\">";
	echo "<li class=\"list-group-item\">First Name: " . $_POST['inputFirstname'] . "</li>";
	echo "<li class=\"list-group-item\">Last Name: " . $_POST['inputLastname'] . "</li>";
	echo "<li class=\"list-group-item\">Profile Type (m = merchant, c = client): " . $_POST['inputType'] . "</li>";
	echo "<li class=\"list-group-item\">Phone Number: " . $_POST['inputPhone'] . "</li>";
	echo "</ul>";

	echo "<form action=\"../../edit-profile/index.php\">";
	echo "<input class =\"form-control\" type=\"submit\" value=\"Edit Profile\">";
	echo "</form>";

	echo "</div>";
}else{
	echo"Please go back and change the above";
}