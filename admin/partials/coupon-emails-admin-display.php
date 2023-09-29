<?php
// check user capabilities
if ( ! current_user_can( 'read' ) ) {
	return;
}
wp_enqueue_script( 'jquery-tiptip' );
$birthday_top = \COUPONEMAILS\EmailFunctions::get_tab_top_color('birthdayemail');
$nameday_top = \COUPONEMAILS\EmailFunctions::get_tab_top_color('namedayemail');
$afterorder_top = \COUPONEMAILS\EmailFunctions::get_tab_top_color('afterorderemail');
$reviewed_top = \COUPONEMAILS\EmailFunctions::get_tab_top_color('reviewedemail');
$reorder_top = \COUPONEMAILS\EmailFunctions::get_tab_top_color('reorderemail');
$onetime_top = \COUPONEMAILS\EmailFunctions::get_tab_top_color('onetimeemail');
$reviewreminder_top = \COUPONEMAILS\EmailFunctions::get_tab_top_color('reminderemail');
$referral_top  = \COUPONEMAILS\EmailFunctions::get_tab_top_color('referralemail');
$options = get_option('couponemails_options');
$enable_referral = isset($options["enable_referral"]) ? $options["enable_referral"] : 0;

//Get the active tab from the $_GET param
$default_tab = null;
$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
$section = isset($_GET['section']) ? $_GET['section'] : $default_tab;
?>
<div class="wrap">
	<h1><?php _e( 'Coupon Emails Settings','coupon-emails' ); ?></h1>
<nav class="nav-tab-wrapper">
	<a href="?page=couponemails" class="nav-tab <?php
	if ($tab===null) : ?>nav-tab-active<?php
	endif; ?> top-gray"><?php echo  __( 'Common', 'coupon-emails' ); ?></a>
	<a href="?page=couponemails&tab=birth-day" class="nav-tab
		<?php
	if ($tab==='birth-day') : ?>nav-tab-active<?php
	endif; echo(' ' . $birthday_top); ?>"> <?php echo  __( 'Birthday', 'coupon-emails' ); ?></a>	
	<a href="?page=couponemails&tab=name-day" class="nav-tab 
		<?php
		if ($tab==='name-day') : ?>nav-tab-active<?php
		endif; echo(' ' . $nameday_top); ?>"> <?php echo  __( 'Name Day', 'coupon-emails' ); ?></a>
				
	<a href="?page=couponemails&tab=reorder" class="nav-tab <?php
			if ($tab==='reorder') : ?>nav-tab-active<?php
			endif; echo(' ' . $reorder_top); ?>"><?php echo  __( 'Reorder', 'coupon-emails' ); ?></a>

	<a href="?page=couponemails&tab=after-order" class="nav-tab
		<?php
			if ($tab==='after-order') : ?>nav-tab-active<?php
			endif; echo(' ' . $afterorder_top); ?>"> <?php echo  __( 'After Order', 'coupon-emails' ); ?></a>
						
	<a href="?page=couponemails&tab=reviewed" class="nav-tab <?php
		if ($tab==='reviewed') : ?>nav-tab-active<?php
		endif; echo(' ' . $reviewed_top); ?>"><?php echo  __( 'After Reviewed', 'coupon-emails' ); ?></a>
				
	<a href="?page=couponemails&tab=one-time" class="nav-tab
		<?php
		if ($tab==='one-time') : ?>nav-tab-active<?php
		endif; echo(' ' . $onetime_top); ?>"> <?php echo  __( 'One Time', 'coupon-emails' ); ?></a>

	<a href="?page=couponemails&tab=reminder" class="nav-tab
		<?php
		if ($tab==='reminder') : ?>nav-tab-active<?php
	endif; echo(' ' . $reviewreminder_top); ?>"> <?php echo  __( 'Reminders', 'coupon-emails' ); ?></a>

	<a href="?page=couponemails&tab=referral" class="nav-tab
		<?php
		if ($tab==='referral') : ?>nav-tab-active<?php
		endif; echo(' ' . $referral_top); ?>"> <?php echo  __( 'Referrals', 'coupon-emails' ); ?></a>
	
</nav>
	
<div class="tab-content">
	<?php
switch ($tab) :
case 'one-time':
	?>
	<div class="metabox-holder">
		<?php include('onetime-email-admin-display.php'); ?>		
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
case 'referral':
	?>
	<div class="metabox-holder">
		<?php include('referral-email-admin-display.php'); ?>
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