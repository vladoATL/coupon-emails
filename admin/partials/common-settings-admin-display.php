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

$fun = new COUPONEMAILS\EmailFunctions();
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
	<div id="namedayemail-setting"  class="coupon-emails-setting">

<div class="icon32" id="icon-options-general"><br></div>
<h2><?php echo _x('Common Settings','Setting', 'coupon-emails'); ?> </h2>
<form method="post" id="form1" name="form1" action="options.php">
<?php
settings_fields('couponemails_plugin_options');
$options = get_option('couponemails_options');

?>
<div class="couponemails_wrapper">
	<div class="couponemails_left">
	<table class="form-table">
	<tr>
		<th class="titledesc"><?php echo __( 'Delete unused coupons in days after expiration', 'coupon-emails' ); ?>:</th>
		<td>
			<input type="number" id="couponemails_options[days_delete]" name="couponemails_options[days_delete]"  style="width: 60px;" value="<?php echo $options['days_delete'] ?? ''; ?>"</input>
			<?php  echo wc_help_tip(__( 'If you leave this blank, the coupons will not be deleted. To automatically delete coupons, enter the number of days after the expiration date and unused coupons will be deleted.', 'coupon-emails' ), false); ?>
			<a class="button button-primary" href="admin.php?page=couponemails&deleteexpired=true"><?php echo __( 'Delete now', 'coupon-emails' ); ?></a>			
		</td>
	</tr>
	<tr valign="top">
		<th scope="row" class="titledesc"><?php echo __( 'Enable logs', 'coupon-emails' ); ?>:</th>
		<td><input type="checkbox" name="couponemails_options[enable_logs]" id="couponemails_options[enable_logs]"  value="1" <?php echo checked( 1, $options['enable_logs'] ?? '', false ) ?? '' ; ?>>			
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
	<h2><?php echo _x('Statistics','Setting', 'coupon-emails'); ?> </h2>
	<table class="widefat striped" width="100%">
		<thead>
			<tr>
				<th width="20%" ><?php echo __( 'Coupons category', 'coupon-emails' ); ?></th>
				<th width="20%" class="column-posts title-center"><?php echo __( 'Total coupons', 'coupon-emails' ); ?></th>				
				<th width="20%" class="column-posts title-center"><?php echo __( 'Used coupons', 'coupon-emails' ); ?></th>
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
				<td><?php echo __( 'Total', 'coupon-emails' ); ?></td>
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
