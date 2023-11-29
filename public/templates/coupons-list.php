<?php
/**
 * Gutenberg block: Coupons list.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/blocks/coupons-list.php.
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
$card = new \COUPONEMAILS\Coupon_Emails_Coupon_Card();
do_action('before_coupons_list_block', $coupons, $classnames);?>

<div class="<?php echo esc_html(implode(' ', $classnames)); ?>">
	<div class="coupons-grid" style="<?php echo esc_html(implode('; ', $styles)); ?>">
    <?php if (is_array($coupons) && !empty($coupons)): ?>
        <?php foreach ($coupons as $coupon): ?>
		<?php $card->load_single_coupon_template($coupon, $contentVisibility, $type, $single_class); ?>
        <?php endforeach;?>
    <?php endif;?>
    </div>
</div>

<?php do_action('after_coupons_list_block', $coupons, $classnames);?>