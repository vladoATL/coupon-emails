<?php
namespace COUPONEMAILS;

class Reorders
{
	
	public function get_users_reorders($as_objects = false)
	{
		$sql = new PrepareSQL('reorderemail');
		return $sql->get_users_filtered($as_objects);
	}
	
	function reorderemail_event_setup()
	{
		$options = get_option('reorderemail_options');
		$success = false;
		if ( !empty($options['enabled']) && '1' == $options['enabled'] ) {
			$funcs = new EmailFunctions('reorderemail');
			$users = $this->get_users_reorders(true);

			foreach ($users as $user) {				
				$success = $funcs->couponemails_create($user);				
			}
			$funcs->couponemails_delete_expired();
		}
	}	
}
?>