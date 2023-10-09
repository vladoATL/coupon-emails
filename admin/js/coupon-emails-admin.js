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
		$("#clear_log_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'','couponemails_clear_log');
		});
	});
	
	$(document).ready(function() {
		$("#restore_namedayemail_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'namedayemail','heureka_restore_settings');
		});
	});

	$(document).ready(function() {
		$("#restore_referralconfirmationemail_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'referralconfirmationemail','heureka_restore_settings');
		});
	});

	$(document).ready(function() {
		$("#restore_referralemail_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'referralemail','heureka_restore_settings');
		});
	});
	
	$(document).ready(function() {
		$("#restore_reviewed_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'reviewedemail','heureka_restore_settings');
		});
	});
	
	
	$(document).ready(function() {
		$("#restore_onetimeemail_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'onetimeemail','heureka_restore_settings');
		});
	});
	
	$(document).ready(function() {
		$("#restore_reorder_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'reorderemail','heureka_restore_settings');
		});
	});
	
	$(document).ready(function() {
		$("#restore_reviewreminderemail_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'reviewreminderemail','heureka_restore_settings');
		});
	});
	
	$(document).ready(function() {
		$("#restore_expirationreminderemail_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'expirationreminderemail','heureka_restore_settings');
		});
	});
	
	$(document).ready(function() {
		$("#restore_afterorder_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'afterorderemail','heureka_restore_settings');
		});
	});
	
	$(document).ready(function() {
		$("#restore_birthdayemail_values_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'birthdayemail','heureka_restore_settings');
		});
	});	
	
	
	
	$(document).ready(function() {
		$("#test_namedayemail_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'namedayemail', 'email_make_test');
		});
	});		

	$(document).ready(function() {
		$("#test_referralemail_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'referralemail', 'email_make_test');
		});
	});	
	
	$(document).ready(function() {
		$("#test_referralconfirmationemail_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'referralconfirmationemail', 'email_make_test');
		});
	});	
	
	$(document).ready(function() {
		$("#test_reviewedemail_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'reviewedemail', 'email_make_test');
		});
	});	
	
	$(document).ready(function() {
		$("#test_reviewreminderemail_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'reviewreminderemail', 'email_make_test');
		});
	});	
	
	$(document).ready(function() {
		$("#test_expirationreminderemail_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'expirationreminderemail', 'email_make_test');
		});
	});	
	
	$(document).ready(function() {
		$("#test_birthdayemail_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'birthdayemail', 'email_make_test');
		});
	});	
	
	$(document).ready(function() {
		$("#test_reorder_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'reorderemail', 'email_make_test');
		});
	});					
	
	$(document).ready(function() {
		$("#test_onetime_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'onetimeemail', 'email_make_test');
		});
	});
	
	$(document).ready(function() {
		$("#test_afterorder_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'afterorderemail', 'email_make_test');
		});
	});	
		
		
	$(document).ready(function() {
		$("#send_onetime_btn").click(function(e) {
			e.preventDefault();
			sendCouponEmailAjaxRequest($(this),'', 'onetimeemails_send');
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
