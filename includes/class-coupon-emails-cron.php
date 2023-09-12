<?php

add_action( 'namedayemail_cron', 'namedayemail_event' );
add_action( 'birthdayemail_cron', 'birthdayemail_event' );
add_action( 'reorderemail_cron', 'reorderemail_event' );
add_action( 'expirationreminderemail_cron', 'expirationreminderemail_event' );
add_action( 'reviewreminderemail_cron', 'reviewreminderemail_event' );

function reviewreminderemail_event()
{
	$reminders =  new \COUPONEMAILS\AfterOrder('reviewreminderemail');
	$reminders->afterorderemail_event_setup();
}

function reviewreminderemail_run_cron()
{
	couponemails_run_cron_setup("reviewreminderemail");
}

function expirationreminderemail_event()
{
	$reminders =  new \COUPONEMAILS\ExpirationReminder();
	$reminders->expirationreminderemail_event_setup();
}

function expirationreminderemail_run_cron()
{
	couponemails_run_cron_setup("expirationreminderemail");
}


function namedayemail_event(){
	$celebrating =  new \COUPONEMAILS\Namedays();
	$celebrating->namedayemail_event_setup();	
}

function namedayemail_run_cron() {
	couponemails_run_cron_setup("namedayemail");
}

function birthdayemail_event()
{
	$celebrating =  new \COUPONEMAILS\Birthdays();
	$celebrating->birthdayemail_event_setup();
}

function birthdayemail_run_cron() {
	couponemails_run_cron_setup("birthdayemail");
}

function reorderemail_event()
{
	$coupons =  new \COUPONEMAILS\Reorders();
	$coupons->reorderemail_event_setup();
}

function reorderemail_run_cron()
{
	couponemails_run_cron_setup("reorderemail");
}

function couponemails_run_cron_setup($type)
{
	$options = get_option($type . '_options');
	$logs = new \COUPONEMAILS\EmailFunctions($type);

	if (isset($options['enabled'])) {
		wp_clear_scheduled_hook($type . '_cron' );
		if (isset($options['send_time'])) {
			$tm = strtotime(time_utc($options['send_time']));
		} else {
			$tm = time();
		}		

		$res = wp_reschedule_event( $tm, 'daily', $type . '_cron' );
		if ($res == 1 )
			$logs->couponemails_add_log($type . _x("_cron scheduled", "Log file", "coupon-emails") . " " . date("T H:i", $tm));
			else
				$logs->couponemails_add_log(_x("Cron scheduling error" , "Log file", "coupon-emails") );			
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