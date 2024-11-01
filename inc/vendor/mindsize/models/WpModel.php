<?php
namespace SFR\Models;

use WeDevs\ORM\Eloquent\Model;

class WpModel extends Model {
    /**
     * Bruteforce the table name to work with WordPress
     *
     * @return string
     */
    public function getTable() {
       /* if( !empty( $this->table ) ) {
            return $this->table;
        }*/

        $table = str_replace( '\\', '', $this->to_snake_case(class_basename( $this )) );
        return $this->getConnection()->db->prefix . 'sfr_' .  $table . 's';
    }

    /**
     * Convert the camelCase class name to snake_case of the db table names.
     *
     * @param string $theCamel
     * @return string
     */
    private function to_snake_case($theCamel) {
        // Convert first letter to lowercase to avoid the second step
        $snake = lcfirst($theCamel);
        // Prefix uppercase letters with an underscore
        $snake = preg_replace('/([A-Z])/', '_$1', $snake);
        // Convert all to lowercase
        $snake = strtolower($snake);
        return $snake;
    }
}