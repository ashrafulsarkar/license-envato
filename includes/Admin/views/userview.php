<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;
?>
<style>
    .wp-list-table .column-domain .dashicons,
    .wp-list-table .column-action .dashicons {
        font-size: 18px;
        width: 18px;
        height: 18px;
        vertical-align: text-bottom;
    }
    .wp-list-table a.le-icon-link { text-decoration: none; }
    .wp-list-table .le-icon-active { color: #00a32a; }
    .wp-list-table .le-icon-deactivate { color: #d63638; }
    .wp-list-table .le-icon-deactivated { color: #a7aaad; }
</style>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'User List', 'license-envato' ); ?></h1>
    <hr class="wp-header-end">
    <?php
    $table->display();
    ?>
</div>