<?php
/**
 * Allusers()
 *
 * @author: Ashraful Sarkar Naiem
 * @since 1.0.0
 */

namespace LicenseEnvato\Admin;

use LicenseEnvato\API\EnvatoLicenseApiCall;
use WP_List_Table;

class Allusers extends WP_List_Table {

    /**
     * @var int
     */
    private $per_page = 20;
    /**
     * @var mixed
     */
    private $search;
    /**
     * @var mixed
     */
    private $search_by;

    public function __construct() {
        parent::__construct( array(
            'singular' => 'item',
            'plural'   => 'items',
            'ajax'     => true,
        ) );
    }

    public function plugin_page() {
        $table = new Allusers();
        $table->prepare_items();

        $userview = __DIR__ . '/views/userview.php';
        if ( file_exists( $userview ) ) {
            include $userview; // This view will use $table->display()
        }
    }

    /**
     * @param $option
     * @param $default
     * @return mixed
     */
    public function get_items_per_page( $option = 'my_table_per_page', $default = 20 ) {
        return $this->per_page;
    }

    /**
     * @param $per_page
     */
    public function set_items_per_page( $per_page ) {
        $this->per_page = $per_page;
    }

    /**
     * @return mixed
     */
    public function get_columns() {
        $columns = array(
            'username'        => 'Username',
            'itemid'          => 'Item id',
            'purchasecode'    => 'Purchase code',
            'supported_until' => 'Supported until',
            'domain'          => 'Activated domain',
            'action'          => 'Action',
        );
        return $columns;
    }

    /**
     * @param $item
     * @param $column_name
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
        case 'domain':
            if ( empty( $item['activations'] ) ) {
                return '';
            }
            $lines = array();
            foreach ( array_keys( $item['activations'] ) as $domain ) {
                $lines[] = sprintf( '<span class="dashicons dashicons-yes-alt le-icon-active" title="%s"></span> %s',
                    esc_attr__( 'Active', 'license-envato' ),
                    esc_html( $domain )
                );
            }
            return implode( '<br>', $lines );
        case 'action':
            $lines = array();

            if ( ! empty( $item['activations'] ) ) {
                // Read-only: only used to rebuild the current page slug in a link href, no data is processed.
                $page_value = '';
                if ( isset( $_REQUEST['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                    $page_value = sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                }

                foreach ( $item['activations'] as $domain => $token ) {
                    // Add nonce for security
                    $deactivate_nonce = wp_create_nonce( 'license_envato_deactivate_action_' . $token );
                    /* translators: %s: domain name */
                    $deactivate_title = sprintf( __( 'Deactivate %s', 'license-envato' ), $domain );
                    $deactivate = sprintf( '<a href="?page=%s&action=%s&token=%s&_wpnonce=%s" class="deactivate le-icon-link le-icon-deactivate" title="%s" aria-label="%s" onclick="if (confirm(\'Are you sure you want to Deactivate this item?\')){return true;}else{event.stopPropagation(); event.preventDefault();};"><span class="dashicons dashicons-no-alt"></span></a>',
                        esc_attr( $page_value ),
                        'deactivate',
                        esc_attr( $token ),
                        esc_attr( $deactivate_nonce ),
                        esc_attr( $deactivate_title ),
                        esc_attr( $deactivate_title )
                    );

