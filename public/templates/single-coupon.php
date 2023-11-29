<?php
/**
 * Gutenberg block: Single Coupon.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/blocks/single-coupon.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package ACFWF\Templates
 * @version 3.1
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<div class="<?php echo esc_html( implode(' ', $classnames) . ' ' .  $single_class); ?>" >
    <?php do_action('before_single_coupon_block', $coupon);?>
    <?php if ($has_usage_limit): ?>
	<span class="coupon-usage-limit"><?php echo esc_html(sprintf(_n('%s use remaining', '%s uses remaining', $coupon->get_usage_limit(), 'coupon-emails'), $coupon->get_usage_limit())); ?></span>
    <?php endif;?>
    <div class="coupon-content">
		<?php if ($type == 'owned') { ?>
		<a href="<?php echo esc_url($coupon->get_coupon_url()); ?>" title="<?php echo esc_attr($coupon->get_code()); ?>" rel="nofollow">
		<?php } ?>
		<span class="coupon-code"><?php echo esc_html(strtoupper($coupon->get_code())); ?></span>
				<?php
				if ($type == 'owned') { ?>  </a>
				<?php } ?>
        <?php if ( $has_discount_value): ?>
		<span class="coupon-discount-info"><?php echo wp_kses_post($coupon->get_discount_value_string()); ?></span>
        <?php endif;?>
        
        <?php if ($has_description): ?>
		<span class="coupon-description"><?php echo wp_kses_post($coupon->get_description()) ; ?></span>
        <?php endif;?>
    </div>
    <div>
	<?php
	if ( $has_restriction) : ?>
	<span class="coupon-restriction"><?php echo wp_kses_post($coupon->get_email_restriction()); ?></span>
	<?php
	endif; ?>    
    <?php if ($has_schedule): ?>
	<span class="coupon-schedule"><?php echo wp_kses_post($schedule_string); ?></span>
    <?php endif;?>
    <?php do_action('after_single_coupon_block', $coupon);?>
    </div>
</div>