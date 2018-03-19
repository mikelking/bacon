<?php

class Ad_Asset extends Atf_Js_Asset {
	const ASSET_ID      = 'ad-asset';
	const ASSET_DATA_ID = 'ad_data';

	public $data = array(
			"property" =>"6178",
			"siteId" => "rdg",
			"pageType" => "card",
			"contentID" => "42785",
			"pg" => "post-42785",
			"category" => "work-career",
			"lotame_id" => "LOTCC_10234"
		);

	public function __construct() {
		$this->register_local_data( $this->data );
	}

}
