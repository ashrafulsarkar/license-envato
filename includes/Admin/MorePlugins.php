<?php
/**
 * MorePlugins()
 * "More Plugins" admin page — lists the author's other wordpress.org plugins
 * with core's own one-click Install / Activate buttons.
 *
 * @author: Ashraful Sarkar Naiem
 * @since 1.4.6
 */

namespace LicenseEnvato\Admin;

class MorePlugins {

    /**
     * Transient key for the cached wordpress.org plugin data.
     */
    const CACHE_KEY = 'license_envato_more_plugins';

    /**
     * Plugins to showcase. The fallback values are used only when the
     * wordpress.org API cannot be reached.
     *
     * @return array[]
     */
    public static function get_plugins() {
        $plugins = [
            [
                'slug'              => 'bookyzo',
                'name'              => 'Bookyzo – Booking & Appointments',
                'short_description' => __( 'Appointments, table reservations, event tickets and generic time-slot bookings — one booking widget, one admin panel, no WooCommerce required.', 'license-envato' ),
            ],
            [
                'slug'              => 'codeholt-bundles-for-woocommerce',
                'name'              => 'Codeholt Bundles for WooCommerce',
                'short_description' => __( 'Create product bundles for WooCommerce with fixed pricing, automatic savings, stock sync, Gutenberg block, Elementor widget and analytics.', 'license-envato' ),
            ],
        ];

        /**
         * Filter the plugins shown on the More Plugins page.
         *
         * @param array[] $plugins Each item needs at least a wordpress.org `slug`.
         */
        return apply_filters( 'license_envato_more_plugins', $plugins );
    }

    /**
     * Fetch plugin data from the wordpress.org API, cached for 12 hours.
     * Falls back to the static data for any plugin the API did not return.
     *
     * @return object[]
     */
    public function get_plugins_data() {
        $plugins = self::get_plugins();
        $cached  = get_transient( self::CACHE_KEY );
        $cached  = is_array( $cached ) ? $cached : [];
        $fetched = false;

        if ( ! function_exists( 'plugins_api' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        }

        $data = [];
        foreach ( $plugins as $plugin ) {
            if ( empty( $plugin['slug'] ) ) {
                continue;
            }
            $slug = sanitize_key( $plugin['slug'] );

            if ( ! isset( $cached[ $slug ] ) ) {
                $api = plugins_api( 'plugin_information', [
                    'slug'   => $slug,
                    'fields' => [
                        'short_description' => true,
                        'icons'             => true,
                        'active_installs'   => true,
                        'rating'            => true,
                        'num_ratings'       => true,
                        'sections'          => false,
                        'banners'           => false,
                        'reviews'           => false,
                        'versions'          => false,
                        'contributors'      => false,
                        'tags'              => false,
                        'screenshots'       => false,
                        'compatibility'     => false,
                    ],
                ] );

                if ( ! is_wp_error( $api ) && ! empty( $api->slug ) ) {
                    $cached[ $slug ] = $api;
                    $fetched         = true;
                }
            }

            $item = isset( $cached[ $slug ] ) ? clone $cached[ $slug ] : (object) [];

            // Static fallbacks for anything the API did not provide.
            $item->slug = $slug;
            foreach ( [ 'name', 'short_description' ] as $field ) {
                if ( empty( $item->$field ) && ! empty( $plugin[ $field ] ) ) {
                    $item->$field = $plugin[ $field ];
                }
            }
            if ( empty( $item->name ) ) {
                $item->name = $slug;
            }

            $data[] = $item;
        }

        // Only cache when every plugin came back, so a failed lookup is retried soon.
        if ( $fetched ) {
            set_transient( self::CACHE_KEY, $cached, count( $cached ) === count( $data ) ? 12 * HOUR_IN_SECONDS : 15 * MINUTE_IN_SECONDS );
        }

        return $data;
    }

    /**
     * Build the action button for one plugin card, mirroring core's states.
     *
     * @param object $plugin wordpress.org API data.
     * @return string HTML
     */
    public function action_button( $plugin ) {
        if ( ! function_exists( 'install_plugin_install_status' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        }
        if ( ! function_exists( 'is_plugin_active' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        // install_plugin_install_status() needs a version to compare against.
        $api_data = clone $plugin;
        if ( empty( $api_data->version ) ) {
            $api_data->version = '0';
        }
        $status = install_plugin_install_status( $api_data );
        $name   = $plugin->name;

        if ( 'install' === $status['status'] ) {
            if ( ! current_user_can( 'install_plugins' ) || empty( $status['url'] ) ) {
                return '';
            }

            return sprintf(
                '<a class="install-now button button-primary" data-slug="%1$s" href="%2$s" aria-label="%3$s" data-name="%4$s">%5$s</a>',
                esc_attr( $plugin->slug ),
                esc_url( $status['url'] ),
                /* translators: %s: Plugin name. */
                esc_attr( sprintf( __( 'Install %s now', 'license-envato' ), $name ) ),
                esc_attr( $name ),
                esc_html__( 'Install Now', 'license-envato' )
            );
        }

        $file = ! empty( $status['file'] ) ? $status['file'] : '';

        if ( $file && is_plugin_active( $file ) ) {
            return '<button type="button" class="button button-disabled" disabled="disabled">' . esc_html_x( 'Active', 'plugin', 'license-envato' ) . '</button>';
        }

        if ( $file && current_user_can( 'activate_plugin', $file ) ) {
            $activate_url = wp_nonce_url(
                add_query_arg( [ 'action' => 'activate', 'plugin' => $file ], admin_url( 'plugins.php' ) ),
                'activate-plugin_' . $file
            );

            return sprintf(
                '<a class="button button-primary activate-now" href="%1$s" aria-label="%2$s">%3$s</a>',
                esc_url( $activate_url ),
                /* translators: %s: Plugin name. */
                esc_attr( sprintf( __( 'Activate %s', 'license-envato' ), $name ) ),
                esc_html_x( 'Activate', 'plugin', 'license-envato' )
            );
        }

        return '<button type="button" class="button button-disabled" disabled="disabled">' . esc_html_x( 'Installed', 'plugin', 'license-envato' ) . '</button>';
    }

    /**
     * Pick the best icon URL from the API data.
     *
     * @param object $plugin
     * @return string
     */
    public function icon_url( $plugin ) {
        $icons = ! empty( $plugin->icons ) ? (array) $plugin->icons : [];
        foreach ( [ 'svg', '2x', '1x', 'default' ] as $size ) {
            if ( ! empty( $icons[ $size ] ) ) {
                return $icons[ $size ];
            }
        }
        return '';
    }

    /**
     * Enqueue core's plugin-installer scripts (AJAX install + details modal).
     *
     * @return void
     */
    public static function enqueue_assets() {
        wp_enqueue_script( 'updates' );
        wp_enqueue_script( 'plugin-install' );
        add_thickbox();
        add_action( 'admin_footer', [ __CLASS__, 'print_footer_templates' ] );
    }

    /**
     * Templates wp.updates needs: filesystem credentials modal + admin notices.
     *
     * @return void
     */
    public static function print_footer_templates() {
        if ( ! function_exists( 'wp_print_request_filesystem_credentials_modal' ) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        wp_print_request_filesystem_credentials_modal();
        wp_print_admin_notice_templates();
    }

    /**
     * Render the page.
     *
     * @return void
     */
    public function plugin_page() {
        $license_envato_more_plugins = $this->get_plugins_data();
        $license_envato_more_plugins_page = $this;
        $view = __DIR__ . '/views/more-plugins.php';
        if ( file_exists( $view ) ) {
            include $view;
        }
    }
}
