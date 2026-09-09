<?php
/**
 * ReviewNotice()
 * Shows a dismissible admin notice asking for a WordPress.org review
 * after the plugin has been in use for 7 days.
 *
 * @author: Ashraful Sarkar Naiem
 * @since 1.3.0
 */

namespace LicenseEnvato\Admin;

class ReviewNotice {

    const INSTALLED_OPTION = 'license_envato_installed';
    const DISMISSED_OPTION = 'license_envato_review_dismissed';
    const LATER_OPTION     = 'license_envato_review_later';
    const NONCE_ACTION     = 'license_envato_review_action';
    const REVIEW_URL       = 'https://wordpress.org/support/plugin/license-envato/reviews/#new-post';

    /**
     * Days of use before the notice appears (also the snooze length)
     */
    const SHOW_AFTER_DAYS = 7;

    /**
     * __construct()
     * Initialize the class
     *
     * @return void
     * @since 1.3.0
     */
    public function __construct() {
        add_action( 'admin_init', [ $this, 'handle_actions' ] );
        add_action( 'admin_notices', [ $this, 'render_notice' ] );
        add_action( 'wp_ajax_license_envato_dismiss_review', [ $this, 'ajax_dismiss' ] );
    }

    /**
     * should_show()
     * Whether the notice should be rendered for the current request
     *
     * @return bool
     * @since 1.3.0
     */
    private function should_show() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return false;
        }

        if ( get_option( self::DISMISSED_OPTION ) ) {
            return false;
        }

        $later = (int) get_option( self::LATER_OPTION );
        if ( $later && time() < $later ) {
            return false;
        }

        $installed = (int) get_option( self::INSTALLED_OPTION );
        if ( ! $installed ) {
            // Fallback for installs that predate the option — start counting now
            update_option( self::INSTALLED_OPTION, time() );
            return false;
        }

        return ( time() - $installed ) >= self::SHOW_AFTER_DAYS * DAY_IN_SECONDS;
    }

    /**
     * handle_actions()
     * Processes the notice action links (rate / later / dismiss)
     *
     * @return void
     * @since 1.3.0
     */
    public function handle_actions() {
        if ( ! isset( $_GET['le_review_action'] ) || ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
            return;
        }

        $action = sanitize_key( wp_unslash( $_GET['le_review_action'] ) );

        if ( 'rate' === $action ) {
            update_option( self::DISMISSED_OPTION, 1 );
            wp_redirect( self::REVIEW_URL ); // phpcs:ignore WordPress.Security.SafeRedirect -- intentional external redirect to wordpress.org
            exit;
        }

        if ( 'later' === $action || 'dismiss' === $action ) {
            // Only an actual "rate" click stops the notice for good — dismissing
            // (× or "I already did") just snoozes it like "Maybe later" so
            // customers who haven't really left a review keep getting asked.
            update_option( self::LATER_OPTION, time() + self::SHOW_AFTER_DAYS * DAY_IN_SECONDS );
        }

        wp_safe_redirect( remove_query_arg( [ 'le_review_action', '_wpnonce' ] ) );
        exit;
    }

    /**
     * ajax_dismiss()
     * Permanently dismisses the notice when the native × button is clicked
     *
     * @return void
     * @since 1.3.0
     */
    public function ajax_dismiss() {
        check_ajax_referer( self::NONCE_ACTION );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error();
        }

        // Native × close is a snooze, not a permanent dismissal — see handle_actions().
        update_option( self::LATER_OPTION, time() + self::SHOW_AFTER_DAYS * DAY_IN_SECONDS );
        wp_send_json_success();
    }

    /**
     * render_notice()
     * Outputs the review notice
     *
     * @return void
     * @since 1.3.0
     */
    public function render_notice() {
        if ( ! $this->should_show() ) {
            return;
        }

        $nonce       = wp_create_nonce( self::NONCE_ACTION );
        $rate_url    = add_query_arg( [ 'le_review_action' => 'rate', '_wpnonce' => $nonce ] );
        $later_url   = add_query_arg( [ 'le_review_action' => 'later', '_wpnonce' => $nonce ] );
        $dismiss_url = add_query_arg( [ 'le_review_action' => 'dismiss', '_wpnonce' => $nonce ] );
        ?>
        <div class="notice notice-info is-dismissible license-envato-review-notice">
            <p>
                <strong><?php esc_html_e( 'Enjoying License For Envato?', 'license-envato' ); ?></strong><br>
                <?php esc_html_e( 'You have been using it for over a week — that\'s awesome! Could you please do us a big favor and give it a 5-star rating on WordPress.org? It really helps us spread the word and keep improving the plugin.', 'license-envato' ); ?>
            </p>
            <p>
                <a href="<?php echo esc_url( $rate_url ); ?>" class="button button-primary"><?php esc_html_e( 'Ok, you deserve it!', 'license-envato' ); ?></a>
                <a href="<?php echo esc_url( $later_url ); ?>" class="button"><?php esc_html_e( 'Maybe later', 'license-envato' ); ?></a>
                <a href="<?php echo esc_url( $dismiss_url ); ?>" class="license-envato-review-dismiss-link"><?php esc_html_e( 'I already did', 'license-envato' ); ?></a>
            </p>
        </div>
        <script>
            jQuery( function ( $ ) {
                $( document ).on( 'click', '.license-envato-review-notice .notice-dismiss', function () {
                    $.post( ajaxurl, {
                        action: 'license_envato_dismiss_review',
                        _wpnonce: '<?php echo esc_js( $nonce ); ?>'
                    } );
                } );
            } );
        </script>
        <?php
    }
}
