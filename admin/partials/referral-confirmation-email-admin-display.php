<?php
namespace COUPONEMAILS;
$option_name = "couponemails_referralconfirmation";
?>
<div class="wrap woocommerce">
	<div id="<?php echo $option_name; ?>-setting"  class="coupon-emails-setting">
<div class="couponemails_loader_cover">
	<div class="couponemails_loader"></div> 
</div>
<ul class="subsubsub">
	<li>
		<a href="?page=couponemails&tab=referral" class="">
		<?php echo  esc_html__( 'Referrals', 'coupon-emails' ); ?></a>
		|</li>
	<li>
		<a href="?page=couponemails&tab=referral&amp;section=confirmation" class="current">
		<?php echo  esc_html__( 'Order Confirmation', 'coupon-emails' ); ?></a>
		|</li>
</ul>
<input type="button" value="<?php echo  esc_html__( 'Restore Defaults', 'coupon-emails' ); ?>" class="button button-primary btn-restore"
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' .  $option_name . '_nonce' ) ); ?>" id="restore_<?php echo $option_name; ?>_values_btn" />

<div class="icon32" id="icon-options-general"><br></div>
<br>
<h2><?php echo esc_html_x('Referral Order Confirmation Emails Settings','Setting', 'coupon-emails'); ?> </h2>
<h4><?php echo esc_html_x('After the friend who received the referral email makes an order, the user receives a confirmation email with the amount of the reward.','Setting', 'coupon-emails'); ?> </h4>
<form method="post" id="form_<?php echo $option_name; ?>" name="form_<?php echo $option_name; ?>" action="options.php">
	<?php
	settings_fields($option_name . '_plugin_options');
	$options = get_option($option_name . '_options');
	?>
<div class="basic_setting">	
	<table class="form-table">
		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Enable referral order confirmation emails', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="<?php echo $option_name; ?>_options[enabled]" id="<?php echo $option_name; ?>_options[enabled]"  value="1" <?php echo checked( 1, $options['enabled'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(esc_html__( "This allows you to send confirmation emails to referring users when their friend makes an order.", 'coupon-emails' ), false); ?>
			</td>
		</tr>
	</table>
	</div>	
	
<?php include('email-table.php'); ?>

</form>
<p>
<input type="button" value="<?php echo  esc_html__( 'Create a test', 'coupon-emails' ); ?>" class="button button-primary" 
attr-nonce="<?php echo esc_attr( wp_create_nonce( '_' .  $option_name . '_nonce_test' ) ); ?>" id="test_<?php echo $option_name; ?>_btn" />
</p>
</div>
</div>

