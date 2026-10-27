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
    // Nothing to sell once Pro is active. Installed-but-inactive still sees the offer.
    if ( license_envato_is_pro_active() ) {
        return;
    }
    // The sale ends at midnight after October 31 in site time. Sent as seconds
    // remaining (not an absolute date) so a wrong clock on the admin's computer
    // can't skew the countdown.
    $sale_end       = new DateTimeImmutable( '2026-11-01 00:00:00', wp_timezone() );
    $sale_remaining = max( 0, $sale_end->getTimestamp() - time() );
    ?>
    <div id="license-envato-october-sale" class="notice is-dismissible">
        <span class="le-sale-tag"><?php esc_html_e( 'October Sale', 'license-envato' ); ?></span>
        <p class="le-sale-text">
            <strong><?php esc_html_e( '30% OFF', 'license-envato' ); ?></strong>
            <?php esc_html_e( 'License For Envato Pro & all Codeholt products', 'license-envato' ); ?>
        </p>
        <div class="le-sale-countdown" role="timer" aria-label="<?php esc_attr_e( 'Time left in the sale', 'license-envato' ); ?>">
            <span class="dashicons dashicons-clock" aria-hidden="true"></span>
            <span class="le-sale-unit"><b data-unit="d">00</b><?php echo esc_html_x( 'd', 'days, countdown', 'license-envato' ); ?></span>
            <span class="le-sale-unit"><b data-unit="h">00</b><?php echo esc_html_x( 'h', 'hours, countdown', 'license-envato' ); ?></span>
            <span class="le-sale-unit"><b data-unit="m">00</b><?php echo esc_html_x( 'm', 'minutes, countdown', 'license-envato' ); ?></span>
            <span class="le-sale-unit"><b data-unit="s">00</b><?php echo esc_html_x( 's', 'seconds, countdown', 'license-envato' ); ?></span>
        </div>
        <div class="le-sale-actions">
            <button type="button" class="le-sale-copy" title="<?php esc_attr_e( 'Copy coupon code', 'license-envato' ); ?>" aria-label="<?php esc_attr_e( 'Copy coupon code OCTOBER30', 'license-envato' ); ?>"><span>OCTOBER30</span> <span class="dashicons dashicons-admin-page" aria-hidden="true"></span></button>
            <a class="le-sale-upgrade" href="<?php echo esc_url( 'https://codeholt.com/products/license-envato-pro/' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Upgrade', 'license-envato' ); ?> &rarr;</a>
            <span class="le-sale-status screen-reader-text" role="status" aria-live="polite"></span>
        </div>
    </div>
    <style>
        #license-envato-october-sale{position:relative;display:flex;align-items:center;flex-wrap:wrap;gap:8px 14px;box-sizing:border-box;padding:20px 40px 20px 12px;border:1px solid #263c61;border-left:4px solid #ffb367;border-radius:8px;background:linear-gradient(100deg,#0b1223,#14284a);color:#fff;font-size:13px;line-height:1.4}
        #license-envato-october-sale .le-sale-tag{padding:3px 8px;border-radius:4px;background:#ffb36724;color:#ffb367;font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;white-space:nowrap}
        #license-envato-october-sale .le-sale-text{flex:1 1 220px;margin:0;padding:0;color:#ccd7e9;font-size:13px}
        #license-envato-october-sale .le-sale-text strong{color:#fff;font-weight:800;margin-right:2px}
        #license-envato-october-sale .le-sale-countdown{display:flex;align-items:center;gap:6px;color:#ccd7e9;font-size:11px;white-space:nowrap}
        #license-envato-october-sale .le-sale-countdown .dashicons{font-size:16px;width:16px;height:16px;color:#ffb367}
        #license-envato-october-sale .le-sale-unit b{display:inline-block;min-width:20px;margin-right:1px;color:#ffb367;font-size:14px;font-weight:800;text-align:right;font-variant-numeric:tabular-nums}
        #license-envato-october-sale .le-sale-actions{display:flex;align-items:center;gap:8px}
        #license-envato-october-sale .le-sale-copy{display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border:1px dashed #79aaff;border-radius:6px;background:#ffffff14;color:#fff;font-size:12px;font-weight:700;letter-spacing:1px;cursor:pointer}
        #license-envato-october-sale .le-sale-copy:hover{background:#ffffff24}
        #license-envato-october-sale .le-sale-copy .dashicons{font-size:14px;width:14px;height:14px}
        #license-envato-october-sale .le-sale-upgrade{padding:5px 12px;border-radius:6px;background:#377ef5;color:#fff;text-decoration:none;font-size:12px;font-weight:700;white-space:nowrap}
        #license-envato-october-sale .le-sale-upgrade:hover{background:#2466d5}
        #license-envato-october-sale .le-sale-copy:focus-visible,#license-envato-october-sale .le-sale-upgrade:focus-visible{outline:2px solid #ffb367;outline-offset:2px}
        #license-envato-october-sale .notice-dismiss{top:50%;transform:translateY(-50%)}
        #license-envato-october-sale .notice-dismiss:before{color:#ccd7e9}
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
        var endAt = Date.now() + <?php echo (int) $sale_remaining; ?> * 1000;
        var units = {};
        Array.prototype.forEach.call(notice.querySelectorAll('.le-sale-countdown [data-unit]'), function (el) {
            units[el.getAttribute('data-unit')] = el;
        });
        function pad(n) {
            return n < 10 ? '0' + n : String(n);
        }
        function tick() {
            var left = Math.max(0, Math.floor((endAt - Date.now()) / 1000));
            if (left <= 0) {
                // Sale is over — drop the banner instead of showing 00:00:00:00.
                clearInterval(countdownTimer);
                notice.parentNode && notice.parentNode.removeChild(notice);
                return;
            }
            units.d.textContent = pad(Math.floor(left / 86400));
            units.h.textContent = pad(Math.floor(left % 86400 / 3600));
            units.m.textContent = pad(Math.floor(left % 3600 / 60));
            units.s.textContent = pad(left % 60);
        }
        var countdownTimer = setInterval(tick, 1000);
        tick();
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