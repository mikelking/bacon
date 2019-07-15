<?php

require( __DIR__ . '/environment-finder.php' );

/**
 * Class WPE_Environment_Finder
 */
class WPE_Environment_Finder implements Environment_Finder {
    private $clusters = array(
        'events' => array(
            'wp_hmevents'   => 'prod',
            'wp_hmestaging' => 'stage',
            'wp_devhme'     => 'dev',
        ),
        'bizmed' => array(
            'wp_haymarketwp' => 'prod',
            'wp_hmwpstaging' => 'stage',
            'wp_hmwpdev'     => 'dev',
        ),
        'bizmed-stg' => array(
            'wp_haymarketstage' => 'prod',
            'wp_hmsstage'       => 'stage',
            'hmsdev'            => 'dev',
        ),
        'local' => array(
            'events-cluster_lcl_development' => 'lcl',
            'example_com_development' => 'lcl',
        ),
    );
    
    public function findEnvironment() {
        $it = new RecursiveIteratorIterator( new RecursiveArrayIterator( $this->clusters ) );
        foreach ( $it as $key => $val ) {
            if ( $key === DB_NAME ) {
                return $val;
            }
        }
    }
}
