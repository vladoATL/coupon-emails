<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

$error = "";
$option_name = "couponemails_referralemail";
$options = get_option($option_name . '_options');
$headline = isset($options['headline']) ? $options['headline'] : "";
$directions = isset($options['directions']) ? $options['directions'] : "";
$email_text_ = isset($options['email_body']) ? $options['email_body'] : "";
$enabled = isset($options['enabled']) ? $options['enabled'] : 0;


$funcs = new \COUPONEMAILS\Coupon_Emails_EmailFunctions($option_name);
$email_text = $funcs->couponemails_replace_placeholders($email_text_, wp_get_current_user(), $options);
$email_sections = explode( "{personal_text}", strip_tags ( $email_text, "<br>") );

$referral = new \COUPONEMAILS\Coupon_Emails_Referral();

$column_percent = 100;
$columns = 1;
$contentVisibility = (object) array(
	'discount_value' => true,
	'description'    => true,
	'usage_limit'    => true,
	'schedule'       => true,
	'restriction'	 => true,);
$classnames     = array( 'coupons-list-block' );
$styles         = array(
	'grid-template-columns:' . str_repeat( ' ' . $column_percent . '%', $columns ),
	'max-width: ' . ( $columns * 350 ) . 'px',
	'margin: 0 auto',
	'padding-top: 5px'
	);

$address= isset($_POST['email_addresses']) ? $_POST['email_addresses']:''; 
$personaltext = isset($_POST['personal_text']) ? strip_tags (sanitize_text_field($_POST['personal_text']), "<br>") :'';  
if ($address == "" && $personaltext != "") {
	$error = esc_html__( 'Missing email addresses', 'coupon-emails' );
} elseif ($address != "") {
	$addresses = explode(",",$address);
	$email_body = str_replace("{personal_text}", $personaltext, $email_text);
	$cnt = 0;
	$referral->create_referral_coupon();
	foreach ($addresses as $email) {
			$cnt +=1;
			$referral->create_referral_couponemail($email,$email_body);		
			}
	$address = "";		
}

$coupon  =  $referral->referral_coupon;
$owned[] = $coupon;
if ($coupon->get_code() == "") {
	$msg = esc_html__( 'Your coupon will appear here after you email your friends as described below.', 'coupon-emails' );
	$coupon_note = "";
} else {
	$coupon_note = 	esc_html__( "The amount and expiration date of the coupon will increase after each friends purchase.", 'coupon-emails' );
	$msg = "";
}
$referral_coupons = $referral->get_referred_coupons();
$email_error =  $referral->email_error;
?>
<div class="emailerrortext"><?php echo esc_html( $email_error); ?></div>
<h2 style="padding: 1rem 0 0 0; font-size: 1.5rem; font-weight: 200;">
	<?php echo esc_html( esc_html__( 'My referral coupon', 'coupon-emails' )); ?>
</h2>
 <table class="coupon-table">
	<tr>
		<td align="center">
			<div class="coupon-in-table">
				<?php
				\COUPONEMAILS\Coupon_Emails_Helper_Functions::load_template(
				'coupons-list.php',
				array(
				'coupons'           => $owned,
				'classnames'        => $classnames,
				'single_class'		 => 'single-coupon',
				'columns'           => 1,
				'styles'            => $styles,
				'contentVisibility' => (object) $contentVisibility,
				'type'				=> 'owned',
				)
				);
				echo esc_html($msg);
				?>
				<br>
			</div>
		</td>
	</tr>
	<tr>
		<th class="titledesc"><?php echo esc_html($coupon_note); ?></th>
	</tr>
</table>
<?php 
if ($enabled) {
?>

<h2 style="padding: 1rem 0 0 0; font-size: 1.5rem; font-weight: 200;">
	<?php echo esc_html(esc_html__( 'Sending referrals', 'coupon-emails' )); ?>
</h2>
<div class='referral_headline'><?php echo esc_html($headline); ?></div>
<div class='referral_directions'><?php echo esc_html($directions); ?></div>
<form method="post" id="form2" name="form2">
	<table class="coupon-table">
		<tr>
			<th class="titledesc"><?php echo esc_html(esc_html__( "Friends' email addresses", 'coupon-emails' )); ?>:</th>
		</tr>
		<tr>
			<td>
				<input type="text" id="email_addresses" name="email_addresses" value="<?php echo esc_html($address); ?>"
				style="width: 95%;" placeholder="<?php echo  esc_html(esc_html__( 'myfriend@gmail.com,first.last@company.com,somebody@somewhere.net', 'coupon-emails' )); ?>">
			</td>
		</tr>
		<tr>
			<th class="titledesc"><?php echo esc_html(esc_html__( "Email text", 'coupon-emails' )); ?>:</th>
		</tr>
		<tr>
			<td class="emailtext">
				<?php
				$subtext = trim($email_sections[0]);
				$subtext = str_replace("{coupon}","<br><b>" . __( "COUPONCODE", 'coupon-emails' ) . "</b>", $subtext);
				echo wp_kses_post($subtext); ?>
			</td>
		</tr>
		<tr>
			<td class="emailtext">
				<textarea id="personal_text" name="personal_text" rows="5" style="width: 100%;"
				placeholder="<?php echo  esc_html(esc_html__( 'Enter your personal text here', 'coupon-emails' )); ?>"><?php echo esc_html($personaltext); ?></textarea>
			</td>
		</tr>
		<tr>
			<td class="emailtext">
				<?php
				$subtext = trim($email_sections[1]);
				echo wp_kses_post($subtext); ?>
			</td>
		</tr>
	</table>
	<button type="submit" value="<?php echo  esc_html(esc_html__( 'Send', 'coupon-emails' )); ?>" class="woocommerce-Button button send_referralemails_btn"
	attr-nonce="<?php echo esc_attr( wp_create_nonce( 'referral_mail_nonce' ) ); ?>" id="send_referralemails_btn" ><?php echo  esc_html(esc_html__( 'Send', 'coupon-emails' )); ?></button>
</form>
<?php
}
?>
 <h2 style="padding: 1rem 0 0 0; font-size: 1.5rem; font-weight: 200;">
	 <?php echo esc_html(esc_html__( 'My referrals', 'coupon-emails' )); ?>
</h2>
 <table class="coupon-table">
<tr>	
	<th class="titledesc"><?php echo esc_html(__( "Email", 'coupon-emails' )); ?>:</th>
	<th class="titledesc"><?php echo esc_html(__( "Expiration", 'coupon-emails' )); ?>:</th>	
	<th class="titledesc"><?php echo esc_html(__( "Name", 'coupon-emails' )); ?>:</th>
	<th class="titledesc"><?php echo esc_html(__( "Reward", 'coupon-emails' )); ?>:</th>
</tr>

<?php
if ($referral_coupons) {
	foreach ($referral_coupons as $c) { ?>
<tr>
	<td><?php echo esc_html($c['email']); ?></td>
	<td><?php echo  date_i18n( 'j.n.Y', strtotime( $c['coupon_expiration'] ) ); ?></td>
	<td><?php echo isset($c['name']) ? esc_html($c['name']) : ''; ?></td>
	<td><?php echo isset($c['reward']) ? wc_price($c['reward']) : ''; ?></td>
</tr>
<?php
}
} ?>
</table>