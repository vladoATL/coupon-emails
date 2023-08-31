<?php
namespace COUPONEMAILS;

class Birthdays
{
	public function get_celebrating_users($d, $m)
	{
		global $wpdb;

		$date_str = sprintf("%02d", $m) . "-" . sprintf("%02d", $d);
		$sql = "	
		SELECT m.user_id AS user_id, m.meta_value as dob, u.user_email AS user_email, fmu.meta_value AS user_firstname, lmu.meta_value AS user_lastname,
		TIMESTAMPDIFF(YEAR, m.meta_value, CURDATE()) AS age, dmu.meta_value AS sent
		FROM {$wpdb->prefix}users  AS u
		JOIN {$wpdb->prefix}usermeta AS m ON u.ID = m.user_id AND m.meta_key = 'billing_birth_date'
		JOIN {$wpdb->prefix}usermeta AS fmu ON u.ID = fmu.user_id AND fmu.meta_key = 'billing_first_name'
		JOIN {$wpdb->prefix}usermeta AS lmu ON u.ID = lmu.user_id AND lmu.meta_key = 'billing_last_name'
		LEFT JOIN {$wpdb->prefix}usermeta AS dmu ON u.ID = dmu.user_id AND dmu.meta_key = 'dob-coupon-sent'
		WHERE m.meta_value LIKE '%-{$date_str}'";		
					
		$result = $wpdb->get_results($sql, OBJECT);

		return $result;
	}
	
	public function get_users_dob_list()
	{
		global $wpdb;	
	
		$sql = "SELECT  SUBSTRING(m.meta_value FROM 6)  as day, fmu.meta_value AS fname, lmu.meta_value AS lname, u.user_email AS email, m.user_id AS id,
				TIMESTAMPDIFF(YEAR, m.meta_value, CURDATE()) AS age, dmu.meta_value AS sent
				FROM $wpdb->users  AS u
				JOIN $wpdb->usermeta AS m ON u.ID = m.user_id AND m.meta_key = 'billing_birth_date'
				JOIN $wpdb->usermeta AS fmu ON u.ID = fmu.user_id AND fmu.meta_key = 'billing_first_name'
				JOIN $wpdb->usermeta AS lmu ON u.ID = lmu.user_id AND lmu.meta_key = 'billing_last_name'
				LEFT JOIN $wpdb->usermeta AS dmu ON u.ID = dmu.user_id AND dmu.meta_key = 'dob-coupon-sent'
				WHERE TIMESTAMPDIFF(YEAR, m.meta_value, CURDATE()) != ''
				ORDER BY SUBSTRING(m.meta_value FROM 6) ";

		$result = $wpdb->get_results($sql, ARRAY_A);
	
		return $result;
	}
	
	function birthdayemail_event_setup()
	{
		$options = get_option('birthdayemail_options');
		$success = false;
		if ( !empty($options['enabled']) && '1' == $options['enabled'] ) {
			$str_nameday =  date('Y-m-d',strtotime('+' . $options['days_before'] . ' day'));
			$once_year = $options['once_year'];
			$dateValue = strtotime($str_nameday);
			$m = intval(date("m", $dateValue));
			$d = intval(date("d", $dateValue));
			$funcs = new EmailFunctions('birthdayemail');
			
			$users = $this->get_celebrating_users($d,$m);
			foreach ($users as $user) {
				$runit = false;
				if ($once_year == 1) {
					$check = get_user_meta( $user->user_id, $this->getBirthdaySentMetaKey(), true );
					if ( empty( $check )  || $check != date("Y") )
						$runit = true;
				} else {
					$runit = true;
				}
				if ( $runit) {
					$success = $funcs->couponemails_create($user);
					if ( $success) {
						$this->couponemails_set_sent($user);
					} else {
						$funcs->couponemails_add_log("Birth day coupon flag has not been updated for" . " " . $user->user_email);
					}
				} else {
					$funcs->couponemails_add_log("User has already received birth day coupon this year" . " " . $user->user_email);
				}
			}
			$funcs->couponemails_delete_expired();
		}
	}	
	
	function getBirthdaySentMetaKey()
	{
		return 'dob-coupon-sent';
	}

	function couponemails_set_sent($user, $istest = false)
	{
		if (! $istest) {
			$id = $user->user_id;

			$check = get_user_meta( $id, $this->getBirthdaySentMetaKey(), true );
			if ( empty( $check )) {
				add_user_meta($id, $this->getBirthdaySentMetaKey(), date('Y'));
			} else {
				update_user_meta($id, $this->getBirthdaySentMetaKey(), date('Y'));
			}
		}
	}	
}
?>