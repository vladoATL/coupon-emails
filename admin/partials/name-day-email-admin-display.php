<?php
namespace COUPONEMAILS;
/**
* Provide a admin area view for the plugin
*
* This file is used to markup the admin-facing aspects of the plugin.
*
* @link       https://perties.sk
* @since      1.0.0
*
* @package    Coupon_Emails
* @subpackage Coupon_Emails/admin/partials
*/

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
			$table_body = $namedays->get_hungarian_namedays_array();
			break;
		case 4:
			$table_body = $namedays->get_austrian_namedays_array();
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

?>

<div class="wrap woocommerce">
<div id="namedaysemail-setting"  class="myday-setting">
<div class="loader_cover">
	<div class="namedays_loader"></div> </div>
<input type="button" value="<?php echo  __( 'Restore Defaults', 'coupon-emails' ); ?>" class="button button-primary"
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_namedayemail_nonce' ) ); ?>"
id="restore_values_btn" />

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
				<option value='3' <?php selected( $options['language'] ?? '', 3 ); ?>><?php echo __( 'Hungarian calendar', 'coupon-emails' ); ?>&nbsp;</option>
				<option value='4' <?php selected( $options['language'] ?? '', 4 ); ?>><?php echo __( 'Austrian calendar', 'coupon-emails' ); ?>&nbsp;</option>
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
<h3><?php echo __('Coupon settings', 'coupon-emails'); ?> </h3>
<table id="coupon-table" class="form-table">
	<tr>
		<th class="titledesc"><?php echo __( 'Description', 'woocommerce' ); ?>:</th>
		<td>
			<input type="text" id="namedayemail_options[description]" name="namedayemail_options[description]"  style="width: 500px;" value="<?php echo $options['description'] ?? ''; ?>"</input>
			<?php  echo wc_help_tip(__( 'Description will be used on Coupons page.', 'coupon-emails' ), false); ?>
		</td>
	</tr>
	<tr>
		<th class="titledesc"><?php echo __( 'Count of coupon characters', 'coupon-emails' ); ?>:</th>
		<td>
			<select name='namedayemail_options[characters]' style="width: 200px;">
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
			<select name='namedayemail_options[disc_type]' style="width: 200px;">
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
			<input type="number" id="namedayemail_options[coupon_amount]" name="namedayemail_options[coupon_amount]"  style="width: 60px;" value="<?php echo $options['coupon_amount'] ?? ''; ?>"</input>
			<?php  echo wc_help_tip(__(  'Value of the coupon.', 'woocommerce' ), false); ?>
		</td>
	</tr>
	<tr>
		<th class="titledesc"><?php echo __( 'Minimum spend', 'woocommerce' ); ?>:</th>
		<td>
			<input type="number" id="namedayemail_options[minimum_amount]" name="namedayemail_options[minimum_amount]"  style="width: 60px;" value="<?php echo $options['minimum_amount'] ?? ''; ?>"</input>
			<?php  echo wc_help_tip(__( 'This field allows you to set the minimum spend (subtotal) allowed to use the coupon.', 'woocommerce'), false); ?>
		</td>
	</tr>
	<tr>
		<th class="titledesc"><?php echo __( 'Maximum spend', 'woocommerce' ); ?>:</th>
		<td>
			<input type="number" id="namedayemail_options[maximum_amount]" name="namedayemail_options[maximum_amount]"  style="width: 60px;" value="<?php echo $options['maximum_amount'] ?? ''; ?>"</input>
			<?php  echo wc_help_tip(__( 'This field allows you to set the maximum spend (subtotal) allowed to use the coupon.', 'woocommerce'  ), false); ?>
		</td>
	</tr>
	<tr>
		<th class="titledesc"><?php echo __( 'Coupon expires in days', 'coupon-emails' ); ?>:</th>
		<td>
			<input type="number" id="namedayemail_options[expires]" name="namedayemail_options[expires]"  style="width: 60px;" value="<?php echo $options['expires'] ?? ''; ?>"</input>
			<?php  echo wc_help_tip(__( 'Leave this field blank if unexpired coupons are to be created.', 'coupon-emails' ), false); ?>
		</td>
	</tr>
	<tr>
		<th class="titledesc"><?php echo __( 'Limit usage to X items', 'woocommerce' ); ?>:</th>
		<td>
			<input type="number" id="namedayemail_options[max_products]" name="namedayemail_options[max_products]"  style="width: 60px;" value="<?php echo $options['max_products'] ?? ''; ?>"</input>
			<?php  echo wc_help_tip(__( 'The generated coupon can be used for a maximum number of products. For unlimited use, leave blank.', 'coupon-emails' ), false); ?>
		</td>
	</tr>
	<tr>
		<th class="titledesc"><?php echo __( 'Good only for products', 'coupon-emails' ); ?>:</th>
		<td>
			<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="namedayemail_options[only_products]" name="namedayemail_options[only_products][]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" >
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
			<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="namedayemail_options[exclude_prods]" name="namedayemail_options[exclude_prods][]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" >
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
			<select id="namedayemail_options[only_cats]" name="namedayemail_options[only_cats][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No categories', 'woocommerce' ); ?>">
				<?php
				$category_ids = $options['only_cats'];
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
			<select id="namedayemail_options[exclude_cats]" name="namedayemail_options[exclude_cats][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No categories', 'woocommerce' ); ?>">
				<?php
				$category_ids = $options['exclude_cats'];
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
		<td><input type="checkbox" name="namedayemail_options[individual_use]" id="namedayemail_options[individual_use]"  value="1"
			<?php echo checked( 1, $options['individual_use'] ?? '', false ) ?? '' ; ?>>
			<?php
			echo wc_help_tip(__( 'Check this box if the coupon cannot be used in conjunction with other coupons.', 'woocommerce' )); ?>
		</td>
	</tr>
	<tr valign="top">
		<th class="titledesc"><?php echo __( 'Allow free shipping', 'woocommerce' ); ?>:</th>
		<td><input type="checkbox" name="namedayemail_options[free_shipping]" id="namedayemail_options[free_shipping]"  value="1"
			<?php echo checked( 1, $options['free_shipping'] ?? '', false ) ?? '' ; ?>>
			<?php
			echo wc_help_tip(__( 'Check this box if the coupon grants free shipping. A <a href="%s" target="_blank">free shipping method</a> must be enabled in your shipping zone and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).', 'woocommerce' ), 'https://docs.woocommerce.com/document/free-shipping/'); ?>
		</td>
	</tr>
	<tr valign="top">
		<th class="titledesc"><?php echo __( 'Exclude discounted products', 'coupon-emails' ); ?>:</th>
		<td><input type="checkbox" name="namedayemail_options[exclude_discounted]" id="namedayemail_options[exclude_discounted]"  value="1" <?php echo checked( 1, $options['exclude_discounted'] ?? '', false ) ?? '' ; ?>>
			<?php  echo wc_help_tip(__('Check this box if the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are items in the cart that are not on sale.', 'woocommerce'  ), false) ; ?>

		</td>
	</tr>
	<tr>
		<th class="titledesc"><?php echo __( 'Coupon category slug', 'coupon-emails' ); ?>:</th>
		<td>
			<?php
			$acfw ="";
			if ( ! is_plugin_active( 'advanced-coupons-for-woocommerce-free/advanced-coupons-for-woocommerce-free.php' ) ) {
				$acfw = 'disabled';
			}
			?>
			<input type="text" id="namedayemail_options[category]" name="namedayemail_options[category]"  style="width: 200px;" value="<?php echo $options['category'] ?? ''; ?>"
			<?php echo $acfw; ?>>
			<?php  echo wc_help_tip(__( 'This can only be used if the Advanced Coupons for WooCommerce (free) plugin is installed. Specify the slug of the coupon category that must exist.', 'coupon-emails' ), false); ?>
		</td>
	</tr>

