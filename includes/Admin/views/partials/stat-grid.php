<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Shared stat-card row — All Users and Dashboard both `include` this with
 * $license_envato_stats (from license_envato_get_stats()) already in scope.
 */
?>
<div class="le-stat-grid">
    <div class="le-stat-card">
        <div class="le-stat-card-head">
            <span class="le-icon-badge le-icon-badge-neutral dashicons dashicons-id" aria-hidden="true"></span>
            <span class="le-stat-label"><?php esc_html_e( 'Total Licenses', 'license-envato' ); ?></span>
        </div>
        <p class="le-stat-value"><?php echo esc_html( number_format_i18n( $license_envato_stats['total'] ) ); ?></p>
    </div>
    <div class="le-stat-card">
        <div class="le-stat-card-head">
            <span class="le-icon-badge le-icon-badge-success dashicons dashicons-yes-alt" aria-hidden="true"></span>
            <span class="le-stat-label"><?php esc_html_e( 'Active Licenses', 'license-envato' ); ?></span>
        </div>
        <p class="le-stat-value"><?php echo esc_html( number_format_i18n( $license_envato_stats['active'] ) ); ?></p>
        <p class="le-stat-delta le-stat-delta-success">
            <?php
            echo esc_html( sprintf(
                /* translators: %s: percentage of licenses currently active */
                __( '%s%% of total', 'license-envato' ),
                number_format_i18n( $license_envato_stats['active_pct'] )
            ) );
            ?>
        </p>
    </div>
    <div class="le-stat-card">
        <div class="le-stat-card-head">
            <span class="le-icon-badge le-icon-badge-danger dashicons dashicons-dismiss" aria-hidden="true"></span>
            <span class="le-stat-label"><?php esc_html_e( 'Deactivated', 'license-envato' ); ?></span>
        </div>
        <p class="le-stat-value"><?php echo esc_html( number_format_i18n( $license_envato_stats['deactivated'] ) ); ?></p>
    </div>
    <div class="le-stat-card">
        <div class="le-stat-card-head">
            <span class="le-icon-badge le-icon-badge-danger dashicons dashicons-lock" aria-hidden="true"></span>
            <span class="le-stat-label"><?php esc_html_e( 'Blocked', 'license-envato' ); ?></span>
        </div>
        <p class="le-stat-value"><?php echo esc_html( number_format_i18n( $license_envato_stats['blocked'] ) ); ?></p>
        <p class="le-stat-delta le-stat-delta-danger"><?php esc_html_e( 'Codes & domains', 'license-envato' ); ?></p>
    </div>
</div>
