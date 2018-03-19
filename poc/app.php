<?php

require( '../000-asset-handler-trait.php' );
require( '../010-asset-base.php' );
require( 'ad-asset.php' );
require( 'test-asset.php' );

$aa = new Ad_Asset;
$aa->render_localized_data();

$ta = new Test_Asset;
$ta->render_localized_data();
