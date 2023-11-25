<?php
namespace COUPONEMAILS;

class Coupon_Emails_Onetimes
{

	public function send_to_users_filtered( $option_name = 'couponemails_onetimeemail' , $as_objects = false)
	{
		$result = new Coupon_Emails_PrepareSQL($option_name, '<=');
		$users = $result->get_users_filtered(true);
		$options = get_option($option_name . '_options');
		$istest = isset($options['test']) ? $options['test'] : 0;
		$funcs = new Coupon_Emails_EmailFunctions($option_name);
		//return $users;
		Coupon_Emails_EmailFunctions::test_add_log('-- send_to_users_filtered -- ' . $option_name . PHP_EOL );
		$i = 0;
		foreach ($users as $user) {
			if (isset($user)) {
				$funcs->couponemails_create($user, $istest);
				$i = $i + 1;
				if ( $istest && $i >= COUPON_EMAILS_MAX_TEST_EMAILS) {
					break;
				}
			}
		}
		if (! $istest)
			$funcs->couponemails_delete_expired();		
	}		
}
?>