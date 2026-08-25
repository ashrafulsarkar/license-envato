<?php
/**
 * Dashboard()
 * At-a-glance overview page: the stat cards (shared with All Users) plus
 * the 5 most recently verified licenses, each with a "View" drill-down
 * for its full details, activated domains and per-domain actions.
 *
 * @author: Ashraful Sarkar Naiem
 * @since 1.5.0
 */

namespace LicenseEnvato\Admin;

class Dashboard {

    /**
     * dashboard()
     * Handles the Dashboard page
     *
     * @return void
     * @since 1.5.0
     */
    public function plugin_page() {
        $table  = new Allusers();
        $recent = $table->recent( 5 );

        $dashboardview = __DIR__ . '/views/dashboardview.php';
        if ( file_exists( $dashboardview ) ) {
            include $dashboardview; // This view will use $table->column_default() and $recent
        }
    }
}
