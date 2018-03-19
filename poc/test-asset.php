<?php

class Test_Asset extends Btf_Js_Asset {
	const ASSET_ID      = 'test-asset';
	const ASSET_DATA_ID = 'test_data';

	public $data = array(
			"lotame_id" => "LOTCC_10234"
		);

	public function __construct() {
		$this->register_local_data( $this->data );
	}

}

