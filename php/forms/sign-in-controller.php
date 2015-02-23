<?php
/**
 * Created by PhpStorm.
 * User: jason
 * Date: 2/11/2015
 * Time: 2:49 PM
 */

require_once("../classes/user.php");
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");
// require CSRF protection
 require_once("../lib/csrf.php");

// CSRF requires sessions
session_start();

try {
	if(!@isset($_POST["email"]) || !@isset($_POST["password"])) {
		throw new Exception('invalid input post');
	}

	// verify the CSRF tokens
	if(verifyCsrf($_POST["csrfName"], $_POST["csrfToken"]) === false) {
		throw(new RuntimeException("CSRF tokens incorrect or missing. Make sure cookies are enabled."));
	}

	// filter _POST variables
$email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);



// gain access to database
	mysqli_report(MYSQLI_REPORT_STRICT);
	$configArray = readConfig("/etc/apache2/capstone-mysql/farmtoyou.ini");
	$mysqli = new mysqli($configArray['hostname'], $configArray['username'], $configArray['password'], $configArray['database']);

	// get the users email from mysqli
	$mysqlEmail = User::getUserByEmail($mysqli, $email);

	//get the mysqli hash and salt
	$mysqlHash = User($mysqli, $hash);
	$salt = User($mysqli, $salt);

	// generate hash from users password using mysqli salt
	$hash = hash_pbkdf2("sha512", "password", $salt, 2048, 128);

	// compare hashes
	if ($mysqlHash !== $hash) {
		throw new Exception('email input does not match existing account');
	}
	// catch any exceptions
} catch(Exception $exception) {
	echo "<p class=\"input not posted!\">Exception: " . $exception->getMessage() . "</p>";
}
// create session id specific to this user
$_SESSION['user'] = array(
	'id' => $user->getUserId()
);