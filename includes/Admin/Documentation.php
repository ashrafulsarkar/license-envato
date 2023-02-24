<?php
/**
 * Documentation()
 * 
 * @author: Ashraful Sarkar Naiem
 * @since 1.0.0
 */

namespace LicenseEnvato\Admin;

class Documentation {

    /**
     * plugin_page()
     * call documentationView
     *
     * @return void
     * @since 1.0.0 
     */
    public function plugin_page() {
        $documentationView = __DIR__ . '/views/documentationView.php';
        if ( file_exists( $documentationView ) ) {
            include $documentationView;
        }
    }

}