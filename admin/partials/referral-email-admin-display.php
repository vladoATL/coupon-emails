<?php
namespace COUPONEMAILS;
$option_name = "couponemails_referralemail";

// Process export
if ( isset( $_GET['referralexport'] ) ) {
	global $wpdb;
	ob_end_clean();
	$table_head = array('Created', 'Expiration', 'Coupon', 'Email', 'Referred by',  'Reward');
	$csv = implode( ';' , $table_head );
	$csv .= "\n";

	$referrals = new \COUPONEMAILS\Coupon_Emails_Referral();
	$result = $referrals->get_reffered_users();

	foreach ( $result as $value ) {
		$csv .= maybe_unserialize($value->meta_value)["created"] . ";";
		$csv .= maybe_unserialize($value->meta_value)["coupon_expiration"] . ";";
		$csv .= maybe_unserialize($value->meta_value)["coupon_code"] . ";";
		$csv .= maybe_unserialize($value->meta_value)["email"] . ";";
		$csv .= maybe_unserialize($value->meta_value)["referred_by"] . ";";
		$csv .= maybe_unserialize($value->meta_value)["reward"] . ";";
		$csv .= "\n";
	}
	$csv .= "\n";
	$filename = 'referralexport.csv';
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
		<a href="?page=couponemails&tab=referral" class="current">
		<?php echo  esc_html__( 'Referrals', 'coupon-emails' ); ?></a>
		|</li>
	<li>
		<a href="?page=couponemails&tab=referral&amp;section=confirmation" class="">
			<?php echo  esc_html__( 'Order Confirmation', 'coupon-emails' ); ?></a>
		|</li>
</ul>
<input type="button" value="<?php echo  esc_html__( 'Restore Defaults', 'coupon-emails' ); ?>" class="button button-primary btn-restore"
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' .  $option_name . '_nonce' ) ); ?>" id="restore_<?php echo $option_name; ?>_values_btn" />

<div class="icon32" id="icon-options-general"><br></div>
<br>
<h2><?php echo esc_html_x('Referral Emails Settings','Setting', 'coupon-emails'); ?> </h2>
<h4><?php echo esc_html_x('Using this section allows users to send referral emails with the coupon to their friends and, if friends create an order, accumulate the value of the coupon for themselves.','Setting', 'coupon-emails'); ?> </h4>
<form method="post" id="form_<?php echo $option_name; ?>" name="form_<?php echo $option_name; ?>" action="options.php">
	<?php
	settings_fields($option_name . '_plugin_options');
	$options = get_option($option_name . '_options');
	?>
	<table class="form-table">
	<tr valign="top">
		<th scope="row" class="titledesc"><?php echo esc_html__( 'Enable referral', 'coupon-emails' ); ?>:</th>
		<td><input type="checkbox" name="<?php echo $option_name; ?>_options[enable_referral]" id="<?php echo $option_name; ?>_options[enable_referral]"  value="1" <?php echo checked( 1, $options['enable_referral'] ?? '', false ) ?? '' ; ?>>
			<?php  echo wc_help_tip(esc_html__( "This will show/hide the My Recommendations section on the My Account page.", 'coupon-emails' ), false); ?>
		</td>
	</tr>
	</table>
	
<div class="coupon_setting">	
	<h3><?php echo esc_html__('My Account->Referrals page', 'coupon-emails'); ?> </h3>
	<h4><?php echo esc_html_x('If this section is enabled, users will have a My Recommendations section added to their My Account page.','Setting', 'coupon-emails'); ?> </h4>
	<table class="form-table">
		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Enable sending of referral emails', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="<?php echo $option_name; ?>_options[enabled]" id="<?php echo $option_name; ?>_options[enabled]"  value="1" <?php echo checked( 1, $options['enabled'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(esc_html__( 'This enables the option to send referral emails to users on the My Account->Referrals page', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Allow the coupon to be sent to existing users', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="<?php echo $option_name; ?>_options[existing]" id="<?php echo $option_name; ?>_options[existing]"  value="1" <?php echo checked( 1, $options['existing'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(esc_html__( 'If this option is not checked, the email will not be sent to already registered email addresses.', 'coupon-emails' ), false); ?>
			</td>
		</tr>		
		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Allow multiple referral coupons for one address', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="<?php echo $option_name; ?>_options[multiple]" id="<?php echo $option_name; ?>_options[multiple]"  value="1" <?php echo checked( 1, $options['multiple'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(esc_html__( 'If this option is not checked, only one referral coupon can be sent per email address.', 'coupon-emails' ), false); ?>
			</td>
		</tr>		
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Headline', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="text" id="<?php echo $option_name; ?>_options[headline]" name="<?php echo $option_name; ?>_options[headline]"  style="width: 95%;" value="<?php echo $options['headline'] ?? ''; ?>">
				<?php  echo wc_help_tip(esc_html__( 'Enter the title that comes above the user instructions.', 'coupon-emails' ), false); ?>
			</td>
		</tr>		
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Directions for users', 'coupon-emails' ); ?>:</th>
			<td>
				<textarea  style="width: 95%;" id="<?php echo $option_name; ?>_options[directions]" name="<?php echo $option_name; ?>_options[directions]" rows="5" type='textarea'><?php echo $options['directions'] ?? ''; ?></textarea>
				<?php  echo wc_help_tip(esc_html__( 'This is the text on the My Account page with instructions on how to send a referral email to a friend and the benefits of sending it.', 'coupon-emails' )); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'List of users who received the email referral email', 'coupon-emails' ); ?>:</th>
			<td>
				<a class="button button-primary" href="admin.php?page=couponemails&tab=referral&referralexport=table&noheader=1"><?php echo esc_html__( 'Download csv', 'coupon-emails' ); ?></a>
				<?php  echo wc_help_tip(esc_html__( "Download csv file with users who received the email referral email.", 'coupon-emails' ), false); ?>
			</td>
		</tr>		
	</table>
	</div>	
<div class="alt_coupon_setting">		
	<h3><?php echo esc_html__('Referrer Coupon settings', 'coupon-emails'); ?> </h3>	
	<h4><?php echo esc_html_x('This section defines the coupon settings for the user who sends the referral emails.','Setting', 'coupon-emails'); ?> </h4>
	<table id="coupon-table" class="form-table">
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Coupon explanation for the referrer', 'coupon-emails' ); ?>:</th>
			<td>
				<textarea  style="width: 95%;" id="<?php echo $option_name; ?>_options[ref_explanation]" name="<?php echo $option_name; ?>_options[ref_explanation]" rows="5" type='textarea'><?php echo $options['ref_explanation'] ?? ''; ?></textarea>
				<?php  echo wc_help_tip(esc_html__( 'This is the text on the My Account page explaining the coupon features to the referrer', 'coupon-emails' )); ?>
			</td>
		</tr>	
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Description', 'woocommerce' ); ?>:</th>
			<td>
				<input type="text" id="<?php echo $option_name; ?>_options[ref_description]" name="<?php echo $option_name; ?>_options[ref_description]"  style="width: 500px;" value="<?php echo $options['ref_description'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( 'Description will be used on Coupons page.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Count of coupon characters', 'coupon-emails' ); ?>:</th>
			<td>
				<select name='<?php echo $option_name; ?>_options[ref_characters]' style="width: 250px;">
					<option value='5' <?php selected( $options['ref_characters'] ?? '', 5 ); ?>><?php echo '5 ' . esc_html__( 'characters', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='6' <?php selected( $options['ref_characters'] ?? '', 6 ); ?>><?php echo '6 ' . esc_html__( 'characters', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='7' <?php selected( $options['ref_characters'] ?? '', 7 ); ?>><?php echo '7 ' . esc_html__( 'characters', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='8' <?php selected( $options['ref_characters'] ?? '', 8 ); ?>><?php echo '8 ' . esc_html__( 'characters', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='9' <?php selected( $options['ref_characters'] ?? '', 9 ); ?>><?php echo '9 ' . esc_html__( 'characters', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='10' <?php selected( $options['ref_characters'] ?? '', 10 ); ?>><?php echo '10 ' . esc_html__( 'characters', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='0' <?php selected( $options['ref_characters'] ?? '', 0 ); ?>><?php echo  esc_html__( "Don't generate coupon", 'coupon-emails' ); ?>&nbsp;</option>
				</select>
				<?php  echo wc_help_tip(esc_html__( 'Select how many characters the generated coupons should consist of.', 'coupon-emails' ), false); ?>
			</td>
		</tr>

		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Type of reward calculation', 'coupon-emails' ); ?>:</th>
			<td>
				<select name='<?php echo $option_name; ?>_options[ref_disc_type]' style="width: 250px;">
					<option value='1' <?php selected( $options['ref_disc_type'] ?? '', 1 ); ?>><?php echo esc_html__( 'Percentage of products value', 'coupon-emails' ); ?>&nbsp;</option>	
					<option value='2' <?php selected( $options['ref_disc_type'] ?? '', 2 ); ?>><?php echo esc_html__( 'Fixed amount', 'coupon-emails' ); ?>&nbsp;</option>
							
				</select>
				<?php  echo wc_help_tip(esc_html__( 'Set the type of reward calculation.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Reward amount', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="number" id="<?php echo $option_name; ?>_options[ref_coupon_amount]" name="<?php echo $option_name; ?>_options[ref_coupon_amount]"  style="width: 80px;" value="<?php echo $options['ref_coupon_amount'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__(  'Nominal value or percentage to be credited as reward.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Minimum order value', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="number" id="<?php echo $option_name; ?>_options[ref_minimum_amount]" name="<?php echo $option_name; ?>_options[ref_minimum_amount]"  style="width: 80px;" value="<?php echo $options['ref_minimum_amount'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( 'This field allows you to set the minimum spend (subtotal) allowed to use the coupon.', 'woocommerce'), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Maximum order value', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="number" id="<?php echo $option_name; ?>_options[ref_maximum_amount]" name="<?php echo $option_name; ?>_options[ref_maximum_amount]"  style="width: 80px;" value="<?php echo $options['ref_maximum_amount'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( 'This field allows you to set the maximum spend (subtotal) allowed to use the coupon.', 'woocommerce'  ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Coupon expires X days after the last email', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="number" id="<?php echo $option_name; ?>_options[ref_expires]" name="<?php echo $option_name; ?>_options[ref_expires]"  style="width: 80px;" value="<?php echo $options['ref_expires'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( 'Leave this field blank if unexpired coupons are to be created.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Limit usage to X items', 'woocommerce' ); ?>:</th>
			<td>
				<input type="number" id="<?php echo $option_name; ?>_options[ref_max_products]" name="<?php echo $option_name; ?>_options[ref_max_products]"  style="width: 80px;" value="<?php echo $options['ref_max_products'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( 'The generated coupon can be used for a maximum number of products. For unlimited use, leave blank.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Good only for products', 'coupon-emails' ); ?>:</th>
			<td>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="<?php echo $option_name; ?>_options[ref_only_products]" name="<?php echo $option_name; ?>_options[ref_only_products][]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" >
					<?php
	if (isset($options['ref_only_products'])) {
		$product_ids = $options['ref_only_products'];
		foreach ( $product_ids as $product_id ) {
			$product = wc_get_product( $product_id );
			if ( is_object( $product ) ) {
				echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
			}
		}
	}
	?>
				</select>
				<?php  echo wc_help_tip(esc_html__( 'Products that the coupon will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Exclude products', 'woocommerce' ); ?>:</th>
			<td>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="<?php echo $option_name; ?>_options[ref_exclude_prods]" name="<?php echo $option_name; ?>_options[ref_exclude_prods][]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" >
					<?php
	if (isset($options['ref_exclude_prods'])) {
		$ex_product_ids = $options['ref_exclude_prods'];
		foreach ( $ex_product_ids as $product_id ) {
			$product = wc_get_product( $product_id );
			if ( is_object( $product ) ) {
				echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
			}
		}
	}
	?>
				</select>
				<?php  echo wc_help_tip(esc_html__( 'Products that the coupon will not be applied to, or that cannot be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ), false) ; ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Product categories', 'woocommerce' ); ?>:</th>
			<td>
				<select id="<?php echo $option_name; ?>_options[ref_only_cats]" name="<?php echo $option_name; ?>_options[ref_only_cats][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No categories', 'woocommerce' ); ?>">
					<?php
	$category_ids = isset($options['ref_only_cats']) ? $options['ref_only_cats'] : "";
	$categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
	if ( $categories ) {
		foreach ( $categories as $cat ) {
			echo '<option value="' . esc_attr( $cat->term_id ) . '"' . wc_selected( $cat->term_id, $category_ids ) . '>' . esc_html( $cat->name ) . '</option>';
		}
	}
	?>
				</select>
				<?php  echo wc_help_tip(esc_html__(  'Product categories that the coupon will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce'), false) ; ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Exclude categories', 'woocommerce' ); ?>:</th>
			<td>
				<select id="<?php echo $option_name; ?>_options[ref_exclude_cats]" name="<?php echo $option_name; ?>_options[ref_exclude_cats][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No categories', 'woocommerce' ); ?>">
					<?php
	$category_ids =	isset($options['ref_exclude_cats']) ? $options['ref_exclude_cats'] : "";
	$categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
	if ( $categories ) {
		foreach ( $categories as $cat ) {
			echo '<option value="' . esc_attr( $cat->term_id ) . '"' . wc_selected( $cat->term_id, $category_ids ) . '>' . esc_html( $cat->name ) . '</option>';
		}
	}
	?>
				</select>
				<?php  echo wc_help_tip(esc_html__('Product categories that the coupon will not be applied to, or that cannot be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ), false) ; ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Individual use only', 'woocommerce'); ?>:</th>
			<td><input type="checkbox" name="<?php echo $option_name; ?>_options[ref_individual_use]" id="<?php echo $option_name; ?>_options[ref_individual_use]"  value="1"
				<?php echo checked( 1, $options['ref_individual_use'] ?? '', false ) ?? '' ; ?>>
				<?php
	echo wc_help_tip(esc_html__( 'Check this box if the coupon cannot be used in conjunction with other coupons.', 'woocommerce' )); ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Allow free shipping', 'woocommerce' ); ?>:</th>
			<td><input type="checkbox" name="<?php echo $option_name; ?>_options[ref_free_shipping]" id="<?php echo $option_name; ?>_options[ref_free_shipping]"  value="1"
				<?php echo checked( 1, $options['ref_free_shipping'] ?? '', false ) ?? '' ; ?>>
				<?php
	echo wc_help_tip(esc_html__( 'Check this box if the coupon grants free shipping. A <a href="%s" target="_blank">free shipping method</a> must be enabled in your shipping zone and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).', 'woocommerce' ), 'https://docs.woocommerce.com/document/free-shipping/'); ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Exclude discounted products', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="<?php echo $option_name; ?>_options[ref_exclude_discounted]" id="<?php echo $option_name; ?>_options[ref_exclude_discounted]"  value="1" <?php echo checked( 1, $options['ref_exclude_discounted'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(esc_html__('Check this box if the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are items in the cart that are not on sale.', 'woocommerce'  ), false) ; ?>

			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Coupon category', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="text" id="<?php echo $option_name; ?>_options[ref_coupon_cat]" name="<?php echo $option_name; ?>_options[ref_coupon_cat]"  style="width: 200px;" value="<?php echo $options['ref_coupon_cat'] ?? ''; ?>">
				<?php  echo wc_help_tip(esc_html__( 'This feature can be best used if the Advanced Coupons for WooCommerce plugin (free) is installed. Enter the name of the coupon category that will be created if it does not exist.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
	</table>
</div>	
	
	
	
<?php include('coupon-form.php'); ?>

</form>
<p>
<input type="button" value="<?php echo  esc_html__( 'Create a test', 'coupon-emails' ); ?>" class="button button-primary" 
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' .  $option_name . '_nonce_test' ) ); ?>" id="test_<?php echo $option_name; ?>_btn" />
</p>
</div>
</div>

