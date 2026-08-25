<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

$license_envato_upgrade_url = license_envato_upgrade_url();

$license_envato_features = [
    [ 'label' => esc_html__( 'Envato purchase code verification', 'license-envato' ), 'free' => true ],
    [ 'label' => esc_html__( 'Per-domain license activation & deactivation', 'license-envato' ), 'free' => true ],
    [ 'label' => esc_html__( 'REST API endpoints + copy-paste client class', 'license-envato' ), 'free' => true ],
    [ 'label' => esc_html__( 'Admin license list with search', 'license-envato' ), 'free' => true ],
    [ 'label' => esc_html__( 'Secure encrypted token storage', 'license-envato' ), 'free' => true ],
    [ 'label' => esc_html__( 'Multiple domain activations per license (configurable limits)', 'license-envato' ), 'free' => false ],
    [ 'label' => esc_html__( 'Support expiry enforcement with Envato renewal re-sync', 'license-envato' ), 'free' => false ],
    [ 'label' => esc_html__( 'Email notifications + daily expiry-warning digest', 'license-envato' ), 'free' => false ],
    [ 'label' => esc_html__( 'Webhooks with signed payloads', 'license-envato' ), 'free' => false ],
    [ 'label' => esc_html__( 'Purchase code & domain blacklist', 'license-envato' ), 'free' => false ],
    [ 'label' => esc_html__( 'Activity log with CSV export', 'license-envato' ), 'free' => false ],
    [ 'label' => esc_html__( 'Manual & bulk license issuance', 'license-envato' ), 'free' => false ],
    [ 'label' => esc_html__( 'Automatic update delivery for your themes/plugins', 'license-envato' ), 'free' => false ],
    [ 'label' => esc_html__( 'Analytics dashboard', 'license-envato' ), 'free' => false ],
    [ 'label' => esc_html__( 'Priority support', 'license-envato' ), 'free' => false ],
];
?>
<div class="license-envato-getpro">

    <div class="le-pricing-intro">
        <h2><?php esc_html_e( 'Free vs Pro', 'license-envato' ); ?></h2>
        <p class="description">
            <?php esc_html_e( 'Unlock the full power of License For Envato — activation limits, automatic updates for your products, webhooks, analytics and much more.', 'license-envato' ); ?>
        </p>
    </div>

    <table class="widefat striped le-compare-table">
        <thead>
            <tr>
                <th class="le-compare-feature"><?php esc_html_e( 'Feature', 'license-envato' ); ?></th>
                <th class="le-compare-col"><?php esc_html_e( 'Free', 'license-envato' ); ?></th>
                <th class="le-compare-col le-compare-pro"><?php esc_html_e( 'Pro', 'license-envato' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $license_envato_features as $license_envato_feature ) : ?>
                <tr>
                    <td class="le-compare-feature"><?php echo esc_html( $license_envato_feature['label'] ); ?></td>
                    <td class="le-compare-col">
                        <?php if ( $license_envato_feature['free'] ) : ?>
                            <span class="dashicons dashicons-yes-alt le-yes"></span>
                        <?php else : ?>
                            <span class="dashicons dashicons-minus le-no"></span>
                        <?php endif; ?>
                    </td>
                    <td class="le-compare-col le-compare-pro"><span class="dashicons dashicons-yes-alt le-yes"></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="le-compare-feature"></td>
                <td class="le-compare-col"><em><?php esc_html_e( 'You are here', 'license-envato' ); ?></em></td>
                <td class="le-compare-col le-compare-pro">
                    <a href="<?php echo esc_url( $license_envato_upgrade_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary button-hero le-upgrade-btn">
                        <?php esc_html_e( 'Upgrade to Pro', 'license-envato' ); ?>
                    </a>
                </td>
            </tr>
        </tfoot>
    </table>

</div>
