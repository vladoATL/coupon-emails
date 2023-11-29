<?php
namespace COUPONEMAILS;

class Coupon_Emails_Birthdays
{
	public function get_celebrating_users($d, $m)
	{
		global $wpdb;

		$date_str = sprintf("%02d", $m) . "-" . sprintf("%02d", $d);
		$sql = "	
		SELECT m.user_id AS id, fmu.meta_value AS user_firstname, lmu.meta_value AS user_lastname, u.user_email AS user_email, m.meta_value as dob,
		TIMESTAMPDIFF(YEAR, m.meta_value, CURDATE()) AS age, dmu.meta_value AS sent
		FROM {$wpdb->prefix}users  AS u
		JOIN {$wpdb->prefix}usermeta AS m ON u.ID = m.user_id AND m.meta_key = 'billing_birth_date'
		JOIN {$wpdb->prefix}usermeta AS fmu ON u.ID = fmu.user_id AND fmu.meta_key = 'billing_first_name'
		JOIN {$wpdb->prefix}usermeta AS lmu ON u.ID = lmu.user_id AND lmu.meta_key = 'billing_last_name'
		LEFT JOIN {$wpdb->prefix}usermeta AS dmu ON u.ID = dmu.user_id AND dmu.meta_key = 'dob-coupon-sent'
		WHERE m.meta_value LIKE '%-{$date_str}'";		
		Coupon_Emails_EmailFunctions::test_add_log('-- get_celebrating_users - Coupon_Emails_Birthdays -- ' . PHP_EOL  . $sql);			
		$result = $wpdb->get_results($sql, OBJECT);

		return $result;
	}
	
	public function get_users_dob_list()
	{
		global $wpdb;	
	
		$sql = "SELECT  DATE_FORMAT( m.meta_value, '%e.%c')  as day, fmu.meta_value AS fname, lmu.meta_value AS lname, u.user_email AS email, m.user_id AS id,
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
		$istest = isset($options['test']) ? $options['test'] : 0;
		$success = false;
		if (( !empty($options['enabled']) && '1' == $options['enabled']) || $istest ) {
			$str_nameday =  date('Y-m-d',strtotime('+' . $options['days_before'] . ' day'));
			if ($istest) {
				$once_year = 0;
			} else {
				$once_year = $options['once_year'];
			}
			$dateValue = strtotime($str_nameday);
			$m = intval(date("m", $dateValue));
			$d = intval(date("d", $dateValue));
			$funcs = new Coupon_Emails_EmailFunctions('birthdayemail');
			$i = 0;
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

					$i = $i + 1;
					if ( $istest && $i > COUPON_EMAILS_MAX_TEST_EMAILS) {
						$funcs->couponemails_add_log(sprintf( esc_html_x( "An email was created but not sent to %s because the number of test emails exceeded", "Log file", "coupon-emails" ), $user->user_email ) . " " . COUPON_EMAILS_MAX_TEST_EMAILS);
						$success = false;
					} else {
						$success = $funcs->couponemails_create($user, $istest);
					}
					if ( $success) {
						$this->couponemails_set_sent($user);
					} else {
						$funcs->couponemails_add_log(esc_html_x("Birth day coupon flag has not been updated for", "Log file", "coupon-emails")  . " " . $user->user_email);
					}
				} else {
					$funcs->couponemails_add_log(esc_html_x("This user has already received birthday coupon this year", "Log file", "coupon-emails")  . " " . $user->user_email);
				}
			}
			if (! $istest) 
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
			$id = $user->id;

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