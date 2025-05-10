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
        case 'action':
            if ( $item['domain'] ) {
                $page_value = '';
                if (isset($_REQUEST['page'])) {
                    $page_value = sanitize_text_field(wp_unslash($_REQUEST['page']));
                }
                
                // Add nonce for security
                $deactivate_nonce = wp_create_nonce( 'license_envato_deactivate_action_' . $item['token'] );
                return sprintf( '<a href="?page=%s&action=%s&token=%s&_wpnonce=%s" class="deactivate" onclick="if (confirm(\'Are you sure you want to Deactivate this item?\')){return true;}else{event.stopPropagation(); event.preventDefault();};">Deactivate</a>',
                    esc_attr( $page_value ),
                    'deactivate',
                    esc_attr( $item['token'] ),
                    esc_attr( $deactivate_nonce )
                );
            } else {
                return esc_html__( 'Deactivated', 'license-envato' );
            }
        default:
            return $item[$column_name];
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

        // Prepare for database query
        $sql_select_from = "SELECT `username`, `itemid`, `domain`, `purchasecode`, `token`, `supported_until` FROM {$wpdb->prefix}license_envato_userlist";
        $sql_where = "";
        $sql_order_by = " ORDER BY `id` DESC";
        $query_args = array();

        if ($this->search_by === 'purchasecode' && !empty($this->search)) {
            $sql_where = " WHERE `purchasecode` LIKE %s"; // Placeholder added here
            $query_args[] = '%' . $wpdb->esc_like($this->search) . '%';
        }
        
        // Construct the complete SQL query template
        $sql_template = $sql_select_from . $sql_where . $sql_order_by; 

        if (!empty($query_args)) {
            // If there are args, prepare the query template with them
            $executable_query = $wpdb->prepare($sql_template, $query_args); 
        } else {
            // No args, so no placeholders were added. $sql_template is a static string.
            $executable_query = $sql_template; 
        }
        
        $cache_key = 'license_envato_users_' . md5($executable_query);
        $data = wp_cache_get($cache_key, 'license_envato');
        
        if (false === $data) {
            $data = $wpdb->get_results($executable_query, ARRAY_A); 
            wp_cache_set($cache_key, $data, 'license_envato', 3600); 
        }

        $columns = $this->get_columns();
        $this->_column_headers = array( $columns, array(), array() );
        $this->set_items_per_page( 20 );
        $current_page = $this->get_pagenum();
        $total_items = count( $data );
        $data = array_slice( $data, (  ( $current_page - 1 ) * $this->per_page ), $this->per_page );
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $this->per_page,
            'total_pages' => ceil( $total_items / $this->per_page ),
        ) );
        $this->items = $data;
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