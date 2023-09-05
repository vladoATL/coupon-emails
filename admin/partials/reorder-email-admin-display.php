<?php
namespace COUPONEMAILS;
reorderemail_run_cron();

// Process export
if ( isset( $_GET['reorderexport'] ) ) {
	global $wpdb;
	ob_end_clean();
	$table_head = array('First Name', 'Last Name', 'Email', 'User ID', 'Orders count', 'Order total', 'Last order' );
	$csv = implode( ';' , $table_head );
	$csv .= "\n";
	
	$reorders = new Reorders();	
	$result = $reorders->get_users_reorders();

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
$option_name = "reorderemail";
?>

<div class="wrap woocommerce">
<div id="reorderemail-setting"  class="myday-setting">
<div class="loader_cover">
	<div class="reorder_loader"></div> </div>
<input type="button" value="<?php echo  __( 'Restore Defaults', 'coupon-emails' ); ?>" class="button button-primary"
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' .  $option_name . '_nonce' ) ); ?>"
id="restore_reorder_values_btn" />

<div class="icon32" id="icon-options-general"><br></div>
<h2><?php echo _x('Order Reorder Emails Settings','Setting', 'coupon-emails'); ?> </h2>

<form method="post" id="form4" name="form4" action="options.php">
	<?php
	settings_fields( $option_name .'_plugin_options');
	$options = get_option( $option_name .'_options');
	?>
	<table class="form-table">
		<tr valign="top">
			<th class="titledesc"><?php echo __( 'Enable auto sending emails', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="<?php echo $option_name; ?>_options[enabled]" id="<?php echo $option_name; ?>_options[enabled]"  value="1" <?php echo checked( 1, $options['enabled'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(__( 'Turn on and off the automatic functionality of email sending', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo __( 'Run in test mode', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="<?php echo $option_name; ?>_options[test]" id="<?php echo $option_name; ?>_options[test]"  value="1" <?php echo checked( 1, $options['test'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(__( 'Turn on when testing. The user will not get emails. All emails will be sent to BCC/Test address.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Days after last order', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="number" id="<?php echo $option_name; ?>_options[days_after_order]" name="<?php echo $option_name; ?>_options[days_after_order]"  style="width: 100px;" value="<?php echo $options['days_after_order'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(__( 'Enter number of days after last order when to send this email with coupon.', 'coupon-emails'), false); ?>
			</td>
		</tr>	
		<tr>
			<th class="titledesc"><?php echo __( 'Send email every day at', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="time" id="<?php echo $option_name; ?>_options[send_time]" name="<?php echo $option_name; ?>_options[send_time]"  style="width: 100px;" value="<?php echo $options['send_time'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(__( 'This is time when cron sends the email messages.', 'coupon-emails' ), false); ?>
			</td>
		</tr>		
		<tr>
			<th class="titledesc"><?php echo __( 'Users who bought one of these products', 'coupon-emails' ); ?>:</th>
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
				<?php  echo wc_help_tip(__( 'The email will be sent to users who have previously purchased at least one of the selected products. Select the main product if you want it to include all variants.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Users who never bought these products', 'coupon-emails' ); ?>:</th>
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
				<?php  echo wc_help_tip(__( 'The email will be sent to users who have never purchased any of the selected products.', 'coupon-emails' ), false) ; ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Users who bought products in these categories', 'coupon-emails' ); ?>:</th>
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
				<?php  echo wc_help_tip(__(  'The email will be sent to users who have previously purchased products in the selected categories.', 'coupon-emails'), false) ; ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Users who never bought products in these categories', 'coupon-emails' ); ?>:</th>
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
				<?php  echo wc_help_tip(__('The email will be sent to users who have never purchased products in the selected categories.', 'coupon-emails' ), false) ; ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Users who total spent', 'coupon-emails' ); ?>:</th>
			<td>
				<?php echo __( 'Minimum', 'coupon-emails' ); ?>:
				<input type="number" id="<?php echo $option_name; ?>_options[minimum_spent]" name="<?php echo $option_name; ?>_options[minimum_spent]"  style="width: 80px;" value="<?php echo $options['minimum_spent'] ?? ''; ?>"</input>&nbsp;
				<?php echo __( 'Maximum', 'coupon-emails' ); ?>:
				<input type="number" id="<?php echo $option_name; ?>_options[maximum_spent]" name="<?php echo $option_name; ?>_options[maximum_spent]"  style="width: 80px;" value="<?php echo $options['maximum_spent'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(__( 'These fields allow you to filter users by the minimum and maximum amount of their total spending.', 'coupon-emails'), false); ?>
			</td>
		</tr>			
		<tr>
			<th class="titledesc"><?php echo __( 'Download file with users to send email today', 'coupon-emails' ); ?>:</th>
			<td>
				<a class="button button-primary" href="admin.php?page=couponemails&tab=reorder&reorderexport=table&noheader=1"><?php echo __( 'Download csv', 'coupon-emails' ); ?></a>
				<?php  echo wc_help_tip(__( 'Download csv file with filtered users.', 'coupon-emails' ), false); ?>
			</td>
		</tr>		
	</table>	

	<?php include('coupon-form.php'); ?>

			
</form>
<p>
<input type="button" value="<?php echo  __( 'Create a test', 'coupon-emails' ); ?>" class="button button-primary" 
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' .  $option_name . '_nonce_test' ) ); ?>" id="test_reorder_btn" />
</p>
</div>
</div>