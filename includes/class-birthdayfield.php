<?php
namespace COUPONEMAILS;

// don't call the file directly
if( !defined( 'ABSPATH' ) ) exit();

/** Add a user birthday field. */
class BirthdayField
{
	static function register()
	{
		$options = get_option('birthdayemail_options');
		$display_fields = isset($options['display_dob_fields']) ? $options['display_dob_fields'] : "";
		
		$registration = \get_option('lws_woorewards_registration_birthday_field');
		$detail       = \get_option('lws_woorewards_myaccount_detail_birthday_field');
		$myaccount    = \get_option('lws_woorewards_myaccount_register_birthday_field');
		
		$me = new self();

		if (! $registration && $display_fields ) {
			\add_filter('woocommerce_checkout_fields', array($me, 'checkout'));
		}
		if (! $detail && $display_fields ) {
			\add_action('woocommerce_edit_account_form', array($me, 'myaccountDetailForm'));
			\add_action('woocommerce_save_account_details', array($me, 'myaccountDetailSave'));
		}
		if (! $myaccount && $display_fields ) {
			\add_action('woocommerce_register_form', array($me, 'myaccountRegisterForm'));
			\add_filter('woocommerce_process_registration_errors', array($me, 'myaccountRegisterValidation'), 10, 4);
			\add_action('woocommerce_created_customer', array($me, 'myaccountRegisterSave'), 10, 1);
		}
	
		if ( ! $registration && ! $detail && ! $myaccount && $display_fields ) {
			\add_action('show_user_profile', array($me, 'showProfileBirthday'));
			\add_action('edit_user_profile', array($me, 'showProfileBirthday'));
		}
		
		\add_action('personal_options_update', array($me, 'saveProfileBirthday'));
		\add_action('edit_user_profile_update', array($me, 'saveProfileBirthday'));
		
	}
	
	protected function getDefaultBirthdayMetaKey()
	{
		return 'billing_birth_date';
	}

	function checkout($fields)
	{
		$fields['billing'][$this->getDefaultBirthdayMetaKey()] = array(
			'type'        => 'date',
			'label'       => _x("Date of birth - Year is not important but helps","Check out", "coupon-emails"),
			'required'    => false
		);
		return $fields;
	}

	function myaccountRegisterForm()
	{
		$field = $this->getDefaultBirthdayMetaKey();
		$label = _x("Date of birth","Registration", "coupon-emails");
		$legend = _x("Year is not important but helps","DOB note", "coupon-emails");

		echo "<p class='woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide'>";
		echo "<label for='{$field}'>$label</label>";
		echo "<input type='date' class='woocommerce-Input woocommerce-Input--text input-text' name='{$field}' id='{$field}' />";
		echo "<div>{$legend}</div>";
		echo "</p>";
	}

	function myaccountRegisterValidation($validation_error, $username, $password, $email)
	{
		$birthday = $this->grabBirthdayFromPost();
		if( false === $birthday ){
			$field = $this->getDefaultBirthdayMetaKey();
			$validation_error->add($field, __("Invalid date format for date of birth", "coupon-emails"), 'birthday');
		}
		return $validation_error;
	}

	function myaccountRegisterSave($userId)
	{
		$birthday = $this->grabBirthdayFromPost();
		\update_user_meta($userId, $this->getDefaultBirthdayMetaKey(), $birthday);
	}

	function myaccountDetailForm()
	{
		$userId = \get_current_user_id();
		$field = $this->getDefaultBirthdayMetaKey();
		$label = _x("Date of birth","My account", "coupon-emails");
		$legend = _x("Year is not important but helps","DOB note", "coupon-emails");
		$value = \esc_attr(\get_user_meta($userId, $field, true));

		echo "<fieldset>";
		echo "<legend>" . $label . "</legend>";
		echo "<p class='woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide'>";
		echo "<label for='{$field}'>{$legend}</label>";
		echo "<input type='date' class='woocommerce-Input woocommerce-Input--text input-text' name='{$field}' id='{$field}' value='{$value}' />";
		echo "</fieldset>";
		echo "</p><div class='clear'></div>";
	}

	function myaccountDetailSave($userId)
	{
		$birthday = $this->grabBirthdayFromPost();
		if( $birthday !== false )
			\update_user_meta($userId, $this->getDefaultBirthdayMetaKey(), $birthday);
		else
			\wc_add_notice(__("Invalid date format for date of birth", "coupon-emails"), 'error');
	}

	function grabBirthdayFromPost()
	{
		$field = $this->getDefaultBirthdayMetaKey();
		$birthday = !empty($_POST[$field]) ? \wc_clean($_POST[$field]): '';
		if( !empty($birthday) )
		{
			$date = \date_create($birthday);
			if (empty($date)) {
				\wc_add_notice(__("Invalid date format for date of birth", "coupon-emails"), 'error');
				$birthday = false;
			}
/*			$today = \date_create();
			if ($date > $today) {
				\wc_add_notice(__("Enter your date of birth, not your next birthday", "coupon-emails"), 'error');
				$birthday = false;
			}*/
		}
		return $birthday;
	}

	function showProfileBirthday($user)
	{
		$field = $this->getDefaultBirthdayMetaKey();
		$label = _x("Date of birth", "Profile", "coupon-emails");
		$header = _x("Date of birth", "Profile", "coupon-emails");
		$value = \esc_attr(\get_user_meta($user->ID, $field, true));
		echo <<<EOT
<table class="form-table">
	<tr>
	<th><h2><label
	for
		='{$field}'>{$label}</label></h2></th>
		<td><input type='date' name='{$field}' id='{$field}' value='{$value}' /></td>
	</tr>
</table>
EOT;

	}

	function saveProfileBirthday($userId)
	{
		if ( !current_user_can( 'edit_user', $userId ) ) {
			return false;
		}
		$field = $this->getDefaultBirthdayMetaKey();
		$date = \sanitize_text_field($_POST[$field]);
		\update_user_meta( $userId, $field, $date);
	}

}
