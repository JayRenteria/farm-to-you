<?php
/**
 * Created by PhpStorm.
 * User: jason
 * Date: 2/11/2015
 * Time: 2:49 PM
 */
require_once("../classes/user.php");
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");
//// require CSRF protection
//require_once("../../../lib/csrf.php");
//// require encrypted configuration files
//require_once("../../../lib/encrypted-config.php");
//// require PEAR::Mail <http://pear.php.net/package/Mail> to send mail
//require_once("Mail.php");

//generate salt, hash, and activation
$salt = bin2hex(openssl_random_pseudo_bytes(16));
$hash = hash_pbkdf2("sha512", "password", $salt, 2048, 128);
$activation = bin2hex(openssl_random_pseudo_bytes(8));

var_dump($hash);


if(!@isset($_POST["inputEmail"]) || !@isset($_POST["password"])) {
	throw new Exception('invalid input post');
}

try {
//	mysqli_report(MYSQLI_REPORT_STRICT);
	$configArray = readConfig("/etc/apache2/capstone-mysql/farmtoyou.ini");
	$mysqli = new mysqli($configArray['hostname'], $configArray['username'], $configArray['password'], $configArray['database']);

	$user = new User(null, $_POST["inputEmail"], $hash, $salt, $activation);
	$user->insert($mysqli);


	echo "<p class=\"Thank you for signing up with Farm To You! We have sent an email to the address you entered. Please open the email and follow the instructions to complete the registration process.success!\"></p>";
} catch(Exception $exception) {
	echo "<p class=\"input not posted!\">Exception: " . $exception->getMessage() . "</p>";
}

// email the user with an activation message
//$to = $_POST["inputEmail"];
//$from = "CEO@farmtoyou.com";
//
//// build headers
//$headers = array();
//$headers["To"] = $to;
//$headers["From"] = $from;
//$headers["Reply-To"] = $from;
//$headers["Subject"] = "Welcome to Farm To You!";
//$headers["MIME-Version"] = "1.0";
//$headers["Content-Type"] = "text/html; charset=UTF-8";
//
//// build message
//$pageName = end(explode("/", $_SERVER["PHP_SELF"]));
//$url = "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["PHP_SELF"];
//$url = str_replace($pageName, "sign-up-controller.php", $url);
//$url = "$url?activation=$activation";
//$message = <<< EOF
//<html>
//<body>
//<h1>Welcome to Farm To You!</h1>
//<hr />
//<p>Thank you for creating a password. Visit the following URL to complete your registration process: <a href="$url">$url</a>.</p>
//</body>
//</html>
//EOF;
//
//// send the email
//error_reporting(E_ALL & ~E_STRICT);
//$mailer =& Mail::factory("sendmail");
//$status = $mailer->send($to, $headers, $message);
//if(PEAR::isError($status) === true)
//{
//	echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Oh snap!</strong> Unable to send mail message:" . $status->getMessage() . "</div>";
//}
//else
//{
//	echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Sign up successful!</strong> Please check your Email to complete the signup process.</div>";
//}
