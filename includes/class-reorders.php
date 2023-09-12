<?php
namespace COUPONEMAILS;

class Reorders
{
	
	public function get_users_reorders($as_objects = false)
	{
		$sql = new PrepareSQL('reorderemail', '=');
		return $sql->get_users_filtered($as_objects);
	}
	
	function reorderemail_event_setup()
	{
		$options = get_option('reorderemail_options');
		$success = false;
		if ( !empty($options['enabled']) && '1' == $options['enabled'] ) {
			$funcs = new EmailFunctions('reorderemail');
			$users = $this->get_users_reorders(true);
			$i = 0;
			foreach ($users as $user) {
				$i = $i +1 ;
				if ($i>100) break;
				$success = $funcs->couponemails_create($user);				
			}
			$funcs->couponemails_delete_expired();
		}
	}	
}
?>