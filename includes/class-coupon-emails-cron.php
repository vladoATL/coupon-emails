<?php

add_action( 'couponemails_nameday_cron', 'couponemails_nameday_event' );
add_action( 'couponemails_birthday_cron', 'couponemails_birthday_event' );
add_action( 'couponemails_reorder_cron', 'couponemails_reorder_event' );
add_action( 'couponemails_expirationreminder_cron', 'couponemails_expirationreminder_event' );
add_action( 'couponemails_reviewreminder_cron', 'couponemails_reviewreminder_event' );

function couponemails_reviewreminder_event()
{
	$reminders =  new \COUPONEMAILS\Coupon_Emails_AfterOrder('couponemails_reviewreminder');
	$reminders->afterorderemail_event_setup();
}

function couponemails_reviewreminder_run_cron()
{
	couponemails_run_cron_setup("couponemails_reviewreminder");
}

function couponemails_expirationreminder_event()
{
	$reminders =  new \COUPONEMAILS\Coupon_Emails_ExpirationReminder();
	$reminders->expirationreminderemail_event_setup();
}

function couponemails_expirationreminder_run_cron()
{
	couponemails_run_cron_setup("couponemails_expirationreminder");
}


function couponemails_nameday_event()
{
	$celebrating =  new \COUPONEMAILS\Coupon_Emails_Namedays();
	$celebrating->namedayemail_event_setup();	
}

function couponemails_nameday_run_cron()
{
	couponemails_run_cron_setup("couponemails_nameday");
}

function couponemails_birthday_event()
{
	$celebrating =  new \COUPONEMAILS\Coupon_Emails_Birthdays();
	$celebrating->birthdayemail_event_setup();
}

function couponemails_birthday_run_cron()
{
	couponemails_run_cron_setup("couponemails_birthday");
}

function couponemails_reorder_event()
{
	$coupons =  new \COUPONEMAILS\Coupon_Emails_Reorders();
	$coupons->couponemails_reorder_event_setup();
}

function couponemails_reorder_run_cron()
{
	couponemails_run_cron_setup("couponemails_birthday");
}

function couponemails_run_cron_setup($type)
{
	$options = get_option($type . '_options');
	$logs = new \COUPONEMAILS\Coupon_Emails_EmailFunctions($type);

	if (isset($options['enabled'])) {
		wp_clear_scheduled_hook($type . '_cron' );
		if (isset($options['send_time'])) {
			$tm = strtotime(time_utc($options['send_time']));
		} else {
			$tm = time();
		}		

		$res = wp_reschedule_event( $tm, 'daily', $type . '_cron' );
		if ($res == 1 )
			$logs->couponemails_add_log($type . esc_html_x("_cron scheduled", "Log file", "coupon-emails") . " " . date("T H:i", $tm));
			else
				$logs->couponemails_add_log(esc_html_x("Cron scheduling error" , "Log file", "coupon-emails") );			
	}	
}

function time_utc($dateTime)
{
	$timezone_from = wp_timezone_string();
	$newDateTime = new \DateTime($dateTime, new \DateTimeZone($timezone_from));
	if (!$newDateTime instanceof DateTime)
		return "";
	$newDateTime->setTimezone(new DateTimeZone("UTC"));
	$dateTimeUTC = $newDateTime->format("H:i");
	return $dateTimeUTC;
}

?>