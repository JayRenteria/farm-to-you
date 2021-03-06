// document ready event
$(document).ready(
	// inner function for the ready() event
	function() {

		// tell the validator to validate this form (by id)
		$("#bankInfo").validate({
			// setup the formatting for the errors
			errorClass: "label-danger",
			errorLabelContainer: "#outputArea",
			wrapper: "li",

			// rules define what is good/bad input
			rules: {
				// each rule starts with the inputs name (NOT id)
				country: {
					required: true
				}
			},

			// error messages to display to the end user
			messages: {
				country: {
					required: "Please select a country."
				}
			},

			//setup an AJAX call to submit the form without reloading
			submitHandler: function(form) {
				$(form).ajaxSubmit({
					// GET or POST
					type: "POST",
					// where to submit data
					url: "../php/forms/bank-account-controller.php",
					// TL; DR: reformat POST data
					data: $(form),
					// success is an event that happens when the server replies
					success: function(ajaxOutput) {
						// clear the output area's formatting
						$("#outputArea").css("display", "block");
						// write the server's reply to the output area
						$("#outputArea").html(ajaxOutput);


						// reset the form if it was successful
						// this makes it easier to reuse the form again
						//if($(".alert-success").length >= 1) {
						//	$(form)[0].reset();
						//}
					}
				});
			}
		});
	});