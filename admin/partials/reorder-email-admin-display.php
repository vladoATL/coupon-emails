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

?>

<div class="wrap woocommerce">
<div id="reorderemail-setting"  class="myday-setting">
<div class="loader_cover">
	<div class="reorder_loader"></div> </div>
<input type="button" value="<?php echo  __( 'Restore Defaults', 'coupon-emails' ); ?>" class="button button-primary"
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_reorderemail_nonce' ) ); ?>"
id="restore_reorder_values_btn" />

<div class="icon32" id="icon-options-general"><br></div>
<h2><?php echo _x('Order Reorder Emails Settings','Setting', 'coupon-emails'); ?> </h2>

<form method="post" id="form4" name="form4" action="options.php">
	<?php
	settings_fields('reorderemail_plugin_options');
	$options = get_option('reorderemail_options');

	?>
	<table class="form-table">
		<tr valign="top">
			<th class="titledesc"><?php echo __( 'Enable auto sending emails', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="reorderemail_options[enabled]" id="reorderemail_options[enabled]"  value="1" <?php echo checked( 1, $options['enabled'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(__( 'Turn on and off the automatic functionality of email sending', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo __( 'Run in test mode', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="reorderemail_options[test]" id="reorderemail_options[test]"  value="1" <?php echo checked( 1, $options['test'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(__( 'Turn on when testing. The user will not get emails. All emails will be sent to BCC/Test address.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Days after last order', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="number" id="reorderemail_options[days_after_order]" name="reorderemail_options[days_after_order]"  style="width: 100px;" value="<?php echo $options['days_after_order'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(__( 'Enter number of days after last order when to send this email with coupon.', 'coupon-emails'), false); ?>
			</td>
		</tr>	
		<tr>
			<th class="titledesc"><?php echo __( 'Send email every day at', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="time" id="reorderemail_options[send_time]" name="reorderemail_options[send_time]"  style="width: 100px;" value="<?php echo $options['send_time'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(__( 'This is time when cron sends the email messages.', 'coupon-emails' ), false); ?>
			</td>
		</tr>		
		<tr>
			<th class="titledesc"><?php echo __( 'Users who bought one of these products', 'coupon-emails' ); ?>:</th>
			<td>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="reorderemail_options[bought_products]" name="reorderemail_options[bought_products][]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" >
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
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="reorderemail_options[not_bought_products]" name="reorderemail_options[not_bought_products][]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" >
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
				<select id="reorderemail_options[bought_cats]" name="reorderemail_options[bought_cats][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'All categories', 'woocommerce' ); ?>">
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
				<select id="reorderemail_options[not_bought_cats]" name="reorderemail_options[not_bought_cats][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No categories', 'woocommerce' ); ?>">
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
				<input type="number" id="reorderemail_options[minimum_spent]" name="reorderemail_options[minimum_spent]"  style="width: 80px;" value="<?php echo $options['minimum_spent'] ?? ''; ?>"</input>&nbsp;
				<?php echo __( 'Maximum', 'coupon-emails' ); ?>:
				<input type="number" id="reorderemail_options[maximum_spent]" name="reorderemail_options[maximum_spent]"  style="width: 80px;" value="<?php echo $options['maximum_spent'] ?? ''; ?>"</input>
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
	<h3><?php echo __('Coupon settings', 'coupon-emails'); ?> </h3>
	<table id="coupon-table" class="form-table">
		<tr>
			<th class="titledesc"><?php echo __( 'Description', 'woocommerce' ); ?>:</th>
			<td>
				<input type="text" id="reorderemail_options[description]" name="reorderemail_options[description]"  style="width: 500px;" value="<?php echo $options['description'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(__( 'Description will be used on Coupons page.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Count of coupon characters', 'coupon-emails' ); ?>:</th>
			<td>
				<select name='reorderemail_options[characters]' style="width: 200px;">
					<option value='5' <?php selected( $options['characters'] ?? '', 5 ); ?>><?php echo '5 ' . __( 'characters', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='6' <?php selected( $options['characters'] ?? '', 6 ); ?>><?php echo '6 ' . __( 'characters', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='7' <?php selected( $options['characters'] ?? '', 7 ); ?>><?php echo '7 ' . __( 'characters', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='8' <?php selected( $options['characters'] ?? '', 8 ); ?>><?php echo '8 ' . __( 'characters', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='9' <?php selected( $options['characters'] ?? '', 9 ); ?>><?php echo '9 ' . __( 'characters', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='10' <?php selected( $options['characters'] ?? '', 10 ); ?>><?php echo '10 ' . __( 'characters', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='0' <?php selected( $options['characters'] ?? '', 0 ); ?>><?php echo  __( "Don't generate coupon", 'coupon-emails' ); ?>&nbsp;</option>
				</select>
				<?php  echo wc_help_tip(__( 'Select how many characters the generated coupons should consist of.', 'coupon-emails' ), false); ?>
			</td>
		</tr>

		<tr valign="top">
			<th class="titledesc"><?php echo __( 'Discount type', 'woocommerce' ); ?>:</th>
			<td>
				<select name='reorderemail_options[disc_type]' style="width: 200px;">
					<option value='1' <?php selected( $options['disc_type'] ?? '', 1 ); ?>><?php echo __( 'Percentage discount', 'woocommerce' ); ?>&nbsp;</option>
					<option value='2' <?php selected( $options['disc_type'] ?? '', 2 ); ?>><?php echo __( 'Fixed cart discount', 'woocommerce' ); ?>&nbsp;</option>
					<option value='3' <?php selected( $options['disc_type'] ?? '', 3 ); ?>><?php echo __( 'Fixed product discount', 'woocommerce' ); ?>&nbsp;</option>
				</select>
				<?php  echo wc_help_tip(__( 'Set the discount type.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Coupon amount', 'woocommerce' ); ?>:</th>
			<td>
				<input type="number" id="reorderemail_options[coupon_amount]" name="reorderemail_options[coupon_amount]"  style="width: 60px;" value="<?php echo $options['coupon_amount'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(__(  'Value of the coupon.', 'woocommerce' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Minimum spend', 'woocommerce' ); ?>:</th>
			<td>
				<input type="number" id="reorderemail_options[minimum_amount]" name="reorderemail_options[minimum_amount]"  style="width: 60px;" value="<?php echo $options['minimum_amount'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(__( 'This field allows you to set the minimum spend (subtotal) allowed to use the coupon.', 'woocommerce'), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Maximum spend', 'woocommerce' ); ?>:</th>
			<td>
				<input type="number" id="reorderemail_options[maximum_amount]" name="reorderemail_options[maximum_amount]"  style="width: 60px;" value="<?php echo $options['maximum_amount'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(__( 'This field allows you to set the maximum spend (subtotal) allowed to use the coupon.', 'woocommerce'  ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Coupon expires in days', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="number" id="reorderemail_options[expires]" name="reorderemail_options[expires]"  style="width: 60px;" value="<?php echo $options['expires'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(__( 'Leave this field blank if unexpired coupons are to be created.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Limit usage to X items', 'woocommerce' ); ?>:</th>
			<td>
				<input type="number" id="reorderemail_options[max_products]" name="reorderemail_options[max_products]"  style="width: 60px;" value="<?php echo $options['max_products'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(__( 'The generated coupon can be used for a maximum number of products. For unlimited use, leave blank.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Good only for products', 'coupon-emails' ); ?>:</th>
			<td>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="reorderemail_options[only_products]" name="reorderemail_options[only_products][]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" >
					<?php
	if (isset($options['only_products'])) {
		$product_ids = $options['only_products'];
		foreach ( $product_ids as $product_id ) {
			$product = wc_get_product( $product_id );
			if ( is_object( $product ) ) {
				echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
			}
		}
	}
	?>
				</select>
				<?php  echo wc_help_tip(__( 'Products that the coupon will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Exclude products', 'woocommerce' ); ?>:</th>
			<td>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="reorderemail_options[exclude_prods]" name="reorderemail_options[exclude_prods][]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" >
					<?php
	if (isset($options['exclude_prods'])) {
		$ex_product_ids = $options['exclude_prods'];
		foreach ( $ex_product_ids as $product_id ) {
			$product = wc_get_product( $product_id );
			if ( is_object( $product ) ) {
				echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
			}
		}
	}
	?>
				</select>
				<?php  echo wc_help_tip(__( 'Products that the coupon will not be applied to, or that cannot be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ), false) ; ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Product categories', 'woocommerce' ); ?>:</th>
			<td>
				<select id="reorderemail_options[only_cats]" name="reorderemail_options[only_cats][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No categories', 'woocommerce' ); ?>">
					<?php
					$category_ids = isset($options['only_cats']) ? $options['only_cats'] : "";
	$categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
	if ( $categories ) {
		foreach ( $categories as $cat ) {
			echo '<option value="' . esc_attr( $cat->term_id ) . '"' . wc_selected( $cat->term_id, $category_ids ) . '>' . esc_html( $cat->name ) . '</option>';
		}
	}
	?>
				</select>
				<?php  echo wc_help_tip(__(  'Product categories that the coupon will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce'), false) ; ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Exclude categories', 'woocommerce' ); ?>:</th>
			<td>
				<select id="reorderemail_options[exclude_cats]" name="reorderemail_options[exclude_cats][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No categories', 'woocommerce' ); ?>">
					<?php
					$category_ids =	isset($options['exclude_cats']) ? $options['exclude_cats'] : "";
	$categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
	if ( $categories ) {
		foreach ( $categories as $cat ) {
			echo '<option value="' . esc_attr( $cat->term_id ) . '"' . wc_selected( $cat->term_id, $category_ids ) . '>' . esc_html( $cat->name ) . '</option>';
		}
	}
	?>
				</select>
				<?php  echo wc_help_tip(__('Product categories that the coupon will not be applied to, or that cannot be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ), false) ; ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo __( 'Individual use only', 'woocommerce'); ?>:</th>
			<td><input type="checkbox" name="reorderemail_options[individual_use]" id="reorderemail_options[individual_use]"  value="1"
				<?php echo checked( 1, $options['individual_use'] ?? '', false ) ?? '' ; ?>>
				<?php
	echo wc_help_tip(__( 'Check this box if the coupon cannot be used in conjunction with other coupons.', 'woocommerce' )); ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo __( 'Allow free shipping', 'woocommerce' ); ?>:</th>
			<td><input type="checkbox" name="reorderemail_options[free_shipping]" id="reorderemail_options[free_shipping]"  value="1"
				<?php echo checked( 1, $options['free_shipping'] ?? '', false ) ?? '' ; ?>>
				<?php
	echo wc_help_tip(__( 'Check this box if the coupon grants free shipping. A <a href="%s" target="_blank">free shipping method</a> must be enabled in your shipping zone and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).', 'woocommerce' ), 'https://docs.woocommerce.com/document/free-shipping/'); ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo __( 'Exclude discounted products', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="reorderemail_options[exclude_discounted]" id="reorderemail_options[exclude_discounted]"  value="1" <?php echo checked( 1, $options['exclude_discounted'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(__('Check this box if the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are items in the cart that are not on sale.', 'woocommerce'  ), false) ; ?>

			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Coupon category', 'coupon-emails' ); ?>:</th>
			<td>
				<?php
	$acfw ="";
	if ( ! is_plugin_active( 'advanced-coupons-for-woocommerce-free/advanced-coupons-for-woocommerce-free.php' ) ) {
		$acfw = 'readonly';
	}
	?>
				<input type="text" id="reorderemail_options[coupon_cat]" name="reorderemail_options[coupon_cat]"  style="width: 200px;" value="<?php echo $options['coupon_cat'] ?? ''; ?>"
				<?php echo $acfw; ?>>
				<?php  echo wc_help_tip(__( 'This feature can be best used if the Advanced Coupons for WooCommerce plugin (free) is installed. Enter the name of the coupon category that will be created if it does not exist.', 'coupon-emails' ), false); ?>
			</td>
		</tr>

	</table>

	<h3><?php echo _x('Email message setting','Setting section', 'coupon-emails'); ?> </h3>
	<table id="email-table" class="form-table">
		<tr>
			<th class="titledesc"><?php echo __( 'Email from name', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="text" id="reorderemail_options[from_name]" name="reorderemail_options[from_name]"  style="width: 200px;" value="<?php echo $options['from_name'] ?? ''; ?>"</input>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Email from address', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="email" id="reorderemail_options[from_address]" name="reorderemail_options[from_address]"  style="width: 200px;" value="<?php echo $options['from_address'] ?? ''; ?>"</input>

			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Email CC', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="email" id="reorderemail_options[cc_address]" name="reorderemail_options[cc_address]"  style="width: 200px;" value="<?php echo $options['cc_address'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(__( 'Add multiple emails separated by comma ( , ).', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th scope="row" class="titledesc">
				<?php echo __( 'BCC and Test email address', 'coupon-emails' ); ?>:
			</th>
			<td>
				<input type="email" id="reorderemail_options[bcc_address]" name="reorderemail_options[bcc_address]"  style="width: 200px;" value="<?php echo $options['bcc_address'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(__( 'This email address is used for testing as well as for all email messages as a blind copy address.', 'coupon-emails' ) . ' ' .  __( 'Add multiple emails separated by comma ( , ).', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo __( 'Use WooCommerce email template', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="reorderemail_options[wc_template]" id="reorderemail_options[wc_template]"  value="1" <?php echo checked( 1, $options['wc_template'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(__( 'Turn this on if you want your email to look just like a regular WooCommerce email.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Email subject', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="text" id="reorderemail_options[subject]" name="reorderemail_options[subject]"  style="width: 500px;" value="<?php echo $options['subject'] ?? ''; ?>"</input>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo __( 'Email header', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="text" id="reorderemail_options[header]" name="reorderemail_options[header]"  style="width: 500px;" value="<?php echo $options['header'] ?? ''; ?>"</input>
				<?php echo wc_help_tip(__( 'This is short text on the top of the email.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<th class="titledesc" style="padding-bottom: 0px;"><?php echo __( 'Email body', 'coupon-emails' ); ?> :</th>
		<tr>
			<td colspan="2">
				<?php
	$args = array("textarea_name" => "reorderemail_options[email_body]", 'editor_class' => 'textarea_');
	$content_text  = $options['email_body'] ?? '';
	wp_editor( $content_text, "email_body", $args );
	?>
			</td>
		</tr>
		<tfoot>
			<tr>
				<td colspan="2">
					<p class="description">
						<?php echo __( 'Placeholders', 'coupon-emails' ); ?>:
						<i>{fname}, {fname5}, {lname}, {coupon}, {percent}, {products_cnt}, {expires}, {expires_in_days}, {last_order_date}, {my_day_date}, {site_name}, {site_url}, {site_name_url}<br>
							<small><?php echo __( 'Use {fname5} for Czech salutation.', 'coupon-emails' ); ?></small>
						</i>
					</p></td>
			</tr></tfoot>
	</table>		
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>			
</form>
<p>
<input type="button" value="<?php echo  __( 'Create a test', 'coupon-emails' ); ?>" class="button button-primary" 
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_reorder_nonce_test' ) ); ?>" id="test_reorder_btn" />
</p>
</div>
</div>