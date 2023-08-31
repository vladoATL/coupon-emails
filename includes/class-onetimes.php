<?php
namespace COUPONEMAILS;

class Onetimes
{

	public function get_users_filtered($as_objects = false)
	{
		$sql = new PrepareSQL('onetimeemail', '=');
		return $sql->get_users_filtered();		
	}
}
?>