<?php

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

?>

<div class="wrap woocommerce">
	<div id="namedaysemail-setting"  class="myday-setting">

<div class="icon32" id="icon-options-general"><br></div>
<h2><?php echo _x('Common Settings','Setting', 'coupon-emails'); ?> </h2>
<form method="post" id="form1" name="form1" action="options.php">
<?php
settings_fields('couponemails_plugin_options');
$options = get_option('couponemails_options');

?>

<table class="form-table">
<tr>
	<th class="titledesc"><?php echo __( 'Delete unused coupons in days after expiration', 'coupon-emails' ); ?>:</th>
	<td>
		<input type="number" id="couponemails_options[days_delete]" name="couponemails_options[days_delete]"  style="width: 60px;" value="<?php echo $options['days_delete'] ?? ''; ?>"</input>
		<?php  echo wc_help_tip(__( 'If you leave this blank, the coupons will not be deleted. To automatically delete coupons, enter the number of days after the expiration date and unused coupons will be deleted.', 'coupon-emails' ), false); ?>
	</td>
</tr>
<tr valign="top">
	<th scope="row" class="titledesc"><?php echo __( 'Enable logs', 'coupon-emails' ); ?>:</th>
	<td><input type="checkbox" name="couponemails_options[enable_logs]" id="couponemails_options[enable_logs]"  value="1" <?php echo checked( 1, $options['enable_logs'] ?? '', false ) ?? '' ; ?>></td>
</tr>
<tr valign="top">
	<th scope="row" class="titledesc"><?php echo __( 'Enable SQL log', 'coupon-emails' ); ?>:</th>
	<td><input type="checkbox" name="couponemails_options[enable_sql_logs]" id="couponemails_options[enable_sql_logs]"  value="1" <?php echo checked( 1, $options['enable_sql_logs'] ?? '', false ) ?? '' ; ?>></td>
</tr>
</table>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>	
		<form method="post" id="form_log" name="form_log">
			<?php 
				settings_fields('couponemails_plugin_log_options'); 
				$options = get_option('couponemails_logs'); 
			?>	
			<h3><?php echo _x('Logs','Setting section', 'coupon-emails'); ?> </h3>
			<table id="log-table" class="form-table">	
				<tr>
					<td colspan="2" class="textarea_">						
						<textarea class="textarea_" id="couponemails_logs[logs]" name="couponemails_logs[logs]" rows="25" type='textarea'><?php echo $options['logs'] ?? ''; ?></textarea>
					</td>
				</tr>			
			</table>
			<p class="submit">
			<input type="button" value="<?php echo  __( 'Clear Log', 'coupon-emails' ); ?>" class="button button-primary" 
			attr-nonce="<?php echo esc_attr( wp_create_nonce( '_couponemails_nonce_log' ) ); ?>" 
			id="clear_log_btn" />						
			</p>
			
		</form>
	</div>
	</div>