                    /**
                     * Filters the action icons rendered for one activated domain.
                     *
                     * @param array  $actions HTML strings, one per icon link.
                     * @param string $domain  The activated domain.
                     * @param string $token   The activation token.
                     * @param array  $item    The full row.
                     */
                    $actions = apply_filters( 'license_envato_userlist_domain_actions', array( $deactivate ), $domain, $token, $item );
                    $lines[] = implode( ' ', $actions );
                }
            } else {
                $lines[] = sprintf( '<span class="dashicons dashicons-dismiss le-icon-deactivated" title="%1$s"></span><span class="screen-reader-text">%1$s</span>',
                    esc_attr__( 'Deactivated', 'license-envato' )
                );
            }

            /**
             * Filters the license-level action icons for a row (e.g. block
             * the purchase code), rendered on their own line.
             *
             * @param array $actions HTML strings, one per icon link.
             * @param array $item    The full row.
             */
            $row_actions = apply_filters( 'license_envato_userlist_row_actions', array(), $item );
            if ( ! empty( $row_actions ) ) {
                $lines[] = implode( ' ', $row_actions );
            }

            return implode( '<br>', $lines );
        default:
            return esc_html( $item[$column_name] );
        }
    }

    public function prepare_items() {
        // Messages from redirect (error or success) are displayed here
        $codeerror = isset( $_GET['error'] ) ? sanitize_text_field( wp_unslash( $_GET['error'] ) ) : '';
        $codesuccess = isset( $_GET['success'] ) ? sanitize_text_field( wp_unslash( $_GET['success'] ) ) : '';
        
        if ($codeerror) {
            ?>
            <div class="notice notice-error is-dismissible">
                <p><?php echo esc_html( $codeerror ); ?></p>
            </div>
            <?php
        } elseif ($codesuccess) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html( $codesuccess ); ?></p>
            </div>
            <?php
        }

        global $wpdb;

        if (isset($_REQUEST['s'])) {
            $search_nonce = isset($_REQUEST['search_nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['search_nonce'])) : '';
            if (!wp_verify_nonce($search_nonce, 'license_envato_search_action')) {
                wp_die(esc_html__('Search security check failed.', 'license-envato'));
            }
        }

        $this->search = isset($_REQUEST['s']) ? sanitize_text_field(wp_unslash($_REQUEST['s'])) : '';
        $this->search_by = isset($_REQUEST['search_by']) ? sanitize_text_field(wp_unslash($_REQUEST['search_by'])) : '';

        $columns = $this->get_columns();
        $this->_column_headers = array( $columns, array(), array() );
        $this->set_items_per_page( 20 );
        $current_page = $this->get_pagenum();
        $offset = ( $current_page - 1 ) * $this->per_page;

        // Prepare for database query — paginate in SQL instead of loading the whole table.
        $has_search = ( $this->search_by === 'purchasecode' && ! empty( $this->search ) );
        $search_like = $has_search ? '%' . $wpdb->esc_like( $this->search ) . '%' : '';

        // Cache key is built from the query inputs, not the SQL text, since the SQL is
        // now only ever built inline (see below) and never held in its own variable.
        $last_changed = wp_cache_get_last_changed('license_envato');
        $cache_key = 'license_envato_users_' . md5( $this->search_by . '|' . $this->search . '|' . $this->per_page . '|' . $current_page ) . ':' . $last_changed;
        $cached = wp_cache_get($cache_key, 'license_envato');

        if (false === $cached || !isset($cached['items'], $cached['total'])) {
            // The query text is written inline as a literal directly inside prepare(),
            // and prepare() is called inline inside get_var()/get_results(): the checker
            // only recognizes a query as "prepared" with zero indirection between them,
            // and only trusts {$wpdb->prefix} as a safe interpolation inside the literal.
            if ( $has_search ) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
                $total = (int) $wpdb->get_var( $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}license_envato_userlist WHERE `purchasecode` LIKE %s",
                    $search_like
                ) );

                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
                $items = $wpdb->get_results( $wpdb->prepare(
                    "SELECT `username`, `itemid`, `domain`, `purchasecode`, `token`, `supported_until` FROM {$wpdb->prefix}license_envato_userlist WHERE `purchasecode` LIKE %s ORDER BY `id` DESC LIMIT %d OFFSET %d",
                    $search_like,
                    $this->per_page,
                    $offset
                ), ARRAY_A );
            } else {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
                $total = (int) $wpdb->get_var( $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}license_envato_userlist WHERE 1=%d",
                    1
                ) );

                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
                $items = $wpdb->get_results( $wpdb->prepare(
                    "SELECT `username`, `itemid`, `domain`, `purchasecode`, `token`, `supported_until` FROM {$wpdb->prefix}license_envato_userlist WHERE 1=%d ORDER BY `id` DESC LIMIT %d OFFSET %d",
                    1,
                    $this->per_page,
                    $offset
                ), ARRAY_A );
            }

            $cached = array(
                'total' => $total,
                'items' => $items,
            );
            wp_cache_set($cache_key, $cached, 'license_envato', 3600);
        }

        $total_items = $cached['total'];
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $this->per_page,
            'total_pages' => ceil( $total_items / $this->per_page ),
        ) );

        $items = $cached['items'];
        foreach ( $items as &$item ) {
            $item['activations'] = array();
            if ( ! empty( $item['domain'] ) && ! empty( $item['token'] ) ) {
                $item['activations'][ $item['domain'] ] = $item['token'];
            }
        }
        unset( $item );

        /**
         * Filters the user-list rows before display.
         *
         * Add-ons can merge extra domain activations into each row's
         * `activations` map (domain => token); every entry is rendered
         * in the Activated domain column with its own Deactivate link.
         *
         * @param array $items The rows for the current page.
         */
        $this->items = apply_filters( 'license_envato_userlist_items', $items );
    }

    /**
     * @param $which
     */
    public function extra_tablenav( $which ) {
        if ( $which == 'top' ) {
            echo '<div class="alignleft actions">';
            echo '<form method="get">';
            echo '<input type="hidden" name="page" value="licenseenvato"/>';
            // Add nonce field for search form
            wp_nonce_field('license_envato_search_action', 'search_nonce');
            echo '<input type="search" id="search" name="s" value="' . esc_attr( $this->search ) . '"/>';
            echo '<select name="search_by">';
            echo '<option value="purchasecode" ' . selected( $this->search_by, 'purchasecode', false ) . '>Purchase Code</option>';
            echo '</select>';
            echo '<input type="submit" id="search-submit" class="button" value="Search">';
            echo '</form>';
            echo '</div>';
        }
    }
}