<?php
namespace COUPONEMAILS;

class AfterOrder
{
	
	public function get_users_afterorder($as_objects = false)
	{
		$sql = new PrepareSQL('afterorderemail', '=');
		return $sql->get_users_filtered();
	}
	
	function afterorderemail_event_setup()
	{
		$options = get_option('afterorderemail_options');
		$success = false;
		if ( !empty($options['enabled']) && '1' == $options['enabled'] ) {
			$funcs = new EmailFunctions('afterorderemail');
			$users = $this->get_users_reorders(true);

			foreach ($users as $user) {
				$success = $funcs->couponemails_create($user);
			}
			$funcs->couponemails_delete_expired();
		}
	}
	}
?>