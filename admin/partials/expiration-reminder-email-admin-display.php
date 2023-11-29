<?php
namespace COUPONEMAILS;

$option_name = "couponemails_expirationreminder";

if ( isset( $_GET['runtest'] ) ) {
	$rem = new \COUPONEMAILS\Coupon_Emails_ExpirationReminder();
	$rem -> expirationreminderemail_event_setup();
	header("location:admin.php?page=couponemails&tab=reminder&section=expiration"); 
}

// Process export
if ( isset( $_GET['expirationreminderexport'] ) ) {
	global $wpdb;
	ob_end_clean();
	$table_head = array('Category', 'Coupon', 'Email',  'First Name', 'Last Name',  'Coupon Expires', 'ID');
	$csv = implode( ';' , $table_head );
	$csv .= "\n";

	$funcs = new \COUPONEMAILS\Coupon_Emails_PrepareSQL($option_name);
	$result = $funcs->get_users_with_expired_coupons();
	
	foreach ( $result as $key => $value ) {
		$csv .=   implode(';', (array) $value);
		$csv .= "\n";
	}
	$csv .= "\n";
	$filename = 'expirationreminders.csv';
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
couponemails_expirationreminder_run_cron();

?>

<div class="wrap woocommerce">
	<div id="<?php echo $option_name; ?>-setting"  class="coupon-emails-setting">
<div class="couponemails_loader_cover">
	<div class="couponemails_loader"></div> 
</div>
<ul class="subsubsub">
	<li>
		<a href="?page=couponemails&tab=reminder&amp;section=" class="">
		<?php echo  esc_html__( 'Review reminders', 'coupon-emails' ); ?></a>
	|</li>
	<li>
		<a href="?page=couponemails&tab=reminder&amp;section=expiration" class="current">
		<?php echo  esc_html__( 'Expiration reminders', 'coupon-emails' ); ?></a>
	|</li>
</ul>
<input type="button" value="<?php echo  esc_html__( 'Restore Defaults', 'coupon-emails' ); ?>" class="button button-primary btn-restore btn-restore"
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' .  $option_name . '_nonce' ) ); ?>"
id="restore_<?php echo $option_name; ?>_values_btn" />

<div class="icon32" id="icon-options-general">
<br></div>
<br>
<h2><?php echo esc_html_x('Expiration Reminder Emails Settings','Setting', 'coupon-emails'); ?> </h2>
<h4><?php echo esc_html_x('No coupons are generated in this section. Only emails are sent to remind users of the validity of previously sent coupons some time before they expire.','Setting', 'coupon-emails'); ?> </h4>

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
				<input type="checkbox" style="display: none;" name="test_enabled" id="test_enabled"  value="1" <?php echo checked( 1, $options['test'] ?? '', false ) ?? '' ; ?>>	
				<?php  echo wc_help_tip(sprintf(_n( 'If you want to run a test, check the chekbox and save. After pushing this button maximum %s coupon will be created and email sent to administrator.', 'If you want to run a test, check the chekbox and save. After pushing this button maximum %s coupons will be created and test emails sent to administrator.', COUPON_EMAILS_MAX_TEST_EMAILS, 'coupon-emails' ), number_format_i18n(COUPON_EMAILS_MAX_TEST_EMAILS)), false); ?>				
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Days before coupon expiration', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="number" id="<?php echo $option_name; ?>_options[days_before]" name="<?php echo $option_name; ?>_options[days_before]"  style="width: 100px;" value="<?php echo $options['days_before'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( "Enter the number of days before coupon of the customer's coupon expires. Empty or 0 means that the coupon will expire today", 'coupon-emails'), false); ?>
			</td>
		</tr>		
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Send email every day at', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="time" id="<?php echo $option_name; ?>_options[send_time]" name="<?php echo $option_name; ?>_options[send_time]"  style="width: 100px;" value="<?php echo $options['send_time'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( 'This is time when cron sends the email messages.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Categories of coupons to be validated', 'coupon-emails' ); ?>:</th>
			<td>
				<select id="<?php echo $option_name; ?>_options[expiration_cats]" name="<?php echo $option_name; ?>_options[expiration_cats][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'All categories', 'woocommerce' ); ?>">
					<?php
					$category_ids = isset($options['expiration_cats']) ? $options['expiration_cats'] : "";
					$categories   = get_terms( 'shop_coupon_cat', 'orderby=name&hide_empty=0' );
		if ( $categories ) {
			foreach ( $categories as $cat ) {
				echo '<option value="' . esc_attr( $cat->term_id ) . '"' . wc_selected( $cat->term_id, $category_ids ) . '>' . esc_html( $cat->name ) . '</option>';
			}
		}
		?>
				</select>
				<?php  echo wc_help_tip(esc_html__('The email will be sent to users who recieved coupons in the selected categories and have not use them.', 'coupon-emails' ), false) ; ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Add coupon category', 'coupon-emails' ); ?>:</th>
			<td>
				<?php
		$acfw ="";
		if ( ! is_plugin_active( 'advanced-coupons-for-woocommerce/advanced-coupons-for-woocommerce.php' ) ) {
			$acfw = 'readonly';
		}
		?>
				<input type="text" id="<?php echo $option_name; ?>_options[coupon_cat]" name="<?php echo $option_name; ?>_options[coupon_cat]"  style="width: 200px;" value="<?php echo $options['coupon_cat'] ?? ''; ?>"
				<?php echo $acfw; ?>>
				<?php  echo wc_help_tip(esc_html__( 'This feature can be best used if the Advanced Coupons for WooCommerce plugin (free) is installed. Enter the name of the coupon category that will be created if it does not exist and added to the existing coupon.', 'coupon-emails' ), false); ?>
			</td>
		</tr>		
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'List of users who receive the email today', 'coupon-emails' ); ?>:</th>
			<td>
				<a class="button button-primary" href="admin.php?page=couponemails&tab=reminder&expirationreminderexport=table&noheader=1&section=expiration"><?php echo esc_html__( 'Download csv', 'coupon-emails' ); ?></a>
				<?php  echo wc_help_tip(esc_html__( "Download csv file with filtered users for today's email.", 'coupon-emails' ), false); ?>
			</td>
		</tr>		
	</table>	

<?php include('email-table.php'); ?>

</form>
<p>
<input type="button" value="<?php echo  esc_html__( 'Create a test', 'coupon-emails' ); ?>" class="button button-primary" 
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' .  $option_name . '_nonce_test' ) ); ?>" id="test_<?php echo $option_name; ?>_btn" />
</p>
</div>
</div>
