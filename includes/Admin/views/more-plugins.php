<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** @var object[] $license_envato_more_plugins */
/** @var \LicenseEnvato\Admin\MorePlugins $license_envato_more_plugins_page */
?>
<div class="wrap license-envato-wrap">
    <div class="le-page-header">
        <span class="le-icon-badge dashicons dashicons-admin-plugins" aria-hidden="true"></span>
        <div class="le-page-header-text">
            <h1 class="wp-heading-inline"><?php esc_html_e( 'More Plugins', 'license-envato' ); ?></h1>
            <p class="le-page-subtitle"><?php esc_html_e( 'More free plugins from the same team — install them in one click.', 'license-envato' ); ?></p>
        </div>
    </div>
    <hr class="wp-header-end">

    <div id="plugin-filter" class="le-more-plugins">
        <?php foreach ( $license_envato_more_plugins as $license_envato_plugin ) :
            $license_envato_slug    = $license_envato_plugin->slug;
            $license_envato_icon    = $license_envato_more_plugins_page->icon_url( $license_envato_plugin );
            $license_envato_details = add_query_arg(
                [
                    'tab'       => 'plugin-information',
                    'plugin'    => $license_envato_slug,
                    'TB_iframe' => 'true',
                    'width'     => 772,
                    'height'    => 600,
                ],
                self_admin_url( 'plugin-install.php' )
            );
            $license_envato_desc = ! empty( $license_envato_plugin->short_description ) ? wp_strip_all_tags( $license_envato_plugin->short_description ) : '';
            ?>
            <div class="le-plugin-card plugin-card plugin-card-<?php echo esc_attr( $license_envato_slug ); ?>">
                <div class="le-plugin-card-top">
                    <a href="<?php echo esc_url( $license_envato_details ); ?>" class="thickbox open-plugin-details-modal le-plugin-icon" aria-label="<?php /* translators: %s: Plugin name. */ echo esc_attr( sprintf( __( 'More information about %s', 'license-envato' ), $license_envato_plugin->name ) ); ?>">
                        <?php if ( $license_envato_icon ) : ?>
                            <img src="<?php echo esc_url( $license_envato_icon ); ?>" alt="" width="80" height="80" loading="lazy">
                        <?php else : ?>
                            <span class="dashicons dashicons-admin-plugins" aria-hidden="true"></span>
                        <?php endif; ?>
                    </a>
                    <div class="le-plugin-info">
                        <h3>
                            <a href="<?php echo esc_url( $license_envato_details ); ?>" class="thickbox open-plugin-details-modal">
                                <?php echo esc_html( wp_strip_all_tags( $license_envato_plugin->name ) ); ?>
                            </a>
                        </h3>
                        <?php if ( $license_envato_desc ) : ?>
                            <p class="le-plugin-desc"><?php echo esc_html( $license_envato_desc ); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="le-plugin-card-bottom">
                    <div class="le-plugin-actions">
                        <a href="<?php echo esc_url( $license_envato_details ); ?>" class="thickbox open-plugin-details-modal button-link"><?php esc_html_e( 'More Details', 'license-envato' ); ?></a>
                        <?php echo wp_kses_post( $license_envato_more_plugins_page->action_button( $license_envato_plugin ) ); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
