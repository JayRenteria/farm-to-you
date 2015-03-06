<?php
/**
 * Sign in index
 * User: jason
 * Date: 2/12/2015
 * Time: 9:03 AM
 */

$currentDir = dirname(__FILE__);
require_once '../root-path.php';
require_once("../php/lib/header.php");

?>
<script src="../js/sign-in.js"></script>


<div class="signIn">
	<div class="signIn-form">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">

				<h3>Welcome Back!</h3>

				<br>

				<form class= "form" method="post" name="signIn" id="signIn" action="../php/forms/sign-in-controller.php">
					<?php echo generateInputTags(); ?>
					<fieldset>
						<?php
						echo generateInputTags();
						?>
						<label>Your email:</label>
						<input type="text" name="email" id="email" value="" size="relative" />
						</br></br>
						<label>Enter your password:</label>
						<input type="password" name="password2" id="password2" value="" size="relative" />
						<br><br>
						<input type="submit" value="Log In" id="submit">
					</fieldset>
				</form>

				<p id="outputArea"></p>
			</div>
		</div><!-- end row -->
	</div><!-- end container-fluid -->
	</div><!-- end signIn form-->
</div><!-- signIn -->
<?php require_once ("../php/lib/footer.php"); ?>
