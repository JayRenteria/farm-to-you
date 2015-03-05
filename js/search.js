
//$('.list-group-item').on('click', function(event) {
//
//	// force the link to be triggered
//	window.location.href = $(this).prop('href');
//});


//tell the validator to validate this form (by id)
$("#search").validate({
	// setup the formatting for the errors
	errorClass: "label-danger",
	errorLabelContainer: "#outputArea",
	wrapper: "li",

	// rules define what is good/bad input
	rules: {
		// each rule starts with the inputs name (NOT id)
		inputSearch: {
			minlength: 1,
			required: true
		}
	},

	// error messages to display to the end user
	messages: {
		inputFirstname: {

			minlength: "Search must have at least 1 character",
			required: "Please enter a search term"
		}
	}
});

$productsCardId = $('[id^=product-]');

$('.list-group-item').on('click', function() {

	event.preventDefault();

	// the default link should not trigger the ajax callback
	if($(this).hasClass('static')) {
		$productsCardId.each(function() {
			$(this).show();
		});
		return;
	}

	var data = {
		'category': $(this).text(),
		'searchTerm': $('#search-term').text()
	}

	$.ajax({
		type: "post",
		url: "../php/forms/category-search-controller.php",
		//dataType: 'json',
		data: data
	})
		.done(function(jsonOutput) {
			console.log(jsonOutput);
			var jsonOutput = jQuery.parseJSON(jsonOutput);

			// if there is an error
			if(typeof(jsonOutput.error) !== 'undefined') {
				console.log(jsonOutput.error);
				return;
			}
			var productIds = jsonOutput.products;

			// loop for each product card from the store view
			$productsCardId.each(function() {
				var $current = $(this);
				var hide = false;

				for(var i = 0; i < productIds.length; i++) {

					// get the id integer from the html id property
					var id = parseInt($current.prop('id').split('product-')[1]);
					if(id === productIds[i]) {
						hide = true;
					}
				}

				if(hide === true) {
					$current.hide();
				} else {
					$current.show();
				}
			});
		});
});