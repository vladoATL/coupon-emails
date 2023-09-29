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
		"#namedayemail-setting #restore_namedayemail_values_btn",
		function(){
			event.preventDefault();
			var nonce = jQuery( this ).attr( 'attr-nonce' );
			var data  = {
				action: 'email_restore_settings',
				nonce: nonce,
				option_name: 'namedayemail',
			};
			jQuery.ajax(
				{
					type: "post",
					url: ajaxurl,
					data: data,
					beforeSend: function(response){
						jQuery( "#namedayemail-setting .loader_cover" ).addClass( 'active' );
						jQuery( "#namedayemail-setting .namedays_loader" ).addClass( 'loader' );
					},
					complete: function(response){
						jQuery( "#namedayemail-setting .loader_cover" ).removeClass( 'active' );
						jQuery( "#namedayemail-setting .namedays_loader" ).removeClass( 'loader' );
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
	"#referralconfirmationemail-setting #restore_referralconfirmationemail_values_btn",
	function() {
		event.preventDefault();
		var nonce = jQuery( this ).attr( 'attr-nonce' );
		var data  = {
			action: 'email_restore_settings',
			nonce: nonce,
			option_name: 'referralconfirmationemail',
		};
		jQuery.ajax(
		{
			type: "post",
			url: ajaxurl,
			data: data,
			beforeSend: function(response) {
				jQuery( "#referralconfirmationemail-setting .loader_cover" ).addClass( 'active' );
				jQuery( "#referralconfirmationemail-setting .namedays_loader" ).addClass( 'loader' );
			},
			complete: function(response) {
				jQuery( "#referralconfirmationemail-setting .loader_cover" ).removeClass( 'active' );
				jQuery( "#referralconfirmationemail-setting .namedays_loader" ).removeClass( 'loader' );
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
	"#referralemail-setting #restore_referralemail_values_btn",
	function() {
		event.preventDefault();
		var nonce = jQuery( this ).attr( 'attr-nonce' );
		var data  = {
			action: 'email_restore_settings',
			nonce: nonce,
			option_name: 'referralemail',
		};
		jQuery.ajax(
		{
			type: "post",
			url: ajaxurl,
			data: data,
			beforeSend: function(response) {
				jQuery( "#referralemail-setting .loader_cover" ).addClass( 'active' );
				jQuery( "#referralemail-setting .namedays_loader" ).addClass( 'loader' );
			},
			complete: function(response) {
				jQuery( "#referralemail-setting .loader_cover" ).removeClass( 'active' );
				jQuery( "#referralemail-setting .namedays_loader" ).removeClass( 'loader' );
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
	"#reviewedemail-setting #restore_reviewed_values_btn",
	function() {
		event.preventDefault();
		var nonce = jQuery( this ).attr( 'attr-nonce' );
		var data  = {
			action: 'email_restore_settings',
			nonce: nonce,
			option_name: 'reviewedemail',
		};
		jQuery.ajax(
		{
			type: "post",
			url: ajaxurl,
			data: data,
			beforeSend: function(response) {
				jQuery( "#reviewedemail-setting .loader_cover" ).addClass( 'active' );
				jQuery( "#reviewedemail-setting .reviewed_loader" ).addClass( 'loader' );
			},
			complete: function(response) {
				jQuery( "#reviewedemail-setting .loader_cover" ).removeClass( 'active' );
				jQuery( "#reviewedemail-setting .reviewed_loader" ).removeClass( 'loader' );
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
	"#onetimeemail-setting #restore_onetimeemail_values_btn",
	function() {
		event.preventDefault();
		var nonce = jQuery( this ).attr( 'attr-nonce' );
		var data  = {
			action: 'email_restore_settings',
			nonce: nonce,
			option_name: 'onetimeemail',
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
			action: 'email_restore_settings',
			nonce: nonce,
			option_name: 'reorderemail',
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
	"#reviewreminderemail-setting #restore_reviewreminderemail_values_btn",
	function() {
		event.preventDefault();
		var nonce = jQuery( this ).attr( 'attr-nonce' );
		var data  = {
			action: 'email_restore_settings',
			nonce: nonce,
			option_name: 'reviewreminderemail',
		};
		jQuery.ajax(
		{
			type: "post",
			url: ajaxurl,
			data: data,
			beforeSend: function(response) {
				jQuery( "#reviewreminderemail-setting .loader_cover" ).addClass( 'active' );
				jQuery( "#reviewreminderemail-setting .reviewreminderemail_loader" ).addClass( 'loader' );
			},
			complete: function(response) {
				jQuery( "#reviewreminderemail-setting .loader_cover" ).removeClass( 'active' );
				jQuery( "#reviewreminderemail-setting .reviewreminderemail_loader" ).removeClass( 'loader' );
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
	"#expirationreminderemail-setting #restore_expirationreminderemail_values_btn",
	function() {
		event.preventDefault();
		var nonce = jQuery( this ).attr( 'attr-nonce' );
		var data  = {
			action: 'email_restore_settings',
			nonce: nonce,
			option_name: 'expirationreminderemail',
		};
		jQuery.ajax(
		{
			type: "post",
			url: ajaxurl,
			data: data,
			beforeSend: function(response) {
				jQuery( "#expirationreminderemail-setting .loader_cover" ).addClass( 'active' );
				jQuery( "#expirationreminderemail-setting .expirationreminderemail_loader" ).addClass( 'loader' );
			},
			complete: function(response) {
				jQuery( "#expirationreminderemail-setting .loader_cover" ).removeClass( 'active' );
				jQuery( "#expirationreminderemail-setting .expirationreminderemail_loader" ).removeClass( 'loader' );
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
			action: 'email_restore_settings',
			nonce: nonce,
			option_name: 'afterorderemail',
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
	"#birthdayemail-setting #restore_birthdayemail_values_btn",
	function() {
		event.preventDefault();
		var nonce = jQuery( this ).attr( 'attr-nonce' );
		var data  = {
			action: 'email_restore_settings',
			nonce: nonce,
			option_name: 'birthdayemail',
		};
		jQuery.ajax(
		{
			type: "post",
			url: ajaxurl,
			data: data,
			beforeSend: function(response) {
				jQuery( "#birthdayemail-setting .loader_cover" ).addClass( 'active' );
				jQuery( "#birthdayemail-setting .birthdays_loader" ).addClass( 'loader' );
			},
			complete: function(response) {
				jQuery( "#birthdayemail-setting .loader_cover" ).removeClass( 'active' );
				jQuery( "#birthdayemail-setting .birthdays_loader" ).removeClass( 'loader' );
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
	"#namedayemail-setting #download_btn",
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
				jQuery( "#namedayemail-setting .loader_cover" ).addClass( 'active' );
				jQuery( "#namedayemail-setting .namedays_loader" ).addClass( 'loader' );
			},
			complete: function(response) {
				jQuery( "#namedayemail-setting .loader_cover" ).removeClass( 'active' );
				jQuery( "#namedayemail-setting .namedays_loader" ).removeClass( 'loader' );
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
						jQuery( "#namedayemail-setting .loader_cover" ).addClass( 'active' );
						jQuery( "#namedayemail-setting .namedays_loader" ).addClass( 'loader' );
					},
					complete: function(response){
						jQuery( "#namedayemail-setting .loader_cover" ).removeClass( 'active' );
						jQuery( "#namedayemail-setting .namedays_loader" ).removeClass( 'loader' );
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
		"#namedayemail-setting #test_namedayemail_btn",
		function(){
			event.preventDefault();
			var nonce = jQuery( this ).attr( 'attr-nonce' );
			var data  = {
				action: 'email_make_test',
				nonce: nonce,
				option_name: 'namedayemail',
			};
			jQuery.ajax(
				{
					type: "post",
					url: ajaxurl,
					data: data,
					beforeSend: function(response){
						jQuery( "#namedayemail-setting .loader_cover" ).addClass( 'active' );
						jQuery( "#namedayemail-setting .namedays_loader" ).addClass( 'loader' );
					},
					complete: function(response){
						jQuery( "#namedayemail-setting .loader_cover" ).removeClass( 'active' );
						jQuery( "#namedayemail-setting .namedays_loader" ).removeClass( 'loader' );
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
	"#referralemail-setting #test_referralemail_btn",
	function() {
		event.preventDefault();
		var nonce = jQuery( this ).attr( 'attr-nonce' );
		var data  = {
			action: 'email_make_test',
			nonce: nonce,
			option_name: 'referralemail',
		};
		jQuery.ajax(
		{
			type: "post",
			url: ajaxurl,
			data: data,
			beforeSend: function(response) {
				jQuery( "#referralemail-setting .loader_cover" ).addClass( 'active' );
				jQuery( "#referralemail-setting .namedays_loader" ).addClass( 'loader' );
			},
			complete: function(response) {
				jQuery( "#referralemail-setting .loader_cover" ).removeClass( 'active' );
				jQuery( "#referralemail-setting .namedays_loader" ).removeClass( 'loader' );
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
	"#referralconfirmationemail-setting #test_referralconfirmationemail_btn",
	function() {
		event.preventDefault();
		var nonce = jQuery( this ).attr( 'attr-nonce' );
		var data  = {
			action: 'email_make_test',
			nonce: nonce,
			option_name: 'referralconfirmationemail',
		};
		jQuery.ajax(
		{
			type: "post",
			url: ajaxurl,
			data: data,
			beforeSend: function(response) {
				jQuery( "#referralconfirmationemail-setting .loader_cover" ).addClass( 'active' );
				jQuery( "#referralconfirmationemail-setting .namedays_loader" ).addClass( 'loader' );
			},
			complete: function(response) {
				jQuery( "#referralconfirmationemail-setting .loader_cover" ).removeClass( 'active' );
				jQuery( "#referralconfirmationemail-setting .namedays_loader" ).removeClass( 'loader' );
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
	"#reviewedemail-setting #test_reviewedemail_btn",
	function() {
		event.preventDefault();
		var nonce = jQuery( this ).attr( 'attr-nonce' );
		var data  = {
			action: 'email_make_test',
			nonce: nonce,
			option_name: 'reviewedemail',
		};
		jQuery.ajax(
		{
			type: "post",
			url: ajaxurl,
			data: data,
			beforeSend: function(response) {
				jQuery( "#reviewedemail-setting .loader_cover" ).addClass( 'active' );
				jQuery( "#reviewedemail-setting .reviewedemail_loader" ).addClass( 'loader' );
			},
			complete: function(response) {
				jQuery( "#reviewedemail-setting .loader_cover" ).removeClass( 'active' );
				jQuery( "#reviewedemail-setting .reviewedemail_loader" ).removeClass( 'loader' );
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
"#reviewreminderemail-setting #test_reviewreminderemail_btn",
function() {
	event.preventDefault();
	var nonce = jQuery( this ).attr( 'attr-nonce' );
	var data  = {
		action: 'email_make_test',
		nonce: nonce,
		option_name: 'reviewreminderemail',
	};
	jQuery.ajax(
	{
		type: "post",
		url: ajaxurl,
		data: data,
		beforeSend: function(response) {
			jQuery( "#reviewreminderemail-setting .loader_cover" ).addClass( 'active' );
			jQuery( "#reviewreminderemail-setting .reviewreminderemail_loader" ).addClass( 'loader' );
		},
		complete: function(response) {
			jQuery( "#reviewreminderemail-setting .loader_cover" ).removeClass( 'active' );
			jQuery( "#reviewreminderemail-setting .reviewreminderemail_loader" ).removeClass( 'loader' );
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
"#expirationreminderemail-setting #test_expirationreminderemail_btn",
function() {
	event.preventDefault();
	var nonce = jQuery( this ).attr( 'attr-nonce' );
	var data  = {
		action: 'email_make_test',
		nonce: nonce,
		option_name: 'expirationreminderemail',
	};
	jQuery.ajax(
	{
		type: "post",
		url: ajaxurl,
		data: data,
		beforeSend: function(response) {
			jQuery( "#expirationreminderemail-setting .loader_cover" ).addClass( 'active' );
			jQuery( "#expirationreminderemail-setting .reviewreminderemail_loader" ).addClass( 'loader' );
		},
		complete: function(response) {
			jQuery( "#expirationreminderemail-setting .loader_cover" ).removeClass( 'active' );
			jQuery( "#expirationreminderemail-setting .expirationreminderemail_loader" ).removeClass( 'loader' );
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
"#birthdayemail-setting #test_birthdayemail_btn",
function() {
	event.preventDefault();
	var nonce = jQuery( this ).attr( 'attr-nonce' );
	var data  = {
		action: 'email_make_test',
		nonce: nonce,
		option_name: 'birthdayemail',
	};
	jQuery.ajax(
	{
		type: "post",
		url: ajaxurl,
		data: data,
		beforeSend: function(response) {
			jQuery( "#birthdayemail-setting .loader_cover" ).addClass( 'active' );
			jQuery( "#birthdayemail-setting .birthdays_loader" ).addClass( 'loader' );
		},
		complete: function(response) {
			jQuery( "#birthdayemail-setting .loader_cover" ).removeClass( 'active' );
			jQuery( "#birthdayemail-setting .birthdays_loader" ).removeClass( 'loader' );
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
		action: 'email_make_test',
		nonce: nonce,
		option_name: 'reorderemail',
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
		action: 'email_make_test',
		nonce: nonce,
		option_name: 'onetimeemail',
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
"#onetimeemail-setting #send_onetime_btn",
function() {
	event.preventDefault();
	var nonce = jQuery( this ).attr( 'attr-nonce' );
	var data  = {
		action: 'onetimeemails_send',
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
		action: 'email_make_test',
		nonce: nonce,
		option_name: 'afterorderemail',
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

	
	
