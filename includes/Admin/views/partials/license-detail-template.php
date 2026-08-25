<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Full license detail <template> for the "View" modal — shared by the
 * Dashboard's Recent Licenses list and the Users page's View column.
 *
 * Expects in scope:
 * $license_envato_tpl_item  (array) one row, shaped like Allusers::recent()/prepare_items() rows
 * $license_envato_tpl_table (\LicenseEnvato\Admin\Allusers) for build_domain_actions()
 */

$license_envato_tpl_uid = \LicenseEnvato\Admin\Allusers::detail_uid( $license_envato_tpl_item );
?>
<template id="le-license-detail-<?php echo esc_attr( $license_envato_tpl_uid ); ?>">
    <table class="form-table" role="presentation" autocomplete="off" data-lpignore="true" data-1p-ignore data-bwignore="true" data-form-type="other">
        <tbody>
            <tr>
                <th><?php esc_html_e( 'Username', 'license-envato' ); ?></th>
                <td><?php echo esc_html( $license_envato_tpl_item['username'] ? $license_envato_tpl_item['username'] : '—' ); ?></td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Item Name', 'license-envato' ); ?></th>
                <td><?php echo esc_html( $license_envato_tpl_item['itemname'] ? $license_envato_tpl_item['itemname'] : '—' ); ?></td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Item ID', 'license-envato' ); ?></th>
                <td><?php echo esc_html( $license_envato_tpl_item['itemid'] ); ?></td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Purchase code', 'license-envato' ); ?></th>
                <td class="le-field-with-actions">
                    <code><?php echo esc_html( $license_envato_tpl_item['purchasecode'] ); ?></code>
                    <?php
                    /** This filter is documented in includes/Admin/Allusers.php */
                    $license_envato_tpl_row_actions = apply_filters( 'license_envato_userlist_row_actions', array(), $license_envato_tpl_item );
                    if ( ! empty( $license_envato_tpl_row_actions ) ) {
                        echo wp_kses_post( implode( ' ', $license_envato_tpl_row_actions ) );
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Supported until', 'license-envato' ); ?></th>
                <td><?php echo esc_html( $license_envato_tpl_item['supported_until'] ? $license_envato_tpl_item['supported_until'] : '—' ); ?></td>
            </tr>
        </tbody>
    </table>
    <p class="le-domain-count">
        <?php
        $license_envato_tpl_domain_count = count( $license_envato_tpl_item['activations'] );
        echo esc_html( sprintf(
            /* translators: %s: number of activated domains */
            _n( '%s activated domain', '%s activated domains', $license_envato_tpl_domain_count, 'license-envato' ),
            number_format_i18n( $license_envato_tpl_domain_count )
        ) );
        ?>
    </p>
    <div class="le-domain-scroll">
        <table class="widefat striped">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Activated domain', 'license-envato' ); ?></th>
                    <th><?php esc_html_e( 'Action', 'license-envato' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $license_envato_tpl_item['activations'] ) ) { ?>
                    <tr>
                        <td colspan="2"><?php esc_html_e( 'No active domains.', 'license-envato' ); ?></td>
                    </tr>
                <?php } else { ?>
                    <?php
                    $license_envato_tpl_domain_actions = $license_envato_tpl_table->build_domain_actions( $license_envato_tpl_item );
                    foreach ( $license_envato_tpl_item['activations'] as $license_envato_tpl_domain => $license_envato_tpl_token ) {
                        ?>
                        <tr>
                            <td><span class="dashicons dashicons-yes-alt le-icon-active" aria-hidden="true"></span> <?php echo esc_html( $license_envato_tpl_domain ); ?></td>
                            <td><?php echo isset( $license_envato_tpl_domain_actions[ $license_envato_tpl_domain ] ) ? wp_kses_post( $license_envato_tpl_domain_actions[ $license_envato_tpl_domain ] ) : ''; ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</template>
