$(document).ready(
// inner function for the ready() event


	function() {


		//function addStoreEdit(referrer) {
		//	$.ajax({
		//		ßtype: "POST",
		//		url: "../store/index.php",
		//		data: {storeList: referrer.id},
		//		success: function(data) {
		//			$("#statusBar").html(data);
		//		}
		//	});
		//$(".store-edit").onclick(function() {
		//
		//
		//	var storeEditObject = {
		//		type: "post",
		//		url: ""
		//	}
		//	$.ajax(storeEditObject).done(function(ajaxOutput) {
		//
		//	});
		//})


		// tell the validator to validate this form (by id)
		$("#editStoreController").validate({
			// setup the formatting for the errors
			errorClass: "label-danger",
			errorLabelContainer: "#outputArea",
			wrapper: "li",

			// rules define what is good/bad input
			rules: {
				// each rule starts with the inputs name (NOT id)
				editStoreName: {
					maxlength: 100,
					required: true
				},

				editStoreDescription: {
					maxlength: 4294967295,
					required: false
				},

				editInputImage: {

					required: false,
					maxlength: 255
				}

			},

			// error messages to display to the end user
			messages: {
				editStoreName: {

					maxlength: "Store name cannot exceed 100 characters",
					required: "Please enter the store name."
				},

				editStoreDescription: {
					maxlength: "Store description too long!"
				},

				editInputImage: {
					maxlength: "Image path is too long!"

				}
			},

			//setup an AJAX call to submit the form without reloading
			submitHandler: function(form) {
				$(form).ajaxSubmit({
					// GET or POST
					type: "POST",
					// where to submit data
					url: "../php/forms/edit-store-controller.php",
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
						if($(".alert-success").length >= 1) {
							$(form)[0].reset();
						}
					}
				});
			}
		});
	});