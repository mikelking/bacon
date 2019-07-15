<?php
	/*
	Plugin Name: HTTP Header Controller
	Version: 1.0
	Description: A simple framework for working control headers within WordPress, because of course they have to be difficult.
	Author: Mikel King
	Text Domain: http-header-controller
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
	
	
/**
 * Class HTTP_Header_Controller
 *
.org/Plugin_API/Action_Reference/send_headers
 * @see https://developer.wordpress.org/reference/functions/feed_content_type/
 * @see http://php.net/manual/en/function.header.php
 */
class HTTP_Header_Controller extends Singleton_Base {
	const VERSION              = '1.0';
	const OUTPUT_BUFFERING     = false;
	const FEED_SLUG            = '/feed/';
	const CACHE_MAX_AGE        = 3600; //seconds
	const DEFAULT_CACHE_AGE    = 14400; //seconds
	const HOME_CACHE_AGE       = 14400; //seconds
	const TAXONOMY_CACHE_AGE   = 21600; //seconds (6 hours)
	const ARCHIVE_CACHE_AGE    = 21600; //seconds (6 hours)
	const CONTENT_CACHE_AGE    = 86400; // seconds (1 day)
	const FEED_CACHE_AGE       = 3600; //seconds


	public function __construct() {
		add_action( 'send_headers', array( $this, 'send_http_page_headers' ) );
	}

	/**
	 * Check for standard WordPres content (i.e., posts, pages & attachments)
	 * @return bool
	 */
	public function is_content() {
		return(
			is_single() ||
			is_singular() ||
			is_attachment()
		);
	}

	/**
	 * Default for HTTP page headers
	 */
	public function send_http_page_headers() {
		$this->send_http_content_header();
		$this->send_http_home_header();
		$this->send_http_taxonomy_header();
		$this->send_http_feed_header();
		$this->send_http_archive_header();
		$this->send_http_default_header();
	}

	public function is_feed() {
		return(
		stripos(
			filter_var( $_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL ),
			static::FEED_SLUG )
		);
	}

	/**
	 * Default for Feeds
	 */
	public function send_http_feed_header() {
		if ( $this->is_feed() ) {
			$header  = $this->get_the_header_content_type();
			$header .= 'charset=' . get_option( 'blog_charset' );
			header( 'Cache-Control: max-age=' . static::FEED_CACHE_AGE . ', must-revalidate', true );
			header( $header, true );
		}
	}

	/**
	 * For Taxonomy listing pages
	 */
	public function send_http_taxonomy_header() {
		if ( is_tag() || is_category() ) {
			header( 'Cache-Control: max-age=' . static::TAXONOMY_CACHE_AGE . ', must-revalidate', true );
			header( $header, true );
		}
	}

	/**
	 * For Archive listing pages
	 */
	public function send_http_archive_header() {
		if ( is_archive() ) {
			header( 'Cache-Control: max-age=' . static::ARCHIVE_CACHE_AGE . ', must-revalidate', true );
			header( $header, true );
		}
	}

	/**
	 * For standard Content pages
	 */
	public function send_http_content_header() {
		if ( $this->is_content() ) {
			header( 'Cache-Control: max-age=' . static::ARCHIVE_CACHE_AGE . ', must-revalidate', true );
			header( $header, true );
		}
	}

	/**
	 *
	 */
	public function send_http_home_header() {
		if ( is_front_page() || is_home() ) {
			header( 'Cache-Control: max-age=' . static::HOME_CACHE_AGE . ', must-revalidate', true );
			header( $header, true );
		}
	}

	/**
	 * Default max cache age for ALL pages
	 */
	public function send_http_default_header() {
		if ( ! is_admin() && ! Base_Plugin::is_cms_user() ) {
			header( 'Cache-Control: max-age=' . static::DEFAULT_CACHE_AGE . ', must-revalidate', true );
			header( $header, true );
		}
	}

	/**
	 * This method may not be required for standard content types
	 * @return string
	 */
	public function get_the_header_content_type() {
		return( 'Content-Type: ' . feed_content_type( 'rss' ) ) . '; ';
	}

	/**
	 * This may not be necessary
	 */
	public function ob_begin() {
		if ( static::OUTPUT_BUFFERING === true ) {
			ob_start();
		}
	}

	/**
	 * This may not be necessary
	 */
	public function ob_flush() {
		if ( static::OUTPUT_BUFFERING === true ) {
			ob_end_flush();
		}
	}
}
