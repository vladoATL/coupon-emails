<?php
namespace COUPONEMAILS;

class AfterOrder
{
	protected $type;
	
	public function __construct($type)
	{
		$this->type = $type;
	}

	public function get_users_afterorder($as_objects = false)
	{
		$sql = new PrepareSQL( $this->type , '=');
		return $sql->get_users_filtered($as_objects);
	}

	function afterorderemail_event_setup()
	{
		$options = get_option($this->type . '_options');
		$istest = isset($options['test']) ? $options['test'] : 0;

		if ( (!empty($options['enabled']) && '1' == $options['enabled']) || $istest ) {

			$funcs = new EmailFunctions($this->type);
			$users = $this->get_users_afterorder(true);

			foreach ($users as $user) {
				
				if (!empty($user->user_firstname)) {					
					$funcs->couponemails_create($user, $istest);
					$i = $i + 1;
					if ( $istest && $i >= MAX_TEST_EMAILS) {
						break;
					}
				}
			}
			if (! $istest)
				$funcs->couponemails_delete_expired();
		}
	}
}
?>