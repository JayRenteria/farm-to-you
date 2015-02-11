<?php
/**
 * Created by PhpStorm.
 * User: jason
 * Date: 2/11/2015
 * Time: 2:49 PM
 */
require_once("../classes/user.php");
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");


// verify the form values have been submitted
if(@isset($_POST["email"]) === false || @isset($_POST["hash"]) === false) {
	echo "<p class=\"alert alert-danger\">form values not complete. Verify the form and try again.</p>";
}

try {
//
	mysqli_report(MYSQLI_REPORT_STRICT);
	$configArray = readConfig("/etc/apache2/capstone-mysql/farmtoyou.ini");
	$mysqli = new mysqli($configArray['hostname'], $configArray['username'], $configArray['password'], $configArray['database']);

	$user = new User(null, getRandomWord() . "@test.com", $randHash, $randSalt, $randActivation);
	$user->insert($mysqli);

	if(@isset($_POST["inputImage"])) {
		$profile = new Profile(null, $_POST["inputFirstname"], $_POST["inputLastname"], $_POST["inputPhone"], $_POST["inputType"], "012345", $_POST["inputImage"], $user->getUserId());
	} else {
		$profile = new Profile(null, $_POST["inputFirstname"], $_POST["inputLastname"], $_POST["inputPhone"], $_POST["inputType"], "012345", null, $user->getUserId());

	}
	$profile->insert($mysqli);
	echo "<p class=\"alert alert-success\">Profile (id = " . $profile->getProfileId() . ") posted!</p>";
} catch(Exception $exception) {
	echo "<p class=\"alert alert-danger\">Exception: " . $exception->getMessage() . "</p>";
}