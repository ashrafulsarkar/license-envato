<?php

namespace EnvatoLicenser\Admin;


class Documentation {

    /**
     * Plugin page handler
     *
     * @return void
     */
    public function plugin_page() {
        $documentationView = __DIR__ . '/views/documentationView.php';
        if ( file_exists( $documentationView ) ) {
            include $documentationView;
        }
    }

}