<?php
/**
 * Limited-time Codeholt October promotion.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_notices', 'license_envato_october_sale_notice' );
function license_envato_october_sale_notice() {
    // WordPress site time keeps the offer visible through October 31, 2026.
    $today = current_time( 'Y-m-d' );
    $cookie_name = 'license_envato_october_sale_' . get_current_blog_id() . '_' . get_current_user_id();
    if ( isset( $_COOKIE[ $cookie_name ] ) && $today === $_COOKIE[ $cookie_name ] ) {
        return;
    }
    $tomorrow = new DateTimeImmutable( 'tomorrow', wp_timezone() );
    if ( ! current_user_can( 'manage_options' ) || $today < '2026-10-01' || $today > '2026-10-31' ) {
        return;
    }
    ?>
    <div id="license-envato-october-sale" class="notice is-dismissible">
        <div class="le-sale-message">
            <span class="le-sale-brand">License For Envato Pro</span>
            <span class="le-sale-title"><?php esc_html_e( 'October Sale', 'license-envato' ); ?> <strong><?php esc_html_e( '30% OFF', 'license-envato' ); ?></strong></span>
            <span class="le-sale-detail"><?php esc_html_e( 'On all Codeholt products · Valid till 31 October', 'license-envato' ); ?></span>
        </div>
        <div class="le-sale-actions">
            <span class="le-sale-label"><?php esc_html_e( 'USE CODE', 'license-envato' ); ?></span>
            <button type="button" class="le-sale-copy" aria-label="<?php esc_attr_e( 'Copy coupon code OCTOBER30', 'license-envato' ); ?>"><span>OCTOBER30</span> <span class="dashicons dashicons-admin-page" aria-hidden="true"></span></button>
            <a class="le-sale-upgrade" href="<?php echo esc_url( 'https://app.codeholt.com/' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Upgrade Now', 'license-envato' ); ?></a>
            <span class="le-sale-status screen-reader-text" role="status" aria-live="polite"></span>
        </div>
    </div>
    <style>
        #license-envato-october-sale{position:relative;display:flex;align-items:center;justify-content:space-between;gap:24px;box-sizing:border-box;padding:22px 42px 22px 26px;border:1px solid #263c61;border-radius:12px;background:radial-gradient(ellipse at top right,#244d8c,transparent 65%),linear-gradient(110deg,#0b1223,#122039);color:#fff;box-shadow:0 4px 12px #0b122312}
        #license-envato-october-sale .le-sale-message{display:flex;flex-direction:column;gap:8px}
        #license-envato-october-sale .le-sale-brand{color:#a9caff;font-weight:700;font-size:12px;letter-spacing:2px;text-transform:uppercase}
        #license-envato-october-sale .le-sale-title{font-size:25px;font-weight:600;line-height:1.3}
        #license-envato-october-sale .le-sale-title strong{color:#ffb367;margin-left:10px;font-size:30px;font-weight:800}
        #license-envato-october-sale .le-sale-detail{color:#ccd7e9;font-size:12px}
        #license-envato-october-sale .le-sale-actions{display:flex;align-items:center;flex-wrap:wrap;gap:14px}
        #license-envato-october-sale .le-sale-label{font-size:10px;letter-spacing:2px;color:#ccd7e9}
        #license-envato-october-sale .le-sale-copy{display:inline-flex;align-items:center;gap:12px;padding:13px 18px;border:2px dashed #79aaff;border-radius:8px;background:#fff;color:#143f86;font-size:17px;font-weight:800;letter-spacing:1px;cursor:pointer}
        #license-envato-october-sale .le-sale-upgrade{padding:16px 24px;border-radius:8px;background:#377ef5;color:#fff;text-decoration:none;font-size:14px;font-weight:700;white-space:nowrap}
        #license-envato-october-sale .le-sale-upgrade:hover{background:#2466d5}
        #license-envato-october-sale .le-sale-copy:focus-visible,#license-envato-october-sale .le-sale-upgrade:focus-visible{outline:2px solid #ffb367;outline-offset:3px}
        #license-envato-october-sale .notice-dismiss:before{color:#ccd7e9}
        @media(max-width:1100px){#license-envato-october-sale{align-items:flex-start;flex-direction:column;gap:18px}}
        @media(max-width:600px){#license-envato-october-sale .le-sale-title{font-size:20px}#license-envato-october-sale .le-sale-title strong{font-size:24px}#license-envato-october-sale .le-sale-label{width:100%}}
    </style>
    <script>
    (function () {
        var notice = document.getElementById('license-envato-october-sale');
        // Delegation also handles the dismiss button added later by WordPress.
        notice.addEventListener('click', function (event) {
            if (!event.target.closest('.notice-dismiss')) {
                return;
            }
            document.cookie = <?php echo wp_json_encode( $cookie_name . '=' . $today ); ?>
                + '; expires=' + <?php echo wp_json_encode( gmdate( 'D, d M Y H:i:s', $tomorrow->getTimestamp() ) . ' GMT' ); ?>
                + '; path=' + <?php echo wp_json_encode( wp_parse_url( admin_url(), PHP_URL_PATH ) ); ?>
                + '; SameSite=Lax' + (window.location.protocol === 'https:' ? '; Secure' : '');
        });
        var button = notice.querySelector('.le-sale-copy');
        var status = notice.querySelector('.le-sale-status');
        var resetTimer;
        function showResult(success) {
            status.textContent = success ? <?php echo wp_json_encode( __( 'Coupon code copied.', 'license-envato' ) ); ?> : <?php echo wp_json_encode( __( 'Please select and copy OCTOBER30 manually.', 'license-envato' ) ); ?>;
            button.querySelector('.dashicons').classList.toggle('dashicons-yes', success);
            button.querySelector('.dashicons').classList.toggle('dashicons-admin-page', !success);
            clearTimeout(resetTimer);
            resetTimer = setTimeout(function () {
                button.querySelector('.dashicons').classList.remove('dashicons-yes');
                button.querySelector('.dashicons').classList.add('dashicons-admin-page');
            }, 2500);
        }
        function fallbackCopy() {
            var field = document.createElement('textarea');
            field.value = 'OCTOBER30';
            field.style.position = 'fixed';
            field.style.opacity = '0';
            document.body.appendChild(field);
            field.select();
            var copied = false;
            try { copied = document.execCommand('copy'); } catch (error) {}
            document.body.removeChild(field);
            button.focus();
            showResult(copied);
        }
        button.addEventListener('click', function () {
            status.textContent = '';
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText('OCTOBER30').then(function () { showResult(true); }, fallbackCopy);
            } else {
                fallbackCopy();
            }
        });
    }());
    </script>
    <?php
}