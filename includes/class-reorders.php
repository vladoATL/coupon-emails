<?php
namespace COUPONEMAILS;

class Coupon_Emails_Reorders
{
	
	public function get_users_reorders($as_objects = false)
	{
		$sql = new Coupon_Emails_PrepareSQL('couponemails_reorder', '=');
		return $sql->get_users_filtered($as_objects);
	}
	
	function couponemails_reorder_event_setup()
	{
		$options = get_option('couponemails_reorder_options');
		$success = false;
		if ( !empty($options['enabled']) && '1' == $options['enabled'] ) {
			$funcs = new Coupon_Emails_EmailFunctions('couponemails_reorder');
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