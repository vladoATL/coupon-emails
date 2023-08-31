(function( $ ) {
	'use strict';
	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 */
	 
	 	jQuery( document ).on(
		"click",
		"#namedaysemail-setting #restore_values_btn",
		function(){
			event.preventDefault();
			var nonce = jQuery( this ).attr( 'attr-nonce' );
			var data  = {
				action: 'namedayemail_restore_settings',
				nonce: nonce,
			};
			jQuery.ajax(
				{
					type: "post",
					url: ajaxurl,
					data: data,
					beforeSend: function(response){
						jQuery( "#namedaysemail-setting .loader_cover" ).addClass( 'active' );
						jQuery( "#namedaysemail-setting .namedays_loader" ).addClass( 'loader' );
					},
					complete: function(response){
						jQuery( "#namedaysemail-setting .loader_cover" ).removeClass( 'active' );
						jQuery( "#namedaysemail-setting .namedays_loader" ).removeClass( 'loader' );
					},
					success: function(response) {
						location.reload();
					}
				}
			);
			return false;
		}
	);
	 
	jQuery( document ).on(
	"click",
	"#onetimeemail-setting #restore_one_values_btn",
	function() {
		event.preventDefault();
		var nonce = jQuery( this ).attr( 'attr-nonce' );
		var data  = {
			action: 'onetimeemail_restore_settings',
			nonce: nonce,
		};
		jQuery.ajax(
		{
			type: "post",
			url: ajaxurl,
			data: data,
			beforeSend: function(response) {
				jQuery( "#onetimeemail-setting .loader_cover" ).addClass( 'active' );
				jQuery( "#onetimeemail-setting .onetime_loader" ).addClass( 'loader' );
			},
			complete: function(response) {
				jQuery( "#onetimeemail-setting .loader_cover" ).removeClass( 'active' );
				jQuery( "#onetimeemail-setting .onetime_loader" ).removeClass( 'loader' );
			},
			success: function(response) {
				location.reload();
			}
		}
		);
		return false;
	}
	);
	
	jQuery( document ).on(
	"click",
	"#reorderemail-setting #restore_reorder_values_btn",
	function() {
		event.preventDefault();
		var nonce = jQuery( this ).attr( 'attr-nonce' );
		var data  = {
			action: 'reorderemail_restore_settings',
			nonce: nonce,
		};
		jQuery.ajax(
		{
			type: "post",
			url: ajaxurl,
			data: data,
			beforeSend: function(response) {
				jQuery( "#reorderemail-setting .loader_cover" ).addClass( 'active' );
				jQuery( "#reorderemail-setting .reorder_loader" ).addClass( 'loader' );
			},
			complete: function(response) {
				jQuery( "#reorderemail-setting .loader_cover" ).removeClass( 'active' );
				jQuery( "#reorderemail-setting .reorder_loader" ).removeClass( 'loader' );
			},
			success: function(response) {
				location.reload();
			}
		}
		);
		return false;
	}
	);
	
	jQuery( document ).on(
	"click",
	"#afterorderemail-setting #restore_afterorder_values_btn",
	function() {
		event.preventDefault();
		var nonce = jQuery( this ).attr( 'attr-nonce' );
		var data  = {
			action: 'afterorderemail_restore_settings',
			nonce: nonce,
		};
		jQuery.ajax(
		{
			type: "post",
			url: ajaxurl,
			data: data,
			beforeSend: function(response) {
				jQuery( "#afterorderemail-setting .loader_cover" ).addClass( 'active' );
				jQuery( "#afterorderemail-setting .afterorder_loader" ).addClass( 'loader' );
			},
			complete: function(response) {
				jQuery( "#afterorderemail-setting .loader_cover" ).removeClass( 'active' );
				jQuery( "#afterorderemail-setting .afterorder_loader" ).removeClass( 'loader' );
			},
			success: function(response) {
				location.reload();
			}
		}
		);
		return false;
	}
	);	
	
	
	 	jQuery( document ).on(
	"click",
	"#birthdaysemail-setting #restore_bd_values_btn",
	function() {
		event.preventDefault();
		var nonce = jQuery( this ).attr( 'attr-nonce' );
		var data  = {
			action: 'birthdayemail_restore_settings',
			nonce: nonce,
		};
		jQuery.ajax(
		{
			type: "post",
			url: ajaxurl,
			data: data,
			beforeSend: function(response) {
				jQuery( "#birthdaysemail-setting .loader_cover" ).addClass( 'active' );
				jQuery( "#birthdaysemail-setting .birthdays_loader" ).addClass( 'loader' );
			},
			complete: function(response) {
				jQuery( "#birthdaysemail-setting .loader_cover" ).removeClass( 'active' );
				jQuery( "#birthdaysemail-setting .birthdays_loader" ).removeClass( 'loader' );
			},
			success: function(response) {
				location.reload();
			}
		}
		);
		return false;
	}
	);
		 
	 	jQuery( document ).on(
	"click",
	"#namedaysemail-setting #download_btn",
	function() {
		event.preventDefault();
		var nonce = jQuery( this ).attr( 'attr-nonce' );
		var data  = {
			action: 'namedayemail_download_csv',
			nonce: nonce,
		};
		jQuery.ajax(
		{
			type: "post",
			url: ajaxurl,
			data: data,
			beforeSend: function(response) {
				jQuery( "#namedaysemail-setting .loader_cover" ).addClass( 'active' );
				jQuery( "#namedaysemail-setting .namedays_loader" ).addClass( 'loader' );
			},
			complete: function(response) {
				jQuery( "#namedaysemail-setting .loader_cover" ).removeClass( 'active' );
				jQuery( "#namedaysemail-setting .namedays_loader" ).removeClass( 'loader' );
			},
			success: function(response) {
				location.reload();
			}
		}
		);
		return false;
	}
	);
		 
	 	jQuery( document ).on(
		"click",
		"#clear_log_btn",
		function(){
			event.preventDefault();
			var nonce = jQuery( this ).attr( 'attr-nonce' );
			var data  = {
				action: 'couponemails_clear_log',
				nonce: nonce,
			};
			jQuery.ajax(
				{
					type: "post",
					url: ajaxurl,
					data: data,
					beforeSend: function(response){
						jQuery( "#namedaysemail-setting .loader_cover" ).addClass( 'active' );
						jQuery( "#namedaysemail-setting .namedays_loader" ).addClass( 'loader' );
					},
					complete: function(response){
						jQuery( "#namedaysemail-setting .loader_cover" ).removeClass( 'active' );
						jQuery( "#namedaysemail-setting .namedays_loader" ).removeClass( 'loader' );
					},
					success: function(response) {
						location.reload();
					}
				}
			);
			return false;
		}
	);	 

	 	jQuery( document ).on(
		"click",
		"#namedaysemail-setting #test_btn",
		function(){
			event.preventDefault();
			var nonce = jQuery( this ).attr( 'attr-nonce' );
			var data  = {
				action: 'namedayemail_make_test',
				nonce: nonce,
			};
			jQuery.ajax(
				{
					type: "post",
					url: ajaxurl,
					data: data,
					beforeSend: function(response){
						jQuery( "#namedaysemail-setting .loader_cover" ).addClass( 'active' );
						jQuery( "#namedaysemail-setting .namedays_loader" ).addClass( 'loader' );
					},
					complete: function(response){
						jQuery( "#namedaysemail-setting .loader_cover" ).removeClass( 'active' );
						jQuery( "#namedaysemail-setting .namedays_loader" ).removeClass( 'loader' );
					},
					success: function(response) {
						location.reload();
					}
				}
			);
			return false;
		}
	);	


jQuery( document ).on(
"click",
"#birthdaysemail-setting #test_bd_btn",
function() {
	event.preventDefault();
	var nonce = jQuery( this ).attr( 'attr-nonce' );
	var data  = {
		action: 'birthdayemail_make_test',
		nonce: nonce,
	};
	jQuery.ajax(
	{
		type: "post",
		url: ajaxurl,
		data: data,
		beforeSend: function(response) {
			jQuery( "#birthdaysemail-setting .loader_cover" ).addClass( 'active' );
			jQuery( "#birthdaysemail-setting .birthdays_loader" ).addClass( 'loader' );
		},
		complete: function(response) {
			jQuery( "#birthdaysemail-setting .loader_cover" ).removeClass( 'active' );
			jQuery( "#birthdaysemail-setting .birthdays_loader" ).removeClass( 'loader' );
		},
		success: function(response) {
			location.reload();
		}
	}
	);
	return false;
}
);

jQuery( document ).on(
"click",
"#reorderemail-setting #test_reorder_btn",
function() {
	event.preventDefault();
	var nonce = jQuery( this ).attr( 'attr-nonce' );
	var data  = {
		action: 'reorderemail_make_test',
		nonce: nonce,
	};
	jQuery.ajax(
	{
		type: "post",
		url: ajaxurl,
		data: data,
		beforeSend: function(response) {
			jQuery( "#reorderemail-setting .loader_cover" ).addClass( 'active' );
			jQuery( "#reorderemail-setting .reorder_loader" ).addClass( 'loader' );
		},
		complete: function(response) {
			jQuery( "#reorderemail-setting .loader_cover" ).removeClass( 'active' );
			jQuery( "#reorderemail-setting .reorder_loader" ).removeClass( 'loader' );
		},
		success: function(response) {
			location.reload();
		}
	}
	);
	return false;
}
);

jQuery( document ).on(
"click",
"#onetimeemail-setting #test_onetime_btn",
function() {
	event.preventDefault();
	var nonce = jQuery( this ).attr( 'attr-nonce' );
	var data  = {
		action: 'onetimeemail_make_test',
		nonce: nonce,
	};
	jQuery.ajax(
	{
		type: "post",
		url: ajaxurl,
		data: data,
		beforeSend: function(response) {
			jQuery( "#onetimeemail-setting .loader_cover" ).addClass( 'active' );
			jQuery( "#onetimeemail-setting .onetime_loader" ).addClass( 'loader' );
		},
		complete: function(response) {
			jQuery( "#onetimeemail-setting .loader_cover" ).removeClass( 'active' );
			jQuery( "#onetimeemail-setting .onetime_loader" ).removeClass( 'loader' );
		},
		success: function(response) {
			location.reload();
		}
	}
	);
	return false;
}
);

jQuery( document ).on(
"click",
"#afterorderemail-setting #test_afterorder_btn",
function() {
	event.preventDefault();
	var nonce = jQuery( this ).attr( 'attr-nonce' );
	var data  = {
		action: 'afterorderemail_make_test',
		nonce: nonce,
	};
	jQuery.ajax(
	{
		type: "post",
		url: ajaxurl,
		data: data,
		beforeSend: function(response) {
			jQuery( "#afterorderemail-setting .loader_cover" ).addClass( 'active' );
			jQuery( "#afterorderemail-setting .afterorder_loader" ).addClass( 'loader' );
		},
		complete: function(response) {
			jQuery( "#afterorderemail-setting .loader_cover" ).removeClass( 'active' );
			jQuery( "#afterorderemail-setting .afterorder_loader" ).removeClass( 'loader' );
		},
		success: function(response) {
			location.reload();
		}
	}
	);
	return false;
}
);

})( jQuery );

jQuery( function( $ ) {
    $('.woocommerce-help-tip').tipTip({
        'attribute': 'data-tip',
        'fadeIn':    50,
        'fadeOut':   50,
        'delay':     200
    });
});
