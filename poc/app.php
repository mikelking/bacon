<?php

require( '../010-asset-base.php' );
require( 'ad-asset.php' );
require( 'test-asset.php' );

$aa = new AdAsset;
$aa->render_localized_data();

$ta = new TestAsset;
$ta->render_localized_data();
