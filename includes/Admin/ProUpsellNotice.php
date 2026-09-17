<?php
/**
 * ProUpsellNotice()
 * A dismissible "Build more with Pro" card, shown only on this plugin's
 * own admin pages (Dashboard, Users, Settings) — never on the main
 * WordPress Dashboard or any other unrelated admin screen. Scoping it
 * this way keeps it well inside WordPress.org's guidelines against
 * unsolicited upsell notices, unlike a sitewide dashboard widget.
 *
 * @author: Ashraful Sarkar Naiem
 * @since 1.4.0
 */

namespace LicenseEnvato\Admin;

class ProUpsellNotice {

    const SNOOZE_OPTION = 'license_envato_pro_upsell_snoozed_until';
    const NONCE_ACTION   = 'license_envato_pro_upsell_dismiss';

    /**
     * How long a dismiss (the native × close) snoozes the card for
     */
    const SNOOZE_DAYS = 14;

    /**
     * Initialize the class
     */
    public function __construct() {
        add_action( 'admin_notices', [ $this, 'render' ] );
        add_action( 'wp_ajax_license_envato_dismiss_pro_upsell', [ $this, 'ajax_dismiss' ] );
    }

    /**
     * Whether the card should be rendered for the current request
     *
     * @return bool
     */
    private function should_show() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return false;
        }

        // Once Pro is installed there's nothing left to upsell — this is the
        // notice's natural, permanent stop condition (no separate "I bought
        // it" dismissal needed, unlike the review notice).
        if ( license_envato_is_pro_active() ) {
            return false;
        }

        if ( ! $this->is_own_admin_page() ) {
            return false;
        }

        $snoozed_until = (int) get_option( self::SNOOZE_OPTION );
        return ! ( $snoozed_until && time() < $snoozed_until );
    }

    /**
     * Whether the current admin screen is one of this plugin's own pages
     *
     * @return bool
     */
    private function is_own_admin_page() {
        if ( ! isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only page-slug check, not a state-changing action.
            return false;
        }

        $page = sanitize_key( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only.
        return 0 === strpos( $page, 'licenseenvato' );
    }

    /**
     * Permanently snooze the card when the native × close is clicked
     *
     * @return void
     */
    public function ajax_dismiss() {
        check_ajax_referer( self::NONCE_ACTION );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error();
        }

        update_option( self::SNOOZE_OPTION, time() + self::SNOOZE_DAYS * DAY_IN_SECONDS );
        wp_send_json_success();
    }

    /**
     * Output the upsell card
     *
     * @return void
     */
    public function render() {
        if ( ! $this->should_show() ) {
            return;
        }

        $nonce = wp_create_nonce( self::NONCE_ACTION );
        ?>
        <div class="le-card license-envato-pro-upsell" id="license-envato-pro-upsell">
            <button type="button" class="license-envato-pro-upsell-close" aria-label="<?php esc_attr_e( 'Dismiss', 'license-envato' ); ?>">&times;</button>
            <p class="license-envato-pro-upsell-title"><?php esc_html_e( 'Build more with License For Envato Pro', 'license-envato' ); ?></p>
            <p class="license-envato-pro-upsell-desc"><?php esc_html_e( 'Automatic update delivery for your themes/plugins, WooCommerce integration, configurable activation limits, webhooks, blacklist and more.', 'license-envato' ); ?></p>
            <a href="<?php echo esc_url( license_envato_upgrade_url() ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary le-upgrade-btn"><?php esc_html_e( 'Upgrade now', 'license-envato' ); ?></a>
        </div>
        <script>
            jQuery( function ( $ ) {
                $( '#license-envato-pro-upsell .license-envato-pro-upsell-close' ).on( 'click', function () {
                    var $card = $( this ).closest( '.license-envato-pro-upsell' );
                    $.post( ajaxurl, {
                        action: 'license_envato_dismiss_pro_upsell',
                        _wpnonce: '<?php echo esc_js( $nonce ); ?>'
                    } );
                    $card.slideUp( 150, function () { $card.remove(); } );
                } );
            } );
        </script>
        <?php
    }
}
