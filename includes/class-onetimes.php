<?php
namespace COUPONEMAILS;

class Onetimes
{

	public function send_to_users_filtered($as_objects = false)
	{
		$option_name = 'onetimeemail';
		$result = new PrepareSQL($option_name, '=');
		$users = $result->get_users_filtered(true);
		$options = get_option($option_name . '_options');
		$istest = isset($options['test']) ? $options['test'] : 0;
		$funcs = new EmailFunctions('onetimeemail');
		//return $users;

		$i = 0;
		foreach ($users as $user) {
			if (isset($user)) {
				$funcs->couponemails_create($user, $istest);
				$i = $i + 1;
				if ( $istest && $i >= MAX_TEST_EMAILS) {
					break;
				}
			}
			if (! $istest)
				$funcs->couponemails_delete_expired();
		}
	}		
}
?>