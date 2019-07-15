<?php

/*
Plugin Name:  Base Manager Admin Class
Version: 1.0
Description: This calss should make it simple to create a manger for your various options in the CMS through an extensible means. Keep in mind that it is a basic first iteration and I shall endeavor to improve it over time.
Author: Mikel King
Text Domain: base-manager-admin
License: BSD(3 Clause)
License URI: http://opensource.org/licenses/BSD-3-Clause

	Copyright (C) 2019, Mikel King, olivent.com, (mikel.king AT olivent DOT com)
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

class Base_Manager_Admin {
	const VERSION       = "1.0";
	const PAGE_TITLE    = 'Base Manager';
	const MENU_SLUG     = 'base-manager';
	const METHOD_PREFIX = 'base_manager';
	const MAX_WIDTH     = '100%'; // the % in the format causes issues
    const LINE_WIDTH    = '700px';
	const FIELD_FMT     = '<input type="text" id="%s" name="%s" value="%s" style="max-width:%s; width: %s;" />';
	const SECTION_INFO  = '<b>Note</b> Only add override URLs to this section for testing. Also all registration system JavaScript URLs will be enqueued as deferred in the footer.';

	public $options;

	/*
	 * Field label => field form/db id
	 */
	public $fields= array(
			'Base option 1' => 'base-option-one',
			'Base option 2' => 'base-option-two',
			'Base option 3' => 'base-option-three',
	);

	public function __construct() {
		$this->get_admin_options();
		add_action( 'admin_menu', array( $this, 'admin_settings' ) );
		add_action( 'admin_init', array( $this, 'admin_page_init' ) );
	}

	/**
	 * @return array|null
	 */
	public function get_options() {
		if ( is_array( $this->options ) && ! empty( $this->options ) ) {
			return( $this->options );
		}
		return null;
	}

	/**
	 * Register the admin page and setup the menu in WordPress
	 */
	public function admin_settings() {
		add_options_page(
			static::PAGE_TITLE,
			static::PAGE_TITLE,
			'manage_options',
			static::MENU_SLUG,
			array( $this, 'show_admin_page' )
		);
	}

	/**
	 * Render the admin page in the WordPress CMS
	 */
	public function show_admin_page() {
		?>
		<div class="wrap">
			<h1><?php static::PAGE_TITLE ?> Admin</h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( static::METHOD_PREFIX . '_options' );
				do_settings_sections( static::METHOD_PREFIX . '_settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Reads the options stored in WordPress
	 */
	public function get_admin_options() {
		foreach ($this->fields as $field) {
			$this->options[$field] = get_option( static::MENU_SLUG . '-' . $field );
		}
	}

	/**
	 * Renders the input fields with the stored data (if any) retrieved from WordPress
	 */
	public function add_fields() {
		foreach ( $this->fields as $key => $field ) {
			add_settings_field(
				static::MENU_SLUG . '-' . $field, // ID
				$key, // Title
				array( $this, 'option_validator' ), // Callback
				static::METHOD_PREFIX . '_settings', // Page
				static::METHOD_PREFIX . '_main' // Section
			);
		}
	}

	/**
	 * Initializes the admin page assigning the fields and labels
	 */
	public function admin_page_init() {
		foreach ( $this->fields as $key => $field ) {
			register_setting(
				static::METHOD_PREFIX . '_options', // Option group
				static::MENU_SLUG . '-' . $field, // Option name
				'this is a test....'
			);

			add_settings_section(
				static::METHOD_PREFIX . '_main', // ID
				static::PAGE_TITLE . ' Settings', // Title
				array($this, 'print_section_info'), // Callback
				static::METHOD_PREFIX . '_settings' // Page
			);

			add_settings_field(
				static::MENU_SLUG . '-' . $field, // ID
				$key, // Title
				array( $this, 'option_validator' ), // Callback
				static::METHOD_PREFIX . '_settings', // Page
				static::METHOD_PREFIX . '_main', // Section
				$field
			);
		}
	}

	/**
	 * Prints the admin page section heading
	 */
	public function print_section_info() {
		echo wpautop( static::SECTION_INFO );
	}

	/**
     * Validates the stored/user inputted field data (if any) and outputs it in the field in the CMS
	 * @param $field
	 */
	public function option_validator( $field ) {
		$option = isset( $this->options[$field] ) ? esc_attr( $this->options[$field] ) : '';
		printf(
            static::FIELD_FMT,
            static::MENU_SLUG . '-' . $field,
            static::MENU_SLUG . '-' . $field,
            $option,
            static::MAX_WIDTH,
            static::LINE_WIDTH
		);
	}
}
