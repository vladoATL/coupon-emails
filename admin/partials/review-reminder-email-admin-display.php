<?php
namespace COUPONEMAILS;
$option_name = "couponemails_reviewreminder";
couponemails_reviewreminder_run_cron();

if ( isset( $_GET['runtest'] ) ) {
	$rr = new \COUPONEMAILS\Coupon_Emails_AfterOrder('couponemails_reviewreminder');
	$rr -> afterorderemail_event_setup();
	header("location:admin.php?page=couponemails&tab=reminder"); 
}

// Process export
if ( isset( $_GET['reviewreminderexport'] ) ) {
	global $wpdb;
	ob_end_clean();
	$table_head = array('First Name', 'Last Name', 'Email', 'User ID', 'Orders count', 'Order total', 'Last order' );
	$csv = implode( ';' , $table_head );
	$csv .= "\n";

	$reminders = new \COUPONEMAILS\Coupon_Emails_AfterOrder($option_name);
	$result = $reminders->get_users_afterorder();

	foreach ( $result as $key => $value ) {
		$csv .=   implode(';', $value);
		$csv .= "\n";
	}
	$csv .= "\n";
	$filename = 'reviewreminders.csv';
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

?>

<div class="wrap woocommerce">
	<div id="<?php echo $option_name; ?>-setting"  class="coupon-emails-setting">
<div class="couponemails_loader_cover">
	<div class="couponemails_loader"></div> 
</div>
<ul class="subsubsub">
	<li>
		<a href="?page=couponemails&tab=reminder&amp;section=" class="current">
		<?php echo  esc_html__( 'Review Reminders', 'coupon-emails' ); ?></a>
	|</li>
	<li>
		<a href="?page=couponemails&tab=reminder&amp;section=expiration" class="">
		<?php echo  esc_html__( 'Expiration Reminders', 'coupon-emails' ); ?></a>
	|</li>
</ul>
<input type="button" value="<?php echo  esc_html__( 'Restore Defaults', 'coupon-emails' ); ?>" class="button button-primary btn-restore"
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' .  $option_name . '_nonce' ) ); ?>"
id="restore_<?php echo $option_name; ?>_values_btn" />

<div class="icon32" id="icon-options-general">
<br></div>
<br>
<h2><?php echo esc_html_x('Review Reminder Emails Settings','Setting', 'coupon-emails'); ?> </h2>
<h4><?php echo esc_html_x('No coupons are generated in this section. Only emails are sent with a call to action a certain number of days after the order - for example, a reminder to write a review.','Setting', 'coupon-emails'); ?> </h4>
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
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Days after last order', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="number" id="<?php echo $option_name; ?>_options[days_after_order]" name="<?php echo $option_name; ?>_options[days_after_order]"  style="width: 100px;" value="<?php echo $options['days_after_order'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( 'Enter the number of days after last order when to send this email with coupon.', 'coupon-emails'), false); ?>
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
			<th class="titledesc"><?php echo esc_html__( 'Users who bought one of these products', 'coupon-emails' ); ?>:</th>
			<td>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="<?php echo $option_name; ?>_options[bought_products]" name="<?php echo $option_name; ?>_options[bought_products][]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" >
					<?php
		if (isset($options['bought_products'])) {
			$product_ids = $options['bought_products'];
			foreach ( $product_ids as $product_id ) {
				$product = wc_get_product( $product_id );
				if ( is_object( $product ) ) {
					echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
				}
			}
		}
		?>
				</select>
				<?php  echo wc_help_tip(esc_html__( 'The email will be sent to users who have previously purchased at least one of the selected products. Select the main product if you want it to include all variants.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Users who never bought these products', 'coupon-emails' ); ?>:</th>
			<td>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="<?php echo $option_name; ?>_options[not_bought_products]" name="<?php echo $option_name; ?>_options[not_bought_products][]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" >
					<?php
		if (isset($options['not_bought_products'])) {
			$ex_product_ids = $options['not_bought_products'];
			foreach ( $ex_product_ids as $product_id ) {
				$product = wc_get_product( $product_id );
				if ( is_object( $product ) ) {
					echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
				}
			}
		}
		?>
				</select>
				<?php  echo wc_help_tip(esc_html__( 'The email will be sent to users who have never purchased any of the selected products.', 'coupon-emails' ), false) ; ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Users who bought products in these categories', 'coupon-emails' ); ?>:</th>
			<td>
				<select id="<?php echo $option_name; ?>_options[bought_cats]" name="<?php echo $option_name; ?>_options[bought_cats][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'All categories', 'woocommerce' ); ?>">
					<?php
		$category_ids = isset($options['bought_cats']) ? $options['bought_cats'] : "";
		$categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
		if ( $categories ) {
			foreach ( $categories as $cat ) {
				echo '<option value="' . esc_attr( $cat->term_id ) . '"' . wc_selected( $cat->term_id, $category_ids ) . '>' . esc_html( $cat->name ) . '</option>';
			}
		}
		?>
				</select>
				<?php  echo wc_help_tip(esc_html__(  'The email will be sent to users who have previously purchased products in the selected categories.', 'coupon-emails'), false) ; ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Users who never bought products in these categories', 'coupon-emails' ); ?>:</th>
			<td>
				<select id="<?php echo $option_name; ?>_options[not_bought_cats]" name="<?php echo $option_name; ?>_options[not_bought_cats][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No categories', 'woocommerce' ); ?>">
					<?php
					$category_ids = isset($options['not_bought_cats']) ? $options['not_bought_cats'] : "";
		$categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
		if ( $categories ) {
			foreach ( $categories as $cat ) {
				echo '<option value="' . esc_attr( $cat->term_id ) . '"' . wc_selected( $cat->term_id, $category_ids ) . '>' . esc_html( $cat->name ) . '</option>';
			}
		}
		?>
				</select>
				<?php  echo wc_help_tip(esc_html__('The email will be sent to users who have never purchased products in the selected categories.', 'coupon-emails' ), false) ; ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Users who total spent', 'coupon-emails' ); ?>:</th>
			<td>
				<?php echo esc_html__( 'Minimum', 'coupon-emails' ); ?>:
				<input type="number" id="<?php echo $option_name; ?>_options[minimum_spent]" name="<?php echo $option_name; ?>_options[minimum_spent]"  style="width: 80px;" value="<?php echo $options['minimum_spent'] ?? ''; ?>"</input>&nbsp;
				<?php echo esc_html__( 'Maximum', 'coupon-emails' ); ?>:
				<input type="number" id="<?php echo $option_name; ?>_options[maximum_spent]" name="<?php echo $option_name; ?>_options[maximum_spent]"  style="width: 80px;" value="<?php echo $options['maximum_spent'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( 'These fields allow you to filter users by the minimum and maximum amount of their total spending.', 'coupon-emails'), false); ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Filter by previous reviews', 'coupon-emails' ); ?>:</th>
			<td>
				<select name='<?php echo $option_name; ?>_options[already_rated]' style="width: 200px;"> 
				<option value='0' <?php selected( $options['already_rated'] ?? '', 0 ); ?>><?php echo esc_html__( 'All users', 'coupon-emails' ); ?>&nbsp;</option>
				<option value='1' <?php selected( $options['already_rated'] ?? '', 1 ); ?>><?php echo esc_html__( 'Only those who have not rated yet', 'coupon-emails' ); ?>&nbsp;</option>
				<option value='2' <?php selected( $options['already_rated'] ?? '', 2 ); ?>><?php echo esc_html__( 'Only those who have already rated', 'coupon-emails' ); ?>&nbsp;</option>
			</select>
			<?php  echo wc_help_tip(esc_html__( 'This setting allows you to filter the sending of emails according to whether the user has previously created a review.', 'coupon-emails' ), false); ?>
			</td>
		</tr>		
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'List of users who receive the email today', 'coupon-emails' ); ?>:</th>
			<td>
				<a class="button button-primary" href="admin.php?page=couponemails&tab=reminder&section&reviewreminderexport=table&noheader=1"><?php echo esc_html__( 'Download csv', 'coupon-emails' ); ?></a>
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
