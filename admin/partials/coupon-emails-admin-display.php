<?php
// check user capabilities
if ( ! current_user_can( 'read' ) ) {
	return;
}
wp_enqueue_script( 'jquery-tiptip' );
$birthday_top = \COUPONEMAILS\Coupon_Emails_EmailFunctions::get_tab_top_color('couponemails_birthday');
$nameday_top = \COUPONEMAILS\Coupon_Emails_EmailFunctions::get_tab_top_color('couponemails_nameday');
$afterorder_top = \COUPONEMAILS\Coupon_Emails_EmailFunctions::get_tab_top_color('couponemails_afterorder');
$reviewed_top = \COUPONEMAILS\Coupon_Emails_EmailFunctions::get_tab_top_color('couponemails_reviewed');
$reorder_top = \COUPONEMAILS\Coupon_Emails_EmailFunctions::get_tab_top_color('couponemails_reorder');
$onetime_top = \COUPONEMAILS\Coupon_Emails_EmailFunctions::get_tab_top_color('couponemails_onetimecoupon');
$reviewreminder_top = \COUPONEMAILS\Coupon_Emails_EmailFunctions::get_tab_top_color('reminderemail');
$referral_top  = \COUPONEMAILS\Coupon_Emails_EmailFunctions::get_tab_top_color('couponemails_referralemail');
$heureka_top  = \COUPONEMAILS\Coupon_Emails_EmailFunctions::get_tab_top_color('heurekaemail');
$options = get_option('couponemails_options');
$enable_referral = isset($options["enable_referral"]) ? $options["enable_referral"] : 0;

//Get the active tab from the $_GET param
$default_tab = null;
$tab = isset($_GET['tab']) ? sanitize_text_field( $_GET['tab']) : $default_tab;
$section = isset($_GET['section']) ? sanitize_text_field($_GET['section']) : $default_tab;

$heureka_enable = false;
$heureka_enable = apply_filters( 'couponemails_heureka_enable', $heureka_enable );


?>
<div class="wrap">
	<h1><?php _e( 'Coupon Emails Settings','coupon-emails' ); ?></h1>
<nav class="nav-tab-wrapper">
	<a href="?page=couponemails" class="nav-tab <?php
	if ($tab===null) : ?>nav-tab-active<?php
	endif; ?> top-gray"><?php echo  esc_html__( 'Common', 'coupon-emails' ); ?></a>
	<a href="?page=couponemails&tab=birth-day" class="nav-tab
		<?php
	if ($tab==='birth-day') : ?>nav-tab-active<?php
	endif; echo(' ' . $birthday_top); ?>"> <?php echo  esc_html__( 'Birthday', 'coupon-emails' ); ?></a>	
	<a href="?page=couponemails&tab=name-day" class="nav-tab 
		<?php
		if ($tab==='name-day') : ?>nav-tab-active<?php
		endif; echo(' ' . $nameday_top); ?>"> <?php echo  esc_html__( 'Name Day', 'coupon-emails' ); ?></a>
				
	<a href="?page=couponemails&tab=reorder" class="nav-tab <?php
			if ($tab==='reorder') : ?>nav-tab-active<?php
			endif; echo(' ' . $reorder_top); ?>"><?php echo  esc_html__( 'Reorder', 'coupon-emails' ); ?></a>

	<a href="?page=couponemails&tab=after-order" class="nav-tab
		<?php
			if ($tab==='after-order') : ?>nav-tab-active<?php
			endif; echo(' ' . $afterorder_top); ?>"> <?php echo  esc_html__( 'After Order', 'coupon-emails' ); ?></a>
						
	<a href="?page=couponemails&tab=reviewed" class="nav-tab <?php
		if ($tab==='reviewed') : ?>nav-tab-active<?php
		endif; echo(' ' . $reviewed_top); ?>"><?php echo  esc_html__( 'After Reviewed', 'coupon-emails' ); ?></a>
				
	<a href="?page=couponemails&tab=one-time" class="nav-tab
		<?php
		if ($tab==='one-time') : ?>nav-tab-active<?php
		endif; echo(' ' . $onetime_top); ?>"> <?php echo  esc_html__( 'One Time', 'coupon-emails' ); ?></a>

	<a href="?page=couponemails&tab=reminder" class="nav-tab
		<?php
		if ($tab==='reminder') : ?>nav-tab-active<?php
	endif; echo(' ' . $reviewreminder_top); ?>"> <?php echo  esc_html__( 'Reminders', 'coupon-emails' ); ?></a>

	<a href="?page=couponemails&tab=referral" class="nav-tab
		<?php
		if ($tab==='referral') : ?>nav-tab-active<?php
		endif; echo(' ' . $referral_top); ?>"> <?php echo  esc_html__( 'Referrals', 'coupon-emails' ); ?></a>
	
	<?php
	// display Heureka tab from a different plugin
	if ($heureka_enable) { ?>
	<a href="?page=couponemails&tab=heureka" class="nav-tab
	<?php
	if ($tab==='heureka') : ?>nav-tab-active<?php
	endif; echo(' ' . $heureka_top); ?>"> <?php echo  esc_html__( 'Heureka', 'coupon-emails' ); ?></a>
	<?php } ?>
		
