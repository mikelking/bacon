<?php

/*
Plugin Name: Base Plugin Class
Version: 1.0
Description: Sets a standard class to build new plugin from.
Author: Mikel King
Text Domain: base-plugin
License: BSD(3 Clause)
License URI: http://opensource.org/licenses/BSD-3-Clause

    Copyright (C) 2014, Mikel King, olivent.com, (mikel.king AT olivent DOT com)
    All rights reserved.

    Redistribution and use in source and binary forms, with or without
    modification, are permitted provided that the following conditions are met:

        * Redistributions of source code must retain the above copyright notice, this
        list of conditions and the following disclaimer.

        * Redistributions in binary form must reproduce the above copyright notice,
        this list of conditions and the following disclaimer in the documentation
        and/or other materials provided with the distribution.

        * Neither the name of the {organization} nor the names of its
        contributors may be used to endorse or promote products derived from
        this software without specific prior written permission.

    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
    AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
    IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
    DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
    FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
    DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
    SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
    CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
    OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
    OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

//Debug::enable_error_reporting();

class AssetBase {
	const FILE_SPEC        = __FILE__;
    const PRIORITY         = 10;
	const FILTER_TAG       = '<script ';
	const ASYNC_FILTER_TAG = '<script async ';
	const DEFER_FILTER_TAG = '<script defer ';
    const LOCALIZATION_FMT = "  var %s = %s;\n";
    const LOCALIZED_HEADER = "<script async type='text/javascript'>\n\n";
    const LOCALIZED_FOOTER = "</script>\n";
    const ASSET_ID         = 'asset-name';
    const ASSET_DATA_ID    = 'asset_data';

	protected static $async_scripts = array();
	protected static $defer_scripts = array();
    public static $encoded_data_stack = array();

    public function register_local_data( array $data ) {
        $encoded_data = json_encode( $data );
        static::$encoded_data_stack[] = sprintf( static::LOCALIZATION_FMT, static::ASSET_DATA_ID, $encoded_data );
    }

    public static function render_localized_data() {
        print( static::LOCALIZED_HEADER );
        foreach ( static::$encoded_data_stack as $data_block) {
            print( $data_block . PHP_EOL );
        }
        print( static::LOCALIZED_FOOTER );
    }

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

class AtfJsAsset extends Assetbase {
	const IN_HEADER = false;
}


class BtfJsAsset extends Assetbase {
	const IN_FOOTER = true;
}
