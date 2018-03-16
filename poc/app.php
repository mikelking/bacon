<?php

require( '../010-asset-base.php' );
require( 'ad-asset.php' );
require( 'test-asset.php' );

$aa = new AdAsset;
$ta = new TestAsset;
AssetBase::render_localized_data();
