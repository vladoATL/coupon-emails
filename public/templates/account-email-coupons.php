<?php
/**
 * My Coupons page (My Accounts > My Coupons).
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/page-my-coupons.php.
 *
 */
defined( 'ABSPATH' ) || exit;
?>

<?php if ( $expired || $owned || $used ) : ?>


    <?php if ( $owned ) : ?>
        <h2 style="padding: 1rem 0; font-size: 1.5rem; font-weight: 200;">
            <?php echo esc_html( $labels['owned'] ); ?>
        </h2>
        <?php echo wp_kses_post( $owned ); ?>
    <?php endif; ?>

    <?php if ( $used ) : ?>
        <h2 style="padding: 1rem 0; font-size: 1.5rem; font-weight: 200;">
            <?php echo esc_html( $labels['used'] ); ?>
        </h2>
        <?php echo wp_kses_post( $used ); ?>
    <?php endif; ?>
    
    <?php if ( $expired ) : ?>
        <h2 style="padding: 1rem 0; font-size: 1.5rem; font-weight: 200;">
			<?php echo esc_html( $labels['expired'] ); ?>
        </h2>
        <?php echo wp_kses_post( $expired ); ?>
    <?php endif; ?>    
    
<?php else : ?>
    <p><?php echo esc_html( $labels['none'] ); ?></p>
<?php endif; ?>
