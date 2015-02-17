<?php
require_once("../classes/profile.php");
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");


// verify the form values have been submitted
if(@isset($_POST["inputFirstname"]) === false || @isset($_POST["inputLastname"]) === false
	|| @isset($_POST["inputType"]) === false || @isset($_POST["inputPhone"]) === false)  {
	echo "<p class=\"alert alert-danger\">Form values not complete. Verify the form and try again.</p>";
}

try {
	//
	mysqli_report(MYSQLI_REPORT_STRICT);
	$configArray = readConfig("/etc/apache2/capstone-mysql/farmtoyou.ini");
	$mysqli = new mysqli($configArray['hostname'], $configArray['username'], $configArray['password'], $configArray['database']);
	if(@isset($_POST["inputImage"])) {
		// the 13 and 25 are holders for profile id of 17 and user id of 25
		$profile = new Profile(886, $_POST["inputFirstname"], $_POST["inputLastname"], $_POST["inputPhone"], $_POST["inputType"], "012345", $_POST["inputImage"], 919);
	} else {
		$profile = new Profile(886, $_POST["inputFirstname"], $_POST["inputLastname"], $_POST["inputPhone"], $_POST["inputType"], "012345", null, 919);

	}
	$profile->update($mysqli);
	echo "<p class=\"alert alert-success\">Profile (id = " . $profile->getProfileId() . ") updated!</p>";
} catch(Exception $exception) {
	echo "<p class=\"alert alert-danger\">Exception: " . $exception->getMessage() . "</p>";
}