</nav>
	
<div class="tab-content">
	<?php
switch ($tab) :
case 'one-time':
?>
	<div class="metabox-holder">
		<?php
		switch ($section) :
		case 'no-coupon':
			include('onetime-email-admin-display.php');
			break;
		default:
			include('onetime-coupon-email-admin-display.php');
			break;
		endswitch;
		?>
	</div>
<?php
break;	
case 'birth-day':
	?>
	<div class="metabox-holder">
		<?php include('birth-day-email-admin-display.php'); ?>
	</div>
	<?php
	break;		
case 'reorder':
	?>
	<div class="metabox-holder">
		<?php include('reorder-email-admin-display.php'); ?>
	</div>
	<?php
	break;
case 'name-day':
	?>
	<div class="metabox-holder">
		<?php include('name-day-email-admin-display.php'); ?>
	</div>
	<?php
	break;
case 'after-order':
	?>
	<div class="metabox-holder">
		<?php include('afterorder-email-admin-display.php'); ?>		
	</div>
	<?php
	break;	
case 'reviewed':
	?>
	<div class="metabox-holder">
		<?php include('reviewed-email-admin-display.php');	?>
	</div>
	<?php
	break;	
case 'heureka':
	?>
	<div class="metabox-holder">
		<?php 
		if ( is_plugin_active( 'coupon-emails-heureka/coupon-emails-heureka.php' ) ) {
			switch ($section) :
			case 'coupon':
				include ( ABSPATH . '/wp-content/plugins/coupon-emails-heureka/admin/partials/heureka-email-admin-display-coupon.php');
				break;
			default:
				include ( ABSPATH . '/wp-content/plugins/coupon-emails-heureka/admin/partials/heureka-email-admin-display.php');
				break;
			endswitch;				
		} else {
			include('heureka-email-admin-display.php');
		}		
		 ?>
	</div>
	<?php
	break;		
case 'referral':
	?>
	<div class="metabox-holder">
		<?php
		switch ($section) :
		case 'confirmation':
			include('referral-confirmation-email-admin-display.php');
		break;
		default:
			include('referral-email-admin-display.php');
		break;
		endswitch;
		?>	
	</div>
	<?php
	break;	
case 'reminder':
	?>
	<div class="metabox-holder">
		<?php 
		switch ($section) :
		case 'expiration':
			include('expiration-reminder-email-admin-display.php'); 
			break;
		default:
		include('review-reminder-email-admin-display.php');
			break;		
		endswitch;	
		?>
	</div>
	<?php
	break;	
default:
	?>
	<div class="metabox-holder">
		<?php include('common-settings-admin-display.php'); ?>
	</div>
	<?php
	break;	
endswitch; ?>
	</div>

</div>