<?php

?>
<div class="email_setting">
	<h3><?php echo esc_html_x('Email message setting','Setting section', 'coupon-emails'); ?> </h3>
	<table id="email-table" class="form-table">
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Email from name', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="text" id="<?php echo $option_name; ?>_options[from_name]" name="<?php echo $option_name; ?>_options[from_name]"  style="width: 200px;" value="<?php echo $options['from_name'] ?? ''; ?>"</input>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Email from address', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="email" id="<?php echo $option_name; ?>_options[from_address]" name="<?php echo $option_name; ?>_options[from_address]"  style="width: 200px;" value="<?php echo $options['from_address'] ?? ''; ?>"</input>

			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Email CC', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="email" id="<?php echo $option_name; ?>_options[cc_address]" name="<?php echo $option_name; ?>_options[cc_address]"  style="width: 200px;" value="<?php echo $options['cc_address'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( 'Add multiple emails separated by comma ( , ).', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th scope="row" class="titledesc">
				<?php echo esc_html__( 'BCC and Test email address', 'coupon-emails' ); ?>:
			</th>
			<td>
				<input type="email" id="<?php echo $option_name; ?>_options[bcc_address]" name="<?php echo $option_name; ?>_options[bcc_address]"  style="width: 200px;" value="<?php echo $options['bcc_address'] ?? ''; ?>"</input>
				<?php  echo wc_help_tip(esc_html__( 'This email address is used for testing as well as for all email messages as a blind copy address.', 'coupon-emails' ) . ' ' .  esc_html__( 'Add multiple emails separated by comma ( , ).', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc"><?php echo esc_html__( 'Use WooCommerce email template', 'coupon-emails' ); ?>:</th>
			<td><input type="checkbox" name="<?php echo $option_name; ?>_options[wc_template]" id="<?php echo $option_name; ?>_options[wc_template]"  value="1" <?php echo checked( 1, $options['wc_template'] ?? '', false ) ?? '' ; ?>>
				<?php  echo wc_help_tip(esc_html__( 'Turn this on if you want your email to look just like a regular WooCommerce email.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Email subject', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="text" id="<?php echo $option_name; ?>_options[subject]" name="<?php echo $option_name; ?>_options[subject]"  style="width: 500px;" value="<?php echo $options['subject'] ?? ''; ?>"</input>
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html__( 'Email header', 'coupon-emails' ); ?>:</th>
			<td>
				<input type="text" id="<?php echo $option_name; ?>_options[header]" name="<?php echo $option_name; ?>_options[header]"  style="width: 500px;" value="<?php echo $options['header'] ?? ''; ?>"</input>
				<?php echo wc_help_tip(esc_html__( 'This is short text on the top of the email.', 'coupon-emails' ), false); ?>
			</td>
		</tr>
		<th class="titledesc" style="padding-bottom: 0px;"><?php echo esc_html__( 'Email body', 'coupon-emails' ); ?> :</th>
		<tr>
			<td colspan="2">
				<?php
				$args = array("textarea_name" => $option_name . "_options[email_body]", 'editor_class' => 'textarea_');
				$content_text  = $options['email_body'] ?? '';
				wp_editor( $content_text, "email_body", $args );
				?>
			</td>
		</tr>
		<tfoot>
			<tr>
				<td colspan="2">
					<p class="description">
						<?php echo esc_html__( 'Placeholders', 'coupon-emails' ); ?>:
						<i>{fname}, {fname5}, {lname}, {email}, {coupon}, {percent}, {products_cnt}, {expires}, {expires_in_days}, {last_order_date}, {site_name}, {site_url}, {site_name_url}<?php
							if ($option_name == 'couponemails_reviewed') {
							echo ', {reviewed_prod}';
						} 
						if ($option_name == 'couponemails_expirationreminder') {
							echo ', {for_date}';
						} 
						if ($option_name == 'couponemails_referralconfirmation') {
							echo ', {friend_firstname}, {friend_lastname}, {reward_amount}, {coupon_amount}';
						}												
						?><br>
							<small><?php echo esc_html__( 'Use {fname5} for Czech salutation.', 'coupon-emails' ); ?></small>
						</i>
					</p></td>
			</tr></tfoot>
	</table>
</div>
 <p class="submit">
	 <input type="submit" class="button-primary" value="<?php esc_html_e('Save Changes') ?>" name="<?php echo $option_name; ?>_submit" id="<?php echo $option_name; ?>_submit"  />
</p>