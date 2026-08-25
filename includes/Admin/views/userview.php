<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;
?>
<div class="wrap license-envato-wrap">
    <div class="le-page-header">
        <span class="le-icon-badge dashicons dashicons-groups" aria-hidden="true"></span>
        <div class="le-page-header-text">
            <h1 class="wp-heading-inline"><?php esc_html_e( 'Users', 'license-envato' ); ?></h1>
            <p class="le-page-subtitle"><?php esc_html_e( 'Every verified purchase code and the domains it is currently activated on.', 'license-envato' ); ?></p>
        </div>
    </div>
    <hr class="wp-header-end">

    <div class="le-card">
        <div class="le-card-head">
            <h2><?php esc_html_e( 'All Licenses', 'license-envato' ); ?></h2>
        </div>
        <?php
        $table->display();
        ?>
    </div>

    <?php include __DIR__ . '/partials/view-modal.php'; ?>
</div>