</table>

<h3><?php echo _x('Email message setting','Setting section', 'coupon-emails'); ?> </h3>
<table id="email-table" class="form-table">
	<tr>
		<th class="titledesc"><?php echo __( 'Email from name', 'coupon-emails' ); ?>:</th>
		<td>
			<input type="text" id="namedayemail_options[from_name]" name="namedayemail_options[from_name]"  style="width: 200px;" value="<?php echo $options['from_name'] ?? ''; ?>"</input>
		</td>
	</tr>
	<tr>
		<th class="titledesc"><?php echo __( 'Email from address', 'coupon-emails' ); ?>:</th>
		<td>
			<input type="email" id="namedayemail_options[from_address]" name="namedayemail_options[from_address]"  style="width: 200px;" value="<?php echo $options['from_address'] ?? ''; ?>"</input>

		</td>
	</tr>
	<tr>
		<th class="titledesc"><?php echo __( 'Email CC', 'coupon-emails' ); ?>:</th>
		<td>
			<input type="email" id="namedayemail_options[cc_address]" name="namedayemail_options[cc_address]"  style="width: 200px;" value="<?php echo $options['cc_address'] ?? ''; ?>"</input>
			<?php  echo wc_help_tip(__( 'Add multiple emails separated by comma ( , ).', 'coupon-emails' ), false); ?>
		</td>
	</tr>
	<tr>
		<th scope="row" class="titledesc">
			<?php echo __( 'BCC and Test email address', 'coupon-emails' ); ?>:
		</th>
		<td>
			<input type="email" id="namedayemail_options[bcc_address]" name="namedayemail_options[bcc_address]"  style="width: 200px;" value="<?php echo $options['bcc_address'] ?? ''; ?>"</input>
			<?php  echo wc_help_tip(__( 'This email address is used for testing as well as for all email messages as a blind copy address.', 'coupon-emails' ) . ' ' .  __( 'Add multiple emails separated by comma ( , ).', 'coupon-emails' ), false); ?>
		</td>
	</tr>
	<tr valign="top">
		<th class="titledesc"><?php echo __( 'Use WooCommerce email template', 'coupon-emails' ); ?>:</th>
		<td><input type="checkbox" name="namedayemail_options[wc_template]" id="namedayemail_options[wc_template]"  value="1" <?php echo checked( 1, $options['wc_template'] ?? '', false ) ?? '' ; ?>>
			<?php  echo wc_help_tip(__( 'Turn this on if you want your email to look just like a regular WooCommerce email.', 'coupon-emails' ), false); ?>
		</td>
	</tr>
	<tr>
		<th class="titledesc"><?php echo __( 'Email subject', 'coupon-emails' ); ?>:</th>
		<td>
			<input type="text" id="namedayemail_options[subject]" name="namedayemail_options[subject]"  style="width: 500px;" value="<?php echo $options['subject'] ?? ''; ?>"</input>
		</td>
	</tr>
	<tr>
		<th class="titledesc"><?php echo __( 'Email header', 'coupon-emails' ); ?>:</th>
		<td>
			<input type="text" id="namedayemail_options[header]" name="namedayemail_options[header]"  style="width: 500px;" value="<?php echo $options['header'] ?? ''; ?>"</input>
			<?php echo wc_help_tip(__( 'This is short text on the top of the email.', 'coupon-emails' ), false); ?>
		</td>
	</tr>
	<th class="titledesc" style="padding-bottom: 0px;"><?php echo __( 'Email body', 'coupon-emails' ); ?> :</th>
	<tr>
		<td colspan="2">
			<?php
			$args = array("textarea_name" => "namedayemail_options[email_body]", 'editor_class' => 'textarea_');
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
			<input type="button" value="<?php echo  __( 'Create a test', 'coupon-emails' ); ?>" class="button button-primary" 
			attr-nonce="<?php echo esc_attr( wp_create_nonce( '_namedayemail_nonce_test' ) ); ?>" id="test_namedayemail_btn" />		
			
	</div>
	</div>