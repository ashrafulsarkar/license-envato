<?php
/**
 * Form_Error()
 * Error handler trait
 *
 * @author: Ashraful Sarkar Naiem
 * @since 1.0.0
 */

namespace EnvatoLicenser\Error;

trait Form_Error {

    /**
     * Holds the errors
     *
     * @var array
     */
    public $errors = [];

    /**
     * Check if the form has error
     *
     * @param  string  $key
     * @return boolean
     */
    public function has_error( $key ) {
        return isset( $this->errors[$key] ) ? true : false;
    }

    /**
     * Get the error by key
     *
     * @param  key $key
     * @return string | false
     */
    public function get_error( $key ) {
        if ( isset( $this->errors[$key] ) ) {
            return $this->errors[$key];
        }

        return false;
    }
}