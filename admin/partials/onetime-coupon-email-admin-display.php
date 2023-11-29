<?php
namespace COUPONEMAILS;
global $wp_roles;
$option_name = "couponemails_onetimecoupon";

if ( isset( $_GET['runtest'] ) ) {
	$onetimes = new Coupon_Emails_Onetimes();
	$result = $onetimes->send_to_users_filtered($option_name);	
	header("location:admin.php?page=couponemails&tab=one-time"); 
}


if ( isset( $_GET['onetimecouponexport'] ) ) {
	global $wpdb;
	ob_end_clean();
	$table_head = array('Email', 'First Name', 'Last Name',  'Last activity', 'User ID', 'Orders count', 'Orders total', 'Last order' );
	$csv = implode( ';' , $table_head );
	$csv .= "\n";

	$onetimes = new Coupon_Emails_PrepareSQL($option_name, '<=');
	$result = $onetimes->get_users_filtered();
		
	foreach ( $result as $key => $value ) {
		$csv .=   implode(';', $value);
		$csv .= "\n";
	}
	$csv .= "\n";
	$filename = 'onetimecouponemail.csv';
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
	<div class="couponemails_loader"></div> </div>
	
	<ul class="subsubsub">
		<li>
			<a href="?page=couponemails&tab=one-time" class="current">
			<?php echo  esc_html__( 'Email with coupon', 'coupon-emails' ); ?></a>
			|</li>
		<li>
			<a href="?page=couponemails&tab=one-time&amp;section=no-coupon" class="">
				<?php echo  esc_html__( 'Email without coupon', 'coupon-emails' ); ?></a>
			|</li>
	</ul>	
	<input type="button" value="<?php echo  esc_html__( 'Restore Defaults', 'coupon-emails' ); ?>" class="button button-primary btn-restore"
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' .  $option_name . '_nonce' ) ); ?>"
id="restore_<?php echo $option_name; ?>_values_btn" />

<div class="icon32" id="icon-options-general"><br></div>
<br>
<h2><?php echo esc_html_x('One Time Coupon Emails Settings','Setting', 'coupon-emails'); ?> </h2>
<h4><?php echo esc_html_x('Use the settings in this section to filter users who will receive a one-time email with a coupon.','Setting', 'coupon-emails'); ?> </h4>
<form method="post" id="form_<?php echo $option_name; ?>" name="form_<?php echo $option_name; ?>" action="options.php">
	<?php
	settings_fields($option_name . '_plugin_options');
	$options = get_option($option_name . '_options');
	?>
	<table class="form-table">
		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Run in test mode', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="<?php echo $option_name; ?>_options[test]" id="<?php echo $option_name; ?>_options[test]"  value="1" <?php echo checked( 1, $options['test'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(esc_html__( 'Turn on when testing. The actual users will not get emails. All emails will be sent to BCC/Test address.', 'coupon-emails' ), false); ?>
				<button type="button" class="button button-primary" id="run_button" onClick="window.location.search += '&runtest=1'"><?php echo esc_html__( 'Run now', 'coupon-emails' ); ?></button>
				<input type="checkbox" style="display: none;" name="test_enabled" id="test_enabled"  value="1" <?php echo checked( 1, $options['test'] ?? '', false ) ?? '' ; ?>>	<?php  echo wc_help_tip(sprintf(_n( 'If you want to run a test, check the chekbox and save. After pushing this button maximum %s coupon will be created and email sent to administrator.', 'If you want to run a test, check the chekbox and save. After pushing this button maximum %s coupons will be created and test emails sent to administrator.', COUPON_EMAILS_MAX_TEST_EMAILS, 'coupon-emails' ), number_format_i18n(COUPON_EMAILS_MAX_TEST_EMAILS)), false); ?>			
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Send even if there is no user name in the database', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="<?php echo $option_name; ?>_options[with_no_name]" id="<?php echo $option_name; ?>_options[with_no_name]"  value="1" <?php echo checked( 1, $options['with_no_name'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(esc_html__( 'If this box is unchecked, only users who have fully completed registration and whose name is in the database will receive the email.', 'coupon-emails' ), false); ?>
			</td>
		</tr>		
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'User Roles to include', 'coupon-emails' ); ?>:</th>
		<td>
			<select id="<?php echo $option_name; ?>_options[roles]" name="<?php echo $option_name; ?>_options[roles][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'All roles', 'coupon-emails' ); ?>">
				<?php
		$role_ids = $options['roles'];
		$roles    = $wp_roles->get_names();
		if ( $roles  ) {
			foreach ( $roles  as $key => $value ) {
				echo '<option value="' . esc_attr( $key ) . '"' . wc_selected( $key, $role_ids ) . '>' . esc_html( $value ) . '</option>';
			}
		}
		?>
			</select>
			<?php  echo wc_help_tip(esc_html__(  'Select user roles to send the emails to.', 'coupon-emails'), false) ; ?>
		</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'User Roles to exclude from sending', 'coupon-emails' ); ?>:</th>
			<td>
				<select id="<?php echo $option_name; ?>_options[exclude-roles]" name="<?php echo $option_name; ?>_options[exclude-roles][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No roles', 'coupon-emails' ); ?>">
					<?php
		$role_ids = isset( $options['exclude-roles']) ? $options['exclude-roles'] : "";
		$roles    = $wp_roles->get_names();
		if ( $roles  ) {
			foreach ( $roles  as $key => $value ) {
				echo '<option value="' . esc_attr( $key ) . '"' . wc_selected( $key, $role_ids ) . '>' . esc_html( $value ) . '</option>';
			}
		}
		?>
				</select>
				<?php  echo wc_help_tip(esc_html__(  'Select user roles to exclude from sending the emails.', 'coupon-emails'), false) ; ?>
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
			<th class="titledesc"><?php echo esc_html__( 'Minimum orders made by a user', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="number" id="<?php echo $option_name; ?>_options[minimum_orders]" name="<?php echo $option_name; ?>_options[minimum_orders]"  style="width: 80px;" value="<?php echo $options['minimum_orders'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( 'This field allows you to set the minimum number of orders that the customer has placed. If the field is empty, all users will be selected, even if they have never ordered. If this field is 0, only users who have no orders will be selected, regardless of the selections in the product and category filters.', 'coupon-emails'), false); ?>
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
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Last order made more than X days before today', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="number" id="<?php echo $option_name; ?>_options[days_after_order]" name="<?php echo $option_name; ?>_options[days_after_order]"  style="width: 80px;" value="<?php echo $options['days_after_order'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( "Enter the minimum number of days since customer's last order after which this coupon email should be sent.", 'coupon-emails'), false); ?>
			</td>
		</tr>	
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Last user activity made more than X days before today', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="number" id="<?php echo $option_name; ?>_options[days_after_active]" name="<?php echo $option_name; ?>_options[days_after_active]"  style="width: 80px;" value="<?php echo $options['days_after_active'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( "Enter the minimum number of days since customer's last activity after which this coupon email should be sent.", 'coupon-emails'), false); ?>
			</td>
		</tr>			
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Send email only to these addresses', 'coupon-emails' ); ?>:</th>
			<td>
				<textarea  style="width: 500px;" id="<?php echo $option_name; ?>_options[email_address]" name="<?php echo $option_name; ?>_options[email_address]" rows="3" type='textarea'><?php echo $options['email_address'] ?? ''; ?></textarea>
				<?php  echo wc_help_tip(esc_html__( 'Add multiple emails separated by comma ( , ).', 'coupon-emails' ) . ' ' . esc_html__( 'Settings of other filters will be ignored if not empty.', 'coupon-emails'  ), false); ?>
			</td>
		</tr>		
		<tr>
			<th class="titledesc"><?php echo esc_html__('Download file with users to send email', 'coupon-emails' ); ?>:</th>
			<td>
				<a class="button button-primary" href="admin.php?page=couponemails&tab=one-time&onetimecouponexport=table&noheader=1"><?php echo esc_html__( 'Download csv', 'coupon-emails' ); ?></a>
				<?php  echo wc_help_tip(esc_html__( 'Download csv file with selected users.', 'coupon-emails' ), false); ?>
			</td>
		</tr>				
	</table>	
	
	<?php include('coupon-form.php'); ?>
		
</form>
<p>
	<input type="button" value="<?php echo  esc_html__( 'Create a test', 'coupon-emails' ); ?>" class="button button-primary" 
	attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' .  $option_name . '_nonce_test' ) ); ?>" id="test_<?php echo $option_name; ?>_btn" />
<input type="button" value="<?php echo  esc_html__( 'Send emails', 'coupon-emails' ); ?>" class="button button-primary" 
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' . $option_name . '_nonce_send' ) ); ?>" id="send_btn" />
</p>
</div>
</div>
