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
*
*/
	function addLoaderClass()
	{
		jQuery( ".coupon_emails_loader_cover" ).addClass( 'active' );
		jQuery( ".coupon_emails_loader" ).addClass( 'loader' );
	}

	function removeLoaderClass()
	{
		jQuery( ".coupon_emails_loader_cover" ).removeClass( 'active' );
		jQuery( ".coupon_emails_loader" ).removeClass( 'loader' );
	}

	function sendCouponEmailAjaxRequest(element, option, action)
	{
		var clickedButton = element;
		var nonce = jQuery( clickedButton ).attr( 'attr-nonce' );
		var data  = {
			action: action,
			nonce: nonce,
			option_name: option ,
		};
		jQuery.ajax(
		{
			type: "post",
			url: ajaxurl,
			data: data,
			beforeSend: function(response) {
				addLoaderClass();
			},
			complete: function(response) {
				removeLoaderClass();
			},
			success: function(response) {
				location.reload();
			}
		}
		);
	}

	$(document).ready(function() {
		$("#couponemails_clear_log_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'','couponemails_clear_log');
		});
	});
	
	$(document).ready(function() {
		$("#restore_couponemails_nameday_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_nameday','email_restore_settings');
		});
	});

	$(document).ready(function() {
		$("#restore_couponemails_referralconfirmation_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_referralconfirmation','email_restore_settings');
		});
	});

	$(document).ready(function() {
		$("#restore_couponemails_referralemail_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_referralemail','email_restore_settings');
		});
	});
	
	$(document).ready(function() {
		$("#restore_couponemails_reviewed_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_reviewed','email_restore_settings');
		});
	});
	
	
	$(document).ready(function() {
		$("#restore_couponemails_onetimeemail_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_onetimeemail','email_restore_settings');
		});
	});
	
	$(document).ready(function() {
		$("#restore_couponemails_onetimecoupon_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_onetimecoupon','email_restore_settings');
		});
	});
		
	$(document).ready(function() {
		$("#restore_couponemails_reorder_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_reorder','email_restore_settings');
		});
	});
	
	$(document).ready(function() {
		$("#restore_couponemails_reviewreminder_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_reviewreminder','email_restore_settings');
		});
	});
	
	$(document).ready(function() {
		$("#restore_couponemails_expirationreminder_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_expirationreminder','email_restore_settings');
		});
	});
	
	$(document).ready(function() {
		$("#restore_couponemails_afterorder_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_afterorder','email_restore_settings');
		});
	});
	
	$(document).ready(function() {
		$("#restore_couponemails_birthday_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_birthday','email_restore_settings');
		});
	});	
		
	
	$(document).ready(function() {
		$("#test_couponemails_nameday_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_nameday', 'email_make_test');
		});
	});		

	$(document).ready(function() {
		$("#test_couponemails_referralconfirmation_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_referralconfirmation', 'email_make_test');
		});
	});	
	
	$(document).ready(function() {
		$("#test_couponemails_referralemail_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_referralemail', 'email_make_test');
		});
	});	
	
	$(document).ready(function() {
		$("#test_couponemails_reviewed_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_reviewed', 'email_make_test');
		});
	});	
	
	$(document).ready(function() {
		$("#test_couponemails_reviewreminder_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_reviewreminder', 'email_make_test');
		});
	});	
	
	$(document).ready(function() {
		$("#test_couponemails_expirationreminder_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_expirationreminder', 'email_make_test');
		});
	});	
	
	$(document).ready(function() {
		$("#test_couponemails_birthday_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_birthday', 'email_make_test');
		});
	});	
	
	$(document).ready(function() {
		$("#test_couponemails_reorder_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_reorder', 'email_make_test');
		});
	});					
	
	$(document).ready(function() {
		$("#test_couponemails_onetimeemail_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_onetimeemail', 'email_make_test');
		});
	});
	
	$(document).ready(function() {
		$("#test_couponemails_onetimecoupon_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_onetimecoupon', 'email_make_test');
		});
	});
		
	$(document).ready(function() {
		$("#test_couponemails_afterorder_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_afterorder', 'email_make_test');
		});
	});	
		
		
	$(document).ready(function() {
		$("#send_couponemails_onetimecoupon_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_onetimecoupon', 'onetimeemail_send');
		});
	});			
		
	$(document).ready(function() {
		$("#send_couponemails_onetimeemail_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'couponemails_onetimeemail', 'onetimeemail_send');
		});
	});		
		
		

	
})( jQuery );

jQuery( function( $ ) {
	$('.woocommerce-help-tip').tipTip({
		'attribute': 'data-tip',
		'fadeIn':    50,
		'fadeOut':   50,
		'delay':     200
	});

});

jQuery( function( $ ) {
	const enabled_hidden = document.querySelector('input[id="test_enabled"]');
	const runNowButton = document.getElementById('run_button');
	const sendButton = document.getElementById('send_btn');
	enabled_hidden.addEventListener('change', checkButtonStatus);

	function checkButtonStatus()
	{
		const allChecked = enabled_hidden.checked ;
		runNowButton.disabled = !allChecked;
		if ( $(sendButton).length )
		{
			sendButton.disabled = allChecked;
		}		
	}
	checkButtonStatus();
});
