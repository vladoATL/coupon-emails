<?php
// check user capabilities
if ( ! current_user_can( 'read' ) ) {
	return;
}
wp_enqueue_script( 'jquery-tiptip' );

//Get the active tab from the $_GET param
$default_tab = null;
$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
?>
<div class="wrap">
	<h1><?php _e( 'Coupon Emails Settings','coupon-emails' ); ?></h1>
<nav class="nav-tab-wrapper">
	<a href="?page=couponemails" class="nav-tab <?php
	if ($tab===null) : ?>nav-tab-active<?php
	endif; ?>"><?php echo  __( 'Common', 'coupon-emails' ); ?></a>
	<a href="?page=couponemails&tab=birth-day" class="nav-tab
		<?php
	if ($tab==='birth-day') : ?>nav-tab-active<?php
	endif; ?>"> <?php echo  __( 'Birthday', 'coupon-emails' ); ?></a>	
	<a href="?page=couponemails&tab=name-day" class="nav-tab 
		<?php if ($tab==='name-day') : ?>nav-tab-active<?php endif; ?>"> <?php echo  __( 'Name Day', 'coupon-emails' ); ?></a>
	<a href="?page=couponemails&tab=after-order" class="nav-tab
		<?php
		if ($tab==='after-order') : ?>nav-tab-active<?php
		endif; ?>"> <?php echo  __( 'After Order', 'coupon-emails' ); ?></a>			
	<a href="?page=couponemails&tab=reorder" class="nav-tab <?php
			if ($tab==='reorder') : ?>nav-tab-active<?php
	endif; ?>"><?php echo  __( 'Reorder', 'coupon-emails' ); ?></a>
		
	<a href="?page=couponemails&tab=one-time" class="nav-tab
		<?php
		if ($tab==='one-time') : ?>nav-tab-active<?php
	endif; ?>"> <?php echo  __( 'One Time', 'coupon-emails' ); ?></a>

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