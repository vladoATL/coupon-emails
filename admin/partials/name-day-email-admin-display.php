<?php
namespace COUPONEMAILS;
global $woocommerce, $post;


// Process export
if ( isset( $_GET['namesexport'] ) ) {
	$options = get_option('namedayemail_options');
	$language = $options['language'];

	$namedays = new \COUPONEMAILS\Calendars();

	switch ($language) {
		case 1:
			$table_body = $namedays->get_slovak_namedays_array();
			break;
		case 2:
			$table_body = $namedays->get_czech_namedays_array();
			break;
		case 3:
			$table_body = $namedays->get_polish_namedays_array();
			break;
		case 4:
			$table_body = $namedays->get_croatian_namedays_array();
			break;								
		case 5:
			$table_body = $namedays->get_hungarian_namedays_array();
			break;
		case 6:
			$table_body = $namedays->get_austrian_namedays_array();
			break;
		case 7:
			$table_body = $namedays->get_spanish_namedays_array();
			break;			
	}
	ob_end_clean();
	$table_head = array( 'Date', 'Name' );
	$csv = implode( ';' , $table_head );
	$csv .= "\n";
	foreach ( $table_body as $key => $value ) {
		$arr    = explode(',', $value);
		$trimmed_array = array_map('trim', $arr);
		$names_str = implode(';', array_unique($trimmed_array));

		$csv .=  $key . ';' . $names_str  ;
		$csv .= "\n";
	}

	$filename = 'name_days.csv';
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

$option_name = "namedayemail";

?>
<div class="wrap woocommerce">
<div id="namedayemail-setting"  class="coupon-emails-setting">
<div class="loader_cover">
	<div class="namedays_loader"></div> </div>
	<input type="button" value="<?php echo  __( 'Restore Defaults', 'coupon-emails' ); ?>" class="button button-primary btn-restore"
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' .  $option_name . '_nonce' ) ); ?>"
id="restore_namedayemail_values_btn" />

<div class="icon32" id="icon-options-general"><br></div>
<h2><?php echo _x('Name Day Emails Settings','Setting', 'coupon-emails'); ?> </h2>

<form method="post" id="form1" name="form1" action="options.php">
<?php
settings_fields('namedayemail_plugin_options');
$options = get_option('namedayemail_options');
namedayemail_run_cron();
?>
<table class="form-table">
	<tr valign="top">
		<th class="titledesc"><?php echo __( 'Enable auto sending emails', 'coupon-emails' ); ?>:</th>
		<td><input type="checkbox" name="namedayemail_options[enabled]" id="namedayemail_options[enabled]"  value="1" <?php echo checked( 1, $options['enabled'] ?? '', false ) ?? '' ; ?>>
			<?php  echo wc_help_tip(__( 'Turn on and off the automatic functionality of email sending', 'coupon-emails' ), false); ?>
		</td>
	</tr>
	<tr valign="top">
		<th class="titledesc"><?php echo __( 'Run in test mode', 'coupon-emails' ); ?>:</th>
		<td><input type="checkbox" name="namedayemail_options[test]" id="namedayemail_options[test]"  value="1" <?php echo checked( 1, $options['test'] ?? '', false ) ?? '' ; ?>>
			<?php  echo wc_help_tip(__( 'Turn on when testing. The user will not get emails. All emails will be sent to BCC/Test address.', 'coupon-emails' ), false); ?>
		</td>
	</tr>
	<tr valign="top">
		<th class="titledesc"><?php echo __( 'Name days calendar', 'coupon-emails' ); ?>:</th>
		<td>
			<select name='namedayemail_options[language]' style="width: 200px;">
				<option value='1' <?php selected( $options['language'] ?? '', 1 ); ?>><?php echo __( 'Slovak calendar', 'coupon-emails' ); ?>&nbsp;</option>
				<option value='2' <?php selected( $options['language'] ?? '', 2 ); ?>><?php echo __( 'Czech calendar', 'coupon-emails' ); ?>&nbsp;</option>
				<option value='3' <?php selected( $options['language'] ?? '', 3 ); ?>><?php echo __( 'Polish calendar', 'coupon-emails' ); ?>&nbsp;</option>
				<option value='4' <?php selected( $options['language'] ?? '', 4 ); ?>><?php echo __( 'Croatian calendar', 'coupon-emails' ); ?>&nbsp;</option>	
				<option value='5' <?php selected( $options['language'] ?? '', 5 ); ?>><?php echo __( 'Hungarian calendar', 'coupon-emails' ); ?>&nbsp;</option>
				<option value='6' <?php selected( $options['language'] ?? '', 6 ); ?>><?php echo __( 'Austrian calendar', 'coupon-emails' ); ?>&nbsp;</option>
				<option value='7' <?php selected( $options['language'] ?? '', 7 ); ?>><?php echo __( 'Spanish calendar', 'coupon-emails' ); ?>&nbsp;</option>						
			</select>
			<?php  echo wc_help_tip(__( 'Choose the calendar country to be used', 'coupon-emails' ), false); ?>
			<a class="button button-primary" href="admin.php?page=couponemails&tab=name-day&namesexport=table&noheader=1"><?php echo __( 'Download csv', 'coupon-emails' ); ?></a>
			<?php  echo wc_help_tip(__( 'Make sure the selection is saved before download.', 'coupon-emails' ), false); ?>
		</td>
	</tr>
	<tr>
		<th class="titledesc"><?php echo __( 'Send email X days before name day', 'coupon-emails' ); ?>:</th>
		<td>
			<input type="number" id="namedayemail_options[days_before]" name="namedayemail_options[days_before]"  style="width: 60px;" value="<?php echo $options['days_before'] ?? ''; ?>"</input>
			<?php 
			$funcs = new EmailFunctions("namedayemail");
			echo $funcs->namedayemail_get_next_names(); ?>		
		</td>
	</tr>

	<tr>
		<th class="titledesc"><?php echo __( 'Send email every day at', 'coupon-emails' ); ?>:</th>
		<td>
			<input type="time" id="namedayemail_options[send_time]" name="namedayemail_options[send_time]"  style="width: 100px;" value="<?php echo $options['send_time'] ?? ''; ?>"</input>
			<?php  echo wc_help_tip(__( 'This is time when cron sends the email messages.', 'coupon-emails' ), false); ?>
		</td>
	</tr>
</table>

<?php include('coupon-form.php'); ?>

		</form>
			<input type="button" value="<?php echo  __( 'Create a test', 'coupon-emails' ); ?>" class="button button-primary" 
			attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' .  $option_name . '_nonce_test' ) ); ?>" id="test_namedayemail_btn" />		
			
	</div>
	</div>