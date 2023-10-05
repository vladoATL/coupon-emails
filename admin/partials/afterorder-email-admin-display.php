<?php
namespace COUPONEMAILS;
$option_name = "afterorderemail";

if ( isset( $_GET['runtest'] ) ) {
	$nd = new \COUPONEMAILS\AfterOrder($option_name);
	$nd -> afterorderemail_event_setup();
	header("location:admin.php?page=couponemails&tab=after-order"); 
}

// Process export
if ( isset( $_GET['afterorderexport'] ) ) {
	global $wpdb;
	ob_end_clean();
	$table_head = array('First Name', 'Last Name', 'Email', 'User ID', 'Orders count', 'Order total', 'Last order' );
	$csv = implode( ';' , $table_head );
	$csv .= "\n";

	$afterorders = new \COUPONEMAILS\AfterOrder($option_name);
	$result = $afterorders->get_users_afterorder();

	foreach ( $result as $key => $value ) {
		$csv .=   implode(';', $value);
		$csv .= "\n";
	}
	$csv .= "\n";
	$filename = 'reorders.csv';
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
<div id="afterorderemail-setting"  class="coupon-emails-setting">
<div class="loader_cover">
	<div class="afterorder_loader"></div> </div>
	<input type="button" value="<?php echo  __( 'Restore Defaults', 'coupon-emails' ); ?>" class="button button-primary btn-restore"
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' .  $option_name . '_nonce' ) ); ?>"
id="restore_afterorder_values_btn" />

<div class="icon32" id="icon-options-general"><br></div>
<h2><?php echo _x('After Order Emails Settings','Setting', 'coupon-emails'); ?> </h2>

<form method="post" id="form5" name="form5" action="options.php">
	<?php
	settings_fields('afterorderemail_plugin_options');
	$options = get_option('afterorderemail_options');

	?>
	<table class="form-table">
		<tr valign="top">
			<th class="titledesc"><?php echo __( 'Enable auto sending emails', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="afterorderemail_options[enabled]" id="afterorderemail_options[enabled]"  value="1" <?php echo checked( 1, $options['enabled'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(__( 'Turn on and off the automatic functionality of email sending', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo __( 'Run in test mode', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="afterorderemail_options[test]" id="afterorderemail_options[test]"  value="1" <?php echo checked( 1, $options['test'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(__( 'Turn on when testing. The user will not get emails. All emails will be sent to BCC/Test address.', 'coupon-emails' ), false); ?>
				<button type="button" class="button button-primary" id="run_button" onClick="window.location.search += '&runtest=1'"><?php echo __( 'Run now', 'coupon-emails' ); ?></button>
				<input type="checkbox" style="display: none;" name="test_enabled" id="test_enabled"  value="1" <?php echo checked( 1, $options['test'] ?? '', false ) ?? '' ; ?>>	<?php  echo wc_help_tip(sprintf(_n( 'If you want to run a test, check the chekbox and save. After pushing this button maximum %s coupon will be created and emails sent to administrator.', 'If you want to run a test, check the chekbox and save. After pushing this button maximum %s coupons will be created and test emails sent to administrator.', MAX_TEST_EMAILS, 'coupon-emails' ), MAX_TEST_EMAILS), false); ?>			
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo __( 'The previous order was made at least', 'coupon-emails' ); ?>:</th>
			<td>
				<select name='afterorderemail_options[previous_order]' style="width: 200px;">
					<option value='0' <?php selected( $options['previous_order'] ?? '', 0 ); ?>><?php echo __( "Doesn't matter", 'coupon-emails' ); ?>&nbsp;</option>
					<option value='1' <?php selected( $options['previous_order'] ?? '', 1 ); ?>><?php echo __( 'a week ago', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='2' <?php selected( $options['previous_order'] ?? '', 2 ); ?>><?php echo __( 'a month ago', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='3' <?php selected( $options['previous_order'] ?? '', 3 ); ?>><?php echo __( 'a quarter ago', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='4' <?php selected( $options['previous_order'] ?? '', 4 ); ?>><?php echo __( 'half a year ago', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='5' <?php selected( $options['previous_order'] ?? '', 5 ); ?>><?php echo __( 'a year ago', 'coupon-emails' ); ?>&nbsp;</option>
				</select>
				<?php  echo wc_help_tip(__( 'An email with a coupon will only be sent if the previous order was placed before the specified time period has elapsed. For regular customers.', 'coupon-emails' ), false); ?>
			</td>
		</tr>		
		<tr>
			<th class="titledesc"><?php echo __( 'Send X days after order', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="number" id="afterorderemail_options[days_after_order]" name="afterorderemail_options[days_after_order]"  style="width: 100px;" value="<?php echo $options['days_after_order'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(__( 'Enter the number of days after order when to send this email with coupon.', 'coupon-emails'), false); ?>
			</td>
		</tr>		
		<tr>
			<th class="titledesc"><?php echo __( 'Send email every day at', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="time" id="afterorderemail_options[send_time]" name="afterorderemail_options[send_time]"  style="width: 100px;" value="<?php echo $options['send_time'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(__( 'This is time when cron sends the email messages.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Users who bought one of these products', 'coupon-emails' ); ?>:</th>
			<td>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="afterorderemail_options[bought_products]" name="afterorderemail_options[bought_products][]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" >
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
				<?php  echo wc_help_tip(__( 'The email will be sent to users who have previously purchased at least one of the selected products. Select the main product if you want it to include all variants.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Users who never bought these products', 'coupon-emails' ); ?>:</th>
			<td>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="afterorderemail_options[not_bought_products]" name="afterorderemail_options[not_bought_products][]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" >
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
				<?php  echo wc_help_tip(__( 'The email will be sent to users who have never purchased any of the selected products.', 'coupon-emails' ), false) ; ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Users who bought products in these categories', 'coupon-emails' ); ?>:</th>
			<td>
				<select id="afterorderemail_options[bought_cats]" name="afterorderemail_options[bought_cats][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'All categories', 'woocommerce' ); ?>">
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
				<?php  echo wc_help_tip(__(  'The email will be sent to users who have previously purchased products in the selected categories.', 'coupon-emails'), false) ; ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Users who never bought products in these categories', 'coupon-emails' ); ?>:</th>
			<td>
				<select id="afterorderemail_options[not_bought_cats]" name="afterorderemail_options[not_bought_cats][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No categories', 'woocommerce' ); ?>">
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
				<?php  echo wc_help_tip(__('The email will be sent to users who have never purchased products in the selected categories.', 'coupon-emails' ), false) ; ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Users who total spent', 'coupon-emails' ); ?>:</th>
			<td>
				<?php echo __( 'Minimum', 'coupon-emails' ); ?>:
				<input type="number" id="afterorderemail_options[minimum_spent]" name="afterorderemail_options[minimum_spent]"  style="width: 80px;" value="<?php echo $options['minimum_spent'] ?? ''; ?>"</input>&nbsp;
				<?php echo __( 'Maximum', 'coupon-emails' ); ?>:
				<input type="number" id="afterorderemail_options[maximum_spent]" name="afterorderemail_options[maximum_spent]"  style="width: 80px;" value="<?php echo $options['maximum_spent'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(__( 'These fields allow you to filter users by the minimum and maximum amount of their total spending.', 'coupon-emails'), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'List of users who receive the email today', 'coupon-emails' ); ?>:</th>
			<td>
				<a class="button button-primary" href="admin.php?page=couponemails&tab=after-order&afterorderexport=table&noheader=1"><?php echo __( 'Download csv', 'coupon-emails' ); ?></a>
				<?php  echo wc_help_tip(__( "Download csv file with filtered users for today's email.", 'coupon-emails' ), false); ?>
			</td>
		</tr>		
	</table>	

<?php include('coupon-form.php'); ?>
		
</form>
<p>
<input type="button" value="<?php echo  __( 'Create a test', 'coupon-emails' ); ?>" class="button button-primary" 
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' .  $option_name . '_nonce_test' ) ); ?>" id="test_afterorder_btn" />
</p>
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