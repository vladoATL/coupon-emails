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
$option_name = "couponemails";

$fun = new COUPONEMAILS\Coupon_Emails_EmailFunctions();
if ( isset( $_GET['deleteexpired'] ) ) {
	$fun->couponemails_delete_expired();	
}
	$stats = $fun->couponemails_get_stats();
	//$stats = $fun->couponemails_get_full_stats();
	$display_stats = true;
	$total = 0;
	$active = 0;
	$expired = 0;
	$used = 0;	
?>

<div class="wrap woocommerce">
	<div id="coupon-emails-setting"  class="coupon-emails-setting">
		<div class="coupon_emails_loader_cover">
			<div class="coupon_emails_loader"></div>
	</div>
<div class="icon32" id="icon-options-general"><br></div>
<br>
<h2><?php echo esc_html_x('Common Settings','Setting', 'coupon-emails'); ?> </h2>
<form method="post" id="form1" name="form1" action="options.php">
<?php
settings_fields($option_name . '_plugin_options');
$options = get_option($option_name . '_options');
?>
<input type="hidden" id="<?php echo $option_name; ?>_options[install_date]" name="<?php echo $option_name; ?>_options[install_date]" value="<?php echo $options['install_date'] ?? ''; ?>"</input>
<div class="couponemails_wrapper">
	<div class="couponemails_left">
	<table class="form-table">
	<tr>
		<th class="titledesc"><?php echo esc_html__( 'Delete unused coupons in days after expiration', 'coupon-emails' ); ?>:</th>
		<td>
			<input type="number" id="<?php echo $option_name; ?>_options[days_delete]" name="<?php echo $option_name; ?>_options[days_delete]"  style="width: 60px;" value="<?php echo $options['days_delete'] ?? ''; ?>"</input>
			<?php  echo wc_help_tip(esc_html__( 'If you leave this blank, the coupons will not be deleted. To automatically delete coupons, enter the number of days after the expiration date and unused coupons will be deleted.', 'coupon-emails' ), false); ?>
			<a class="button button-primary" href="admin.php?page=couponemails&deleteexpired=true"><?php echo esc_html__( 'Delete now', 'coupon-emails' ); ?></a>			
		</td>
	</tr>
	<tr valign="top">
		<th scope="row" class="titledesc"><?php echo esc_html__( 'Enable logs', 'coupon-emails' ); ?>:</th>
		<td><input type="checkbox" name="<?php echo $option_name; ?>_options[enable_logs]" id="<?php echo $option_name; ?>_options[enable_logs]"  value="1" <?php echo checked( 1, $options['enable_logs'] ?? '', false ) ?? '' ; ?>>			
		</td>
	</tr>
	<tr valign="top">
		<th scope="row" class="titledesc"><?php echo esc_html__( 'Show coupons on My Account page', 'coupon-emails' ); ?>:</th>
		<td><input type="checkbox" name="<?php echo $option_name; ?>_options[show_account_coupons]" id="<?php echo $option_name; ?>_options[show_account_coupons]"  value="1" <?php echo checked( 1, $options['show_account_coupons'] ?? '', false ) ?? '' ; ?>>
		</td>
	</tr>		
	<tr>
		<td>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>		
		</td>
	</tr>
	</table>
</div>
<div  class="couponemails_right">
	<h2><?php echo esc_html_x('Statistics','Setting', 'coupon-emails'); ?> </h2>
	<table class="widefat striped" width="100%">
		<thead>
			<tr>
				<th width="20%" ><?php echo esc_html__( 'Coupons category', 'coupon-emails' ); ?></th>
				<th width="20%" class="column-posts title-center"><?php echo esc_html__( 'Total coupons', 'coupon-emails' ); ?></th>				
				<th width="20%" class="column-posts title-center"><?php echo esc_html__( 'Used coupons', 'coupon-emails' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($stats as $row){
				?>
			<tr>
				<td><?php echo $row->name; ?></td>
				<td class="manage-column num"><?php echo $row->total_count; $total += $row->total_count; ?></td>			
				<td class="manage-column num"><?php echo $row->used_count; $used += $row->used_count; ?></td>				
			</tr>
			<?php } 				?>
		</tbody>
		<tfoot>
			<tr>
				<td><?php echo esc_html__( 'Total', 'coupon-emails' ); ?></td>
				<td class="manage-column num"><?php echo $total; ?></td>
				<td class="manage-column num"><?php echo $used; ?></td>				
			</tr>
		</tfoot>
	</table>	
</div>
</div>

</form>	
		<form method="post" id="form_log" name="form_log">
			<?php 
			settings_fields($option_name . '_plugin_log_options'); 
			$options = get_option($option_name . '_logs'); 
			?>	
			<h3><?php echo esc_html_x('Logs','Setting section', 'coupon-emails'); ?> </h3>
			<table id="log-table" class="form-table">	
				<tr>
					<td colspan="2" class="textarea_">						
						<textarea class="textarea_" id="<?php echo $option_name; ?>_logs[logs]" name="<?php echo $option_name; ?>_logs[logs]" rows="25" type='textarea'><?php echo $options['logs'] ?? ''; ?></textarea>
					</td>
				</tr>			
			</table>
			<p class="submit">
			<input type="button" value="<?php echo  esc_html__( 'Clear Log', 'coupon-emails' ); ?>" class="button button-primary" 
			attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' . $option_name . '_nonce_log' ) ); ?>" 
			id="<?php echo $option_name; ?>_clear_log_btn" />						
			</p>
			
		</form>
	</div>
	</div>
