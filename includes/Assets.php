<?php
/**
 * Assets()
 * Assets handlers class
 *
 * @author: Ashraful Sarkar Naiem
 * @since 1.0.0
 */

namespace EnvatoLicenser;

class Assets {

    /**
     * Class constructor
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', [$this, 'register_assets'] );
        add_action( 'admin_enqueue_scripts', [$this, 'register_assets'] );
    }

    /**
     * All available styles
     *
     * @return array
     */
    public function get_styles() {
        return [
            'envatolicenser-admin-style' => [
                'src'     => LICENSE_ENVATO_ASSETS . '/css/admin.css',
                'version' => filemtime( LICENSE_ENVATO_FILE_PATH . '/assets/css/admin.css' ),
            ],
        ];
    }

    /**
     * Register scripts and styles
     *
     * @return void
     */
    public function register_assets() {
        $styles = $this->get_styles();

        foreach ( $styles as $handle => $style ) {
            $deps = isset( $style['deps'] ) ? $style['deps'] : false;
            wp_register_style( $handle, $style['src'], $deps, $style['version'] );
        }
    }
}