<?php
namespace COUPONEMAILS;
global $wp_roles;
$option_name = "couponemails_reviewed";
?>

<div class="wrap woocommerce">
	<div id="<?php echo esc_html($option_name); ?>-setting"  class="coupon-emails-setting">
<div class="couponemails_loader_cover">
	<div class="couponemails_loader"></div> </div>
	<input type="button" value="<?php echo  esc_html__( 'Restore Defaults', 'coupon-emails' ); ?>" class="button button-primary btn-restore"
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' . $option_name . '_nonce' ) ); ?>"
id="restore_<?php echo $option_name; ?>_values_btn" />

<div class="icon32" id="icon-options-general"><br></div>
<h2><?php echo esc_html_x('Emails after Reviewed Settings','Setting', 'coupon-emails'); ?> </h2>
<h4><?php echo esc_html_x('This section automatically generates thank you coupon emails to users after their review on this website.','Setting', 'coupon-emails'); ?> </h4>

<form method="post" id="form_<?php echo $option_name; ?>" name="form_<?php echo $option_name; ?>" action="options.php">

<?php
settings_fields( $option_name .'_plugin_options');
$options = get_option( $option_name .'_options');
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
			<?php  echo wc_help_tip(esc_html__( 'Turn on when testing. The actual users will not get emails. All emails will be sent to BCC/Test address.', 'coupon-emails' ), false); ?>
		</td>
	</tr>
	<tr valign="top">
		<th class="titledesc"><?php echo esc_html__( 'Reviews only with minimum stars', 'coupon-emails' ); ?>:</th>
		<td>
			<select name='<?php echo $option_name; ?>_options[stars]' style="width: 200px;">
				<option value='0' <?php selected( $options['stars'] ?? '', 0 ); ?>><?php echo esc_html__( 'All reviews', 'coupon-emails' ); ?>&nbsp;</option>
				<option value='5' <?php selected( $options['stars'] ?? '', 5 ); ?>><?php echo '★★★★★'; ?>&nbsp;</option>
				<option value='4' <?php selected( $options['stars'] ?? '', 4 ); ?>><?php echo '★★★★☆'; ?>&nbsp;</option>
				<option value='3' <?php selected( $options['stars'] ?? '', 3 ); ?>><?php echo '★★★☆☆'; ?>&nbsp;</option>
				<option value='2' <?php selected( $options['stars'] ?? '', 2 ); ?>><?php echo '★★☆☆☆'; ?>&nbsp;</option>
				<option value='1' <?php selected( $options['stars'] ?? '', 1 ); ?>><?php echo '★☆☆☆☆'; ?>&nbsp;</option>
			</select>
			<?php  echo wc_help_tip(esc_html__( 'Set the minimum stars number of the review when the emails should be sent.', 'coupon-emails' ), false); ?>
		</td>
	</tr>
	<tr>
		<th class="titledesc"><?php echo esc_html__( 'User Roles to include', 'coupon-emails' ); ?>:</th>
		<td>
			<select id="<?php echo $option_name; ?>_options[roles]" name="<?php echo $option_name; ?>_options[roles][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'All roles', 'coupon-emails' ); ?>">
				<?php
				$role_ids = isset($options['roles']) ? $options['roles'] : "";				
				$roles    = $wp_roles->get_names();
				if ( $roles  ) {
					foreach ( $roles  as $key => $value ) {
						echo '<option value="' . esc_attr( $key ) . '"' . wc_selected( $key, $role_ids ) . '>' . esc_html( $value ) . '</option>';
					}
				}
				?>
			</select>
			<?php  echo wc_help_tip(esc_html__( 'Select user roles to send the emails to.', 'coupon-emails'), false) ; ?>
		</td>
	</tr>
	<tr>
		<th class="titledesc"><?php echo esc_html__( 'User Roles to exclude from sending', 'coupon-emails' ); ?>:</th>
		<td>
			<select id="<?php echo $option_name; ?>_options[exclude-roles]" name="<?php echo $option_name; ?>_options[exclude-roles][]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No roles', 'coupon-emails' ); ?>">
				<?php
				$role_ids = isset($options['exclude-roles']) ? $options['exclude-roles'] : "";	
				$roles    = $wp_roles->get_names();
				if ( $roles  ) {
					foreach ( $roles  as $key => $value ) {
						echo '<option value="' . esc_attr( $key ) . '"' . wc_selected( $key, $role_ids ) . '>' . esc_html( $value ) . '</option>';
					}
				}
				?>
			</select>
			<?php  echo wc_help_tip(esc_html__( 'Select user roles to exclude from sending the emails.', 'coupon-emails'), false) ; ?>
		</td>
	</tr>
	<tr>
		<th class="titledesc"><?php echo esc_html__( 'Users who reviewed one of these products', 'coupon-emails' ); ?>:</th>
		<td>
			<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="<?php echo $option_name; ?>_options[bought_products]" name="<?php echo $option_name; ?>_options[bought_products][]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products" >
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
			<?php  echo wc_help_tip(esc_html__( 'The email will be sent to users who have reviewed at least one of the selected products. Select the main product if you want it to include all variants.', 'coupon-emails' ), false); ?>
		</td>
	</tr>
	<tr>
		<th class="titledesc"><?php echo esc_html__( 'Users who reviewed products in these categories', 'coupon-emails' ); ?>:</th>
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
			<?php  echo wc_help_tip(esc_html__(  'The email will be sent to users who have reviewed any product in the selected categories.', 'coupon-emails'), false) ; ?>
		</td>
	</tr>
</table>

<?php include('coupon-form.php'); ?>	

</form>
<p>
	<input type="button" value="<?php echo  esc_html__( 'Create a test', 'coupon-emails' ); ?>" class="button button-primary" 
	attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' .  $option_name . '_nonce_test' ) ); ?>" id="test_<?php echo $option_name; ?>_btn" />
</p>
</div>
</div>