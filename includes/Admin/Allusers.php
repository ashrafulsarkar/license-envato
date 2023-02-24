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
        $userview = __DIR__ . '/views/userview.php';
        if ( file_exists( $userview ) ) {
            include $userview;
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
                return sprintf( '<a href="?page=%s&action=%s&purchasecode=%s" class="deactivate"  onclick="if (confirm(\'Are you sure you want to Deactivate this item?\')){return true;}else{event.stopPropagation(); event.preventDefault();};">Deactivate</a>', $_REQUEST['page'], 'deactivate', $item['purchasecode'] );
            } else {
                return esc_html__( 'Deactivated', 'licenseenvato' );
            }
        default:
            return $item[$column_name];
        }
    }

    public function prepare_items() {

        $deactivate = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
        if ( $deactivate == 'deactivate' ) {

            $purchasecode = isset( $_REQUEST['purchasecode'] ) ? $_REQUEST['purchasecode'] : '';
            $code = [];
            $code['code'] = $purchasecode;
            $EnvatoLicenseApiCall = new EnvatoLicenseApiCall;
            $envatolicense_deactive = $EnvatoLicenseApiCall->envatolicense_deactive( $code );
            $license_envato_Error = isset( $envatolicense_deactive->errors ) ? $envatolicense_deactive->errors : '';
            
            if ( $license_envato_Error ) {
                if ( $license_envato_Error['already_deactivated'] ) {
                    $message = urlencode($license_envato_Error['already_deactivated'][0]);
                    $url = admin_url("admin.php?page=envatolicenser&error={$message}");
                    echo"<script> window.location='{$url}';</script>";
                } elseif ( $license_envato_Error['deactivated_error'] ) {
                    $message = urlencode($license_envato_Error['deactivated_error'][0]);
                    $url = admin_url("admin.php?page=envatolicenser&error={$message}");
                    echo"<script> window.location='{$url}';</script>";
                } elseif ( $license_envato_Error['invalid_code'] ) {
                    $message = urlencode($license_envato_Error['invalid_code'][0]);
                    $url = admin_url("admin.php?page=envatolicenser&error={$message}");
                    echo"<script> window.location='{$url}';</script>";
                } elseif ( $license_envato_Error['parameter_request'] ) {
                    $message = urlencode($license_envato_Error['parameter_request'][0]);
                    $url = admin_url("admin.php?page=envatolicenser&error={$message}");
                    echo"<script> window.location='{$url}';</script>";
                } else {
                    $url = admin_url("admin.php?page=envatolicenser&error=Something wrong! Check Error!");
                    echo"<script> window.location='{$url}';</script>";
                }

            } elseif ( $envatolicense_deactive['deactive'] ) {
                $message = urlencode($envatolicense_deactive['deactive']);
                $url = admin_url("admin.php?page=envatolicenser&success={$message}");
                echo"<script> window.location='{$url}';</script>";
            } else {
                $url = admin_url("admin.php?page=envatolicenser&error=Something+wrong!");
                echo"<script> window.location='{$url}';</script>";
            }
        }

        $codeerror = isset( $_GET['error'] ) ? $_GET['error'] : '';
        $codesuccess = isset( $_GET['success'] ) ? $_GET['success'] : '';
        
        if ($codeerror) {
            ?>
            <div class="notice notice-error is-dismissible">
                <p><?php echo $codeerror; ?></p>
            </div>
            <?php
        }elseif($codesuccess){
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo $codesuccess; ?></p>
            </div>
            <?php
        }

        global $wpdb;

        $query = "SELECT `username`, `itemid`, `domain`, `purchasecode`, `supported_until` FROM `{$wpdb->prefix}license_envato_userlist`";

        $this->search = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';
        $this->search_by = isset( $_REQUEST['search_by'] ) ? $_REQUEST['search_by'] : '';

        // Apply search filter for Purchase code
        if ( $this->search_by == 'purchasecode' ) {
            $query .= $wpdb->prepare(
                " WHERE `purchasecode` LIKE '%%%s%%'",
                $this->search
            );
        }
        $query .= " ORDER BY `id` DESC";

        // Retrieve data from your custom database
        $data = $wpdb->get_results( $query, ARRAY_A );

        // Define the columns for the table
        $columns = $this->get_columns();

        // Set the columns and data for the table
        $this->_column_headers = array( $columns, array(), array() );

        // Set the number of items to display per page
        $this->set_items_per_page( 20 );

        // Set the current page
        $current_page = $this->get_pagenum();

        // Get the total number of items
        $total_items = count( $data );

        // Slice the data to display only the items for the current page
        $data = array_slice( $data, (  ( $current_page - 1 ) * $this->per_page ), $this->per_page );

        // Set the pagination arguments
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
            // Add the search input field
            echo '<div class="alignleft actions">';
            echo '<form method="get">';
            echo '<input type="hidden" name="page" value="licenseenvato"/>';
            echo '<input type="search" id="search" name="s" value="' . $this->search . '"/>';

            // Add the search by dropdown
            echo '<select name="search_by">';
            echo '<option value="purchasecode" ' . selected( $this->search_by, 'purchasecode', false ) . '>Purchase Code</option>';
            echo '</select>';

            // Add the submit button
            echo '<input type="submit" id="search-submit" class="button" value="Search">';
            echo '</form>';
            echo '</div>';

        }
    }
}