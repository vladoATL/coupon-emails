<?php
namespace COUPONEMAILS;
// Process export
$option_name = "couponemails_birthday";

if ( isset( $_GET['runtest'] ) ) {
	$bd = new \COUPONEMAILS\Coupon_Emails_Birthdays();
	$bd -> birthdayemail_event_setup();
	header("location:admin.php?page=couponemails&tab=birth-day"); 
}

// Process export
if ( isset( $_GET['birthdayexport'] ) ) {
	global $wpdb;
	$options = get_option($option_name . '_options');
	$birthday = new \COUPONEMAILS\Coupon_Emails_Birthdays();
	ob_end_clean();
	$table_head = array('User ID','First Name', 'Last Name', 'Email',  'DOB',  'Age', 'Last time sent' );
	$csv = implode( ';' , $table_head );
	$csv .= "\n";
	$days_before = isset($options['days_before']) ? $options['days_before'] : 0;
	$str_nameday =  date('Y-m-d',strtotime('+' . $days_before  . ' day'));
	$dateValue = strtotime($str_nameday);
	$m = intval(date("m", $dateValue));
	$d = intval(date("d", $dateValue));
	$result =  (array) $birthday ->get_celebrating_users($d,$m);
	
	$days_before = $days_before + 1;

	$str_nameday =  date('Y-m-d',strtotime('+' . $days_before . ' day'));
	$dateValue = strtotime($str_nameday);
	$m = intval(date("m", $dateValue));
	$d = intval(date("d", $dateValue));
	$result2 =  (array) $birthday ->get_celebrating_users($d,$m);
	
	$result_merge = array_merge($result,$result2);
	
	foreach ( $result_merge as $key => $value ) {
		$csv .=   implode(';', (array) $value);
		$csv .= "\n";
	}
	
	
	$csv .= "\n";
	$filename = $str_nameday . '_birthday.csv';
	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename="' . $filename .'"');
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	echo "\xEF\xBB\xBF"; // UTF-8 BOM
	echo $csv;
	exit();
}

if ( isset( $_GET['dobexport'] ) ) {
	global $wpdb;
	ob_end_clean();
	$table_head = array( 'Day', 'First Name', 'Last Name', 'Email', 'User ID', 'Age', 'Year Sent' );
	$csv = implode( ';' , $table_head );
	$csv .= "\n";
	
	$dobs = new \COUPONEMAILS\Coupon_Emails_Birthdays();
	$result = $dobs->get_users_dob_list();
					
	foreach ( $result as $key => $value ) {
		$csv .=   implode(';', $value);  
		$csv .= "\n";
	}
	$csv .= "\n";
	$filename = get_option( 'blogname' ) . '_birthdays.csv';
	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename="' . $filename .'"');
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	echo "\xEF\xBB\xBF"; // UTF-8 BOM
	echo $csv;
	exit();
}

couponemails_birthday_run_cron();

?>

<div class="wrap woocommerce">
<div id="birthdayemail-setting" class="coupon-emails-setting">
<div class="couponemails_loader_cover">
	<div class="couponemails_loader"></div> </div>
	<input type="button" value="<?php echo  esc_html__( 'Restore Defaults', 'coupon-emails' ); ?>" class="button button-primary btn-restore"
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' .  $option_name . '_nonce' ) ); ?>"
id="restore_<?php echo $option_name; ?>_values_btn" />

