<?php
require_once("../classes/profile.php");
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");
require_once("../classes/user.php");


// verify the form values have been submitted
if(@isset($_POST["inputFirstname"]) === false || @isset($_POST["inputLastname"]) === false
	|| @isset($_POST["inputType"]) === false || @isset($_POST["inputPhone"]) === false)  {
	echo "<p class=\"alert alert-danger\">Form values not complete. Verify the form and try again.</p>";
}

function getRandomWord($len = 10) {
	$word = array_merge(range('a', 'z'), range('A', 'Z'));
	shuffle($word);
	return substr(implode($word), 0, $len);
}
$randActivation = bin2hex(openssl_random_pseudo_bytes(8));

$randSalt = bin2hex(openssl_random_pseudo_bytes(16));

$randHash = bin2hex(openssl_random_pseudo_bytes(64));

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