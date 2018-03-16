<?php

//require( '../010-asset-base.php' );

class TestAsset extends AssetBase {
    const ASSET_ID         = 'test-asset';
    const ASSET_DATA_ID    = 'test_data';

    public $data = array(
            "lotame_id" => "LOTCC_10234"
        );

    public function __construct() {
        $this->register_local_data( $this->data );
    }

}

