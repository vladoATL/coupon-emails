<?php
namespace COUPONEMAILS;

class Coupon_Emails_ReviewReminder
{
	
	public function get_reviews($as_objects = false)
	{
		$sql = new Coupon_Emails_PrepareSQL('couponemails_reviewreminder', '=');
		return $sql->get_users_filtered($as_objects);
	}
	
	function couponemails_reviewreminder_event_setup()
	{
		$options = get_option('couponemails_reviewreminder_options');
		$istest = isset($options['test']) ? $options['test'] : 0;

		if ( (!empty($options['enabled']) && '1' == $options['enabled']) || $istest ) {
			$funcs = new Coupon_Emails_EmailFunctions('couponemails_reviewreminder');
			$i = 0;
			$users = $this->get_reviews(true);
			foreach ($users as $user) {
				if (isset($user)) {					
					$i = $i + 1;
					if ( $istest && $i > COUPON_EMAILS_MAX_TEST_EMAILS) {
						$funcs->couponemails_add_log(sprintf( esc_html_x( "An email was created but not sent to %s because the number of test emails exceeded", "Log file", "coupon-emails" ), $user->user_email ) . " " . COUPON_EMAILS_MAX_TEST_EMAILS);						
					} else {
						$funcs->couponemails_create($user, $istest);						
					}					
				}
			}
			if (! $istest)
				$funcs->couponemails_delete_expired();
		}
	}	
}
?>