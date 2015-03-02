/**
 * Created by jason on 3/2/2015.
 */
// open a new window with the form under scrutiny
module("tabs", {
	setup: function() {
		F.open("../../sign-in/index.php");
	}
});

// global variables for form values

var VALID_EMAIL = "big@dog2";
var VALID_PASSWORD = "dog";
/**
 * test filling in only valid form data
 **/
function testValidFields() {
	// fill in the form values
	F("#email").type(VALID_EMAIL);
	F("#password2").type(VALID_PASSWORD);

	// click the button once all the fields are filled in
	F("#submit").click();

	// wait for the login page to load
	F('#autocomplete_results:first-child').visible()

	// click the logout button
	F("#logout").click();

	// wait for the logout page to load
	F('#autocomplete_results:first-child').visible()

	// try to access account

	// in forms, we want to assert the form worked as expected
	// here, we assert we got the success message from the AJAX call
	F(".alert").visible(function() {
		// create a expression that evaluates the successful text
		ok(F(this).hasClass("alert-success"), "successful alert CSS");
		ok(F(this).html().indexOf("You are signed in!") === 0, "successful message");
	});
}

/**
 * test filling in invalid form data
 **/
function testInvalidFields() {

	// delete default form value and fill in the form value
	F("#email").type(INVALID_EMAIL);
	F("#password2").type(INVALID_PASSWORD);

	// click the button once field is filled in
	F("#submit").click();

	// assert we got the php error message from the AJAX call
	F(".alert").visible(function() {
		ok(F(this).hasClass("alert-danger"), "danger alert CSS");
		ok(F(this).html().indexOf("Exception: password input does not match existing account") === 0, "unsuccessful message");
	});

}

// the test function *MUST* be called in order for the test to execute
test("test valid fields", testValidFields);
test("test invalid fields", testInvalidFields);
