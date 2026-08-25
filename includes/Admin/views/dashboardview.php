<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

$license_envato_stats = license_envato_get_stats();
?>
<div class="wrap license-envato-wrap">
    <div class="le-page-header">
        <span class="le-icon-badge dashicons dashicons-dashboard" aria-hidden="true"></span>
        <div class="le-page-header-text">
            <h1 class="wp-heading-inline"><?php esc_html_e( 'Dashboard', 'license-envato' ); ?></h1>
            <p class="le-page-subtitle"><?php esc_html_e( 'Overview of Envato purchase code verifications and active licenses on this site.', 'license-envato' ); ?></p>
        </div>
    </div>
    <hr class="wp-header-end">

    <?php include __DIR__ . '/partials/stat-grid.php'; ?>

    <div class="le-card">
        <div class="le-card-head">
            <h2><?php esc_html_e( 'Recent Licenses', 'license-envato' ); ?></h2>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=licenseenvato-users' ) ); ?>" class="button button-small"><?php esc_html_e( 'View all', 'license-envato' ); ?></a>
        </div>

        <?php if ( empty( $recent ) ) { ?>
            <p><?php esc_html_e( 'No licenses verified yet.', 'license-envato' ); ?></p>
        <?php } else { ?>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Username', 'license-envato' ); ?></th>
                        <th><?php esc_html_e( 'Item Name', 'license-envato' ); ?></th>
                        <th><?php esc_html_e( 'Item ID', 'license-envato' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'license-envato' ); ?></th>
                        <th><?php esc_html_e( 'View', 'license-envato' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $recent as $license_envato_dash_item ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- local template loop variable. ?>
                        <tr>
                            <td><?php echo esc_html( $license_envato_dash_item['username'] ? $license_envato_dash_item['username'] : '—' ); ?></td>
                            <td><?php echo esc_html( $license_envato_dash_item['itemname'] ? $license_envato_dash_item['itemname'] : '—' ); ?></td>
                            <td><?php echo esc_html( $license_envato_dash_item['itemid'] ); ?></td>
                            <td>
                                <?php if ( ! empty( $license_envato_dash_item['activations'] ) ) { ?>
                                    <span class="le-badge le-badge-success"><?php esc_html_e( 'Active', 'license-envato' ); ?></span>
                                <?php } else { ?>
                                    <span class="le-badge le-badge-muted"><?php esc_html_e( 'Deactivated', 'license-envato' ); ?></span>
                                <?php } ?>
                            </td>
                            <td><?php echo wp_kses_post( $table->view_button_html( $license_envato_dash_item ) ); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <?php foreach ( $recent as $license_envato_dash_item ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- local template loop variable. ?>
                <?php
                $license_envato_tpl_item  = $license_envato_dash_item;
                $license_envato_tpl_table = $table;
                include __DIR__ . '/partials/license-detail-template.php';
                ?>
            <?php } ?>
        <?php } ?>
    </div>

    <?php include __DIR__ . '/partials/view-modal.php'; ?>
</div>
