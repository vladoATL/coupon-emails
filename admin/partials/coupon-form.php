<?php
$default_tab = null;
$tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : $default_tab;
switch ($tab) :
	case 'referral':
	$coupon_description =  esc_html__('Referee Coupon settings', 'coupon-emails');
		break;	
	default:
		$coupon_description =  esc_html__('Coupon settings', 'coupon-emails');
		break;
endswitch
?>
<div class="coupon_setting">
	<h3><?php echo $coupon_description; ?> </h3>
	<table id="coupon-table" class="form-table">
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Description', 'woocommerce' ); ?>:</th>
			<td>
				<input type="text" id="<?php echo $option_name; ?>_options[description]" name="<?php echo $option_name; ?>_options[description]"  style="width: 500px;" value="<?php echo $options['description'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( 'Description will be used on Coupons page.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Count of coupon characters', 'coupon-emails' ); ?>:</th>
			<td>
				<select name='<?php echo $option_name; ?>_options[characters]' style="width: 200px;">
					<option value='5' <?php selected( $options['characters'] ?? '', 5 ); ?>><?php echo '5 ' . esc_html__( 'characters', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='6' <?php selected( $options['characters'] ?? '', 6 ); ?>><?php echo '6 ' . esc_html__( 'characters', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='7' <?php selected( $options['characters'] ?? '', 7 ); ?>><?php echo '7 ' . esc_html__( 'characters', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='8' <?php selected( $options['characters'] ?? '', 8 ); ?>><?php echo '8 ' . esc_html__( 'characters', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='9' <?php selected( $options['characters'] ?? '', 9 ); ?>><?php echo '9 ' . esc_html__( 'characters', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='10' <?php selected( $options['characters'] ?? '', 10 ); ?>><?php echo '10 ' . esc_html__( 'characters', 'coupon-emails' ); ?>&nbsp;</option>
					<option value='0' <?php selected( $options['characters'] ?? '', 0 ); ?>><?php echo  esc_html__( "Don't generate coupon", 'coupon-emails' ); ?>&nbsp;</option>
				</select>
				<?php  echo wc_help_tip(esc_html__( 'Select how many characters the generated coupons should consist of.', 'coupon-emails' ), false); ?>
			</td>
		</tr>

		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Discount type', 'woocommerce' ); ?>:</th>
			<td>
				<select name='<?php echo $option_name; ?>_options[disc_type]' style="width: 200px;">
					<option value='1' <?php selected( $options['disc_type'] ?? '', 1 ); ?>><?php echo esc_html__( 'Percentage discount', 'woocommerce' ); ?>&nbsp;</option>
					<option value='2' <?php selected( $options['disc_type'] ?? '', 2 ); ?>><?php echo esc_html__( 'Fixed cart discount', 'woocommerce' ); ?>&nbsp;</option>
					<option value='3' <?php selected( $options['disc_type'] ?? '', 3 ); ?>><?php echo esc_html__( 'Fixed product discount', 'woocommerce' ); ?>&nbsp;</option>
				</select>
				<?php  echo wc_help_tip(esc_html__( 'Set the discount type.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Coupon amount', 'woocommerce' ); ?>:</th>
			<td>
				<input type="number" id="<?php echo $option_name; ?>_options[coupon_amount]" name="<?php echo $option_name; ?>_options[coupon_amount]"  style="width: 80px;" value="<?php echo $options['coupon_amount'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__(  'Value of the coupon.', 'woocommerce' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Minimum order value', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="number" id="<?php echo $option_name; ?>_options[minimum_amount]" name="<?php echo $option_name; ?>_options[minimum_amount]"  style="width: 80px;" value="<?php echo $options['minimum_amount'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( 'This field allows you to set the minimum spend (subtotal) allowed to use the coupon.', 'woocommerce'), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Maximum order value', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="number" id="<?php echo $option_name; ?>_options[maximum_amount]" name="<?php echo $option_name; ?>_options[maximum_amount]"  style="width: 80px;" value="<?php echo $options['maximum_amount'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( 'This field allows you to set the maximum spend (subtotal) allowed to use the coupon.', 'woocommerce'  ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Coupon expires in days', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="number" id="<?php echo $option_name; ?>_options[expires]" name="<?php echo $option_name; ?>_options[expires]"  style="width: 80px;" value="<?php echo $options['expires'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( 'Leave this field blank if unexpired coupons are to be created.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Limit usage to X items', 'woocommerce' ); ?>:</th>
			<td>
				<input type="number" id="<?php echo $option_name; ?>_options[max_products]" name="<?php echo $option_name; ?>_options[max_products]"  style="width: 80px;" value="<?php echo $options['max_products'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( 'The generated coupon can be used for a maximum number of products. For unlimited use, leave blank.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Good only for products', 'coupon-emails' ); ?>:</th>
			<td>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="<?php echo $option_name; ?>_options[only_products]" name="<?php echo $option_name; ?>_options[only_products][]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" >
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
				<?php  echo wc_help_tip(esc_html__( 'Products that the coupon will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Exclude products', 'woocommerce' ); ?>:</th>
			<td>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="<?php echo $option_name; ?>_options[exclude_prods]" name="<?php echo $option_name; ?>_options[exclude_prods][]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" >
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
				<?php  echo wc_help_tip(esc_html__( 'Products that the coupon will not be applied to, or that cannot be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ), false) ; ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Product categories', 'woocommerce' ); ?>:</th>
			<td>
				<select id="<?php echo $option_name; ?>_options[only_cats]" name="<?php echo $option_name; ?>_options[only_cats][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No categories', 'woocommerce' ); ?>">
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
				<?php  echo wc_help_tip(esc_html__(  'Product categories that the coupon will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce'), false) ; ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Exclude categories', 'woocommerce' ); ?>:</th>
			<td>
				<select id="<?php echo $option_name; ?>_options[exclude_cats]" name="<?php echo $option_name; ?>_options[exclude_cats][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No categories', 'woocommerce' ); ?>">
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
				<?php  echo wc_help_tip(esc_html__('Product categories that the coupon will not be applied to, or that cannot be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ), false) ; ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Individual use only', 'woocommerce'); ?>:</th>
			<td><input type="checkbox" name="<?php echo $option_name; ?>_options[individual_use]" id="<?php echo $option_name; ?>_options[individual_use]"  value="1"
				<?php echo checked( 1, $options['individual_use'] ?? '', false ) ?? '' ; ?>>
				<?php
				echo wc_help_tip(esc_html__( 'Check this box if the coupon cannot be used in conjunction with other coupons.', 'woocommerce' )); ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Allow free shipping', 'woocommerce' ); ?>:</th>
			<td><input type="checkbox" name="<?php echo $option_name; ?>_options[free_shipping]" id="<?php echo $option_name; ?>_options[free_shipping]"  value="1"
				<?php echo checked( 1, $options['free_shipping'] ?? '', false ) ?? '' ; ?>>
				<?php
				echo wc_help_tip(esc_html__( 'Check this box if the coupon grants free shipping. A <a href="%s" target="_blank">free shipping method</a> must be enabled in your shipping zone and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).', 'woocommerce' ), 'https://docs.woocommerce.com/document/free-shipping/'); ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Exclude discounted products', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="<?php echo $option_name; ?>_options[exclude_discounted]" id="<?php echo $option_name; ?>_options[exclude_discounted]"  value="1" <?php echo checked( 1, $options['exclude_discounted'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(esc_html__('Check this box if the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are items in the cart that are not on sale.', 'woocommerce'  ), false) ; ?>

			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Coupon category', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="text" id="<?php echo $option_name; ?>_options[coupon_cat]" name="<?php echo $option_name; ?>_options[coupon_cat]"  style="width: 200px;" value="<?php echo $options['coupon_cat'] ?? ''; ?>">
				<?php  echo wc_help_tip(esc_html__( 'This feature can be best used if the Advanced Coupons for WooCommerce plugin (free) is installed. Enter the name of the coupon category that will be created if it does not exist.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
	</table>
</div>	

<?php include('email-table.php'); ?>

