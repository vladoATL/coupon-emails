<?php
namespace COUPONEMAILS;
// Process export
$option_name = "birthdayemail";

if ( isset( $_GET['runtest'] ) ) {
	$bd = new \COUPONEMAILS\Birthdays();
	$bd -> birthdayemail_event_setup();
	header("location:admin.php?page=couponemails&tab=birth-day"); 
}

// Process export
if ( isset( $_GET['birthdayexport'] ) ) {
	global $wpdb;
	$options = get_option($option_name . '_options');
	$birthday = new \COUPONEMAILS\Birthdays();
	ob_end_clean();
	$table_head = array('User ID','First Name', 'Last Name', 'Email',  'DOB',  'Age', 'Last time sent' );
	$csv = implode( ';' , $table_head );
	$csv .= "\n";

	$str_nameday =  date('Y-m-d',strtotime('+' . $options['days_before']  . ' day'));
	$dateValue = strtotime($str_nameday);
	$m = intval(date("m", $dateValue));
	$d = intval(date("d", $dateValue));
	$result =  (array) $birthday ->get_celebrating_users($d,$m);

	$str_nameday =  date('Y-m-d',strtotime('+' . $options['days_before'] + 1 . ' day'));
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
	$filename = 'birthday.csv';
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
	
	$dobs = new \COUPONEMAILS\Birthdays();
	$result = $dobs->get_users_dob_list();
					
	foreach ( $result as $key => $value ) {
		$csv .=   implode(';', $value);  
		$csv .= "\n";
	}
	$csv .= "\n";
	$filename = 'birth_days.csv';
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

birthdayemail_run_cron();

?>

<div class="wrap woocommerce">
<div id="birthdayemail-setting" class="coupon-emails-setting">
<div class="loader_cover">
	<div class="birthdays_loader"></div> </div>
	<input type="button" value="<?php echo  __( 'Restore Defaults', 'coupon-emails' ); ?>" class="button button-primary btn-restore"
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' .  $option_name . '_nonce' ) ); ?>"
id="restore_birthdayemail_values_btn" />

<h2><?php echo _x('Birthday Emails Settings','Setting', 'coupon-emails'); ?> </h2>
<form method="post" id="form2" name="form2" action="options.php">
	<?php
	settings_fields('birtdayemail_plugin_options');
	$options = get_option('birthdayemail_options');
	?>
	<table class="form-table">
		<tr valign="top">
			<th class="titledesc"><?php echo __( 'Enable auto sending emails', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="birthdayemail_options[enabled]" id="birthdayemail_options[enabled]"  value="1" <?php echo checked( 1, $options['enabled'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(__( 'Turn on and off the automatic functionality of email sending', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo __( 'Run in test mode', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="birthdayemail_options[test]" id="birthdayemail_options[test]"  value="1" <?php echo checked( 1, $options['test'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(__( 'Turn on when testing. The user will not get emails. All emails will be sent to BCC/Test address.', 'coupon-emails' ), false); ?>
				<button type="button" class="button button-primary" id="run_button" onClick="window.location.search += '&runtest=1'"><?php echo __( 'Run now', 'coupon-emails' ); ?></button>
				<input type="checkbox" style="display: none;" name="test_enabled" id="test_enabled"  value="1" <?php echo checked( 1, $options['test'] ?? '', false ) ?? '' ; ?>>	<?php  echo wc_help_tip(sprintf(_n( 'If you want to run a test, check the chekbox and save. After pushing this button maximum %s coupon will be created and emails sent to administrator.', 'If you want to run a test, check the chekbox and save. After pushing this button maximum %s coupons will be created and test emails sent to administrator.', MAX_TEST_EMAILS, 'coupon-emails' ), MAX_TEST_EMAILS), false); ?>			
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Send email X days before birthday', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="number" id="birthdayemail_options[days_before]" name="birthdayemail_options[days_before]"  style="width: 60px;" value="<?php echo $options['days_before'] ?? ''; ?>"</input>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Send email every day at', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="time" id="birthdayemail_options[send_time]" name="birthdayemail_options[send_time]"  style="width: 100px;" value="<?php echo $options['send_time'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(__( 'This is time when cron sends the email messages.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo __( 'Send it onle one time in a year', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="birthdayemail_options[once_year]" id="birthdayemail_options[once_year]"  value="1" <?php echo checked( 1, $options['once_year'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(__( 'Send the email with coupon only once in a year to customer even though the DOB was changed.', 'coupon-emails' ), false); ?>
			</td>
		</tr>		
		<tr>
			<th class="titledesc"><?php echo __( 'Download file with birthdays', 'coupon-emails' ); ?>:</th>
			<td>
				<a class="button button-primary" href="admin.php?page=couponemails&tab=birth-day&dobexport=table&noheader=1"><?php echo __( 'Download csv', 'coupon-emails' ); ?></a>
				<?php  echo wc_help_tip(__( 'Download csv file with brithdays of users.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'List of users who receive the email today and tomorrow', 'coupon-emails' ); ?>:</th>
			<td>
				<a class="button button-primary" href="admin.php?page=couponemails&tab=birth-day&birthdayexport=table&noheader=1"><?php echo __( 'Download csv', 'coupon-emails' ); ?></a>
				<?php  echo wc_help_tip(__( "Download csv file with filtered users for today's and tomorrow's email.", 'coupon-emails' ), false); ?>
			</td>
		</tr>	
	</table>

<?php include('coupon-form.php'); ?>

</form>
<input type="button" value="<?php echo  __( 'Create a test', 'coupon-emails' ); ?>" class="button button-primary" attr-nonce="<?php echo esc_attr( wp_create_nonce( '_birthdayemail_nonce_test' ) ); ?>" id="test_birthdayemail_btn" />

</div>
</div>

 <script>
	 const enabled_hidden = document.querySelector('input[id="test_enabled"]');
	 const runNowButton = document.getElementById('run_button');
	 enabled_hidden.addEventListener('change', checkButtonStatus);

	 function checkButtonStatus()
	 {
		 const allChecked = enabled_hidden.checked ;
		 runNowButton.disabled = !allChecked;
	 }
	 checkButtonStatus();
 </script>