<h2><?php echo esc_html_x('Birthday Emails Settings','Setting', 'coupon-emails'); ?> </h2>
<h4><?php echo esc_html_x('In this section, coupon emails are automatically generated to users on their birthdays. If this option is enabled, a field for the date of birth will be displayed at checkout and in the My Account section.','Setting', 'coupon-emails'); ?> </h4>
<form method="post" id="form_<?php echo $option_name; ?>" name="form_<?php echo $option_name; ?>" action="options.php">
	<?php
	settings_fields($option_name . '_plugin_options');
	$options = get_option($option_name . '_options');
	?>
	<table class="form-table">
		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Enable auto sending emails', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="<?php echo $option_name; ?>_options[enabled]" id="<?php echo $option_name; ?>_options[enabled]"  value="1" <?php echo checked( 1, $options['enabled'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(esc_html__( 'Turn on and off the automatic functionality of email sending', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Run in test mode', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="<?php echo $option_name; ?>_options[test]" id="<?php echo $option_name; ?>_options[test]"  value="1" <?php echo checked( 1, $options['test'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(esc_html__( 'Turn on when testing. The user will not get emails. All emails will be sent to BCC/Test address.', 'coupon-emails' ), false); ?>
				<button type="button" class="button button-primary" id="run_button" onClick="window.location.search += '&runtest=1'"><?php echo esc_html__( 'Run now', 'coupon-emails' ); ?></button>
				<input type="checkbox" style="display: none;" name="test_enabled" id="test_enabled"  value="1" <?php echo checked( 1, $options['test'] ?? '', false ) ?? '' ; ?>>	<?php  echo wc_help_tip(sprintf(_n( 'If you want to run a test, check the chekbox and save. After pushing this button maximum %s coupon will be created and email sent to administrator.', 'If you want to run a test, check the chekbox and save. After pushing this button maximum %s coupons will be created and test emails sent to administrator.', COUPON_EMAILS_MAX_TEST_EMAILS, 'coupon-emails' ), number_format_i18n(COUPON_EMAILS_MAX_TEST_EMAILS)), false); ?>			
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Display birthday field to users', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="<?php echo $option_name; ?>_options[display_dob_fields]" id="<?php echo $option_name; ?>_options[display_dob_fields]"  value="1" <?php echo checked( 1, $options['display_dob_fields'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(esc_html__( 'Turn on and off displaying of the birthday field in My Account page and in Checkout', 'coupon-emails' ), false); ?>
			</td>
		</tr>		
		
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Send email X days before birthday', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="number" id="<?php echo $option_name; ?>_options[days_before]" name="<?php echo $option_name; ?>_options[days_before]"  style="width: 60px;" value="<?php echo $options['days_before'] ?? ''; ?>"</input>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Send email every day at', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="time" id="<?php echo $option_name; ?>_options[send_time]" name="<?php echo $option_name; ?>_options[send_time]"  style="width: 100px;" value="<?php echo $options['send_time'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( 'This is time when cron sends the email messages.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Send it onle one time in a year', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="<?php echo $option_name; ?>_options[once_year]" id="<?php echo $option_name; ?>_options[once_year]"  value="1" <?php echo checked( 1, $options['once_year'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(esc_html__( 'Send the email with coupon only once in a year to customer even though the DOB was changed.', 'coupon-emails' ), false); ?>
			</td>
		</tr>		
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Download file with birthdays', 'coupon-emails' ); ?>:</th>
			<td>
				<a class="button button-primary" href="admin.php?page=couponemails&tab=birth-day&dobexport=table&noheader=1"><?php echo esc_html__( 'Download csv', 'coupon-emails' ); ?></a>
				<?php  echo wc_help_tip(esc_html__( 'Download csv file with brithdays of users.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'List of users who receive the email today and tomorrow', 'coupon-emails' ); ?>:</th>
			<td>
				<a class="button button-primary" href="admin.php?page=couponemails&tab=birth-day&birthdayexport=table&noheader=1"><?php echo esc_html__( 'Download csv', 'coupon-emails' ); ?></a>
				<?php  echo wc_help_tip(esc_html__( "Download csv file with filtered users for today's and tomorrow's email.", 'coupon-emails' ), false); ?>
			</td>
		</tr>	
	</table>

<?php include('coupon-form.php'); ?>

</form>
<input type="button" value="<?php echo  esc_html__( 'Create a test', 'coupon-emails' ); ?>" class="button button-primary" attr-nonce="<?php echo esc_attr( wp_create_nonce( '_<?php echo $option_name; ?>_nonce_test' ) ); ?>" id="test_<?php echo $option_name; ?>_btn" />

</div>
</div>
