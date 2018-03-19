<?php


trait Asset_Handler {

	public function get_asset_url( $asset_file ) {
		return( plugins_url( $asset_file, static::FILE_SPEC ));
	}

	/**
	 * @param $asset_slug
	 */
	public static function set_async_assets( $asset_slug ) {
		$buffer = self::$async_scripts;
		if ( isset( $asset_slug ) ) {
			$buffer[] = $asset_slug;
			self::$async_scripts = array_unique( $buffer, SORT_STRING );
		}
	}

	/**
	 * This will only modify the matching handle found in
	 * the async_scripts array then we will modify the script call
	 *
	 * @param $tag
	 * @param $handle
	 * @return string
	 */
	public static function async_filter_tag( $tag, $handle, $src ) {
		$key = self::key_finder( $handle, static::$async_scripts );
		if ( $handle && in_array( $handle, static::$async_scripts ) ) {
			static::$async_scripts[$key] = $handle . '-DONE' ;
			return ( str_replace( static::FILTER_TAG, static::ASYNC_FILTER_TAG, $tag ) );
		}
		return( $tag );
	}

	/**
	 * @param $asset_slug
	 */
	public static function set_defer_assets( $asset_slug ) {
		$buffer = self::$defer_scripts;
		if ( isset( $asset_slug ) ) {
			$buffer[] = $asset_slug;
			self::$defer_scripts = array_unique( $buffer, SORT_STRING );
		}
	}

	/**
	 * This will only modify the matching handle found in
	 * the defer_scripts array then we will modify the script call
	 *
	 * @param $tag
	 * @param $handle
	 * @return string
	 */
	public static function defer_filter_tag( $tag, $handle, $src ) {
		$key = self::key_finder( $handle, static::$defer_scripts );
		if ( $handle && in_array( $handle, static::$defer_scripts ) ) {
			static::$defer_scripts[$key] = $handle . '-DONE' ;
			return ( str_replace( static::FILTER_TAG, static::DEFER_FILTER_TAG, $tag ) );
		}
		return( $tag );
	}

	public static function key_finder( $handle, $stack ) {
		return( array_search( $handle, $stack ) );
	}

}


