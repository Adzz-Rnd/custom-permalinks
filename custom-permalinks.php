<?php
/**
 * Plugin Name: Custom Permalinks
 * Plugin URI: https://wordpress.org/plugins/custom-permalinks/
 * Description: Set custom permalinks on a per-post basis
 * Version: 1.5.1
 * Author: Sami Ahmed Siddiqui
 * Author URI: https://www.custompermalinks.com/
 * Donate link: https://www.paypal.me/yasglobal
 * License: GPLv3
 *
 * Text Domain: custom-permalinks
 * Domain Path: /languages/
 *
 * @package CustomPermalinks
 */

/**
 *  Custom Permalinks - Update Permalinks of Post/Pages and Categories
 *  Copyright 2008-2019 Sami Ahmed Siddiqui <sami.siddiqui@yasglobal.com>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.

 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.

 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Make sure we don't expose any info if called directly
if ( ! defined( 'ABSPATH' ) ) {
  echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
  exit;
}

class CustomPermalinks {

  /**
   * Class constructor.
   */
  public function __construct() {
    if ( ! defined( 'CUSTOM_PERMALINKS_FILE' ) ) {
      define( 'CUSTOM_PERMALINKS_FILE', __FILE__ );
    }

    if ( ! defined( 'CUSTOM_PERMALINKS_PLUGIN_VERSION' ) ) {
      define( 'CUSTOM_PERMALINKS_PLUGIN_VERSION', '1.5.1' );
    }

    if ( ! defined( 'CUSTOM_PERMALINKS_PATH' ) ) {
      define( 'CUSTOM_PERMALINKS_PATH', plugin_dir_path( CUSTOM_PERMALINKS_FILE ) );
    }

    if ( ! defined( 'CUSTOM_PERMALINKS_BASENAME' ) ) {
      define( 'CUSTOM_PERMALINKS_BASENAME', plugin_basename( CUSTOM_PERMALINKS_FILE ) );
    }

    $this->includes();

    add_action( 'plugins_loaded', array( $this, 'loadTextDomain' ) );
  }

  /**
   * Include required files.
   *
   * @since 1.2.18
   * @access private
   */
  private function includes() {
    require_once(
      CUSTOM_PERMALINKS_PATH . 'frontend/class-custom-permalinks-frontend.php'
    );
    $cp_frontend = new CustomPermalinksFrontend();
    $cp_frontend->init();

    require_once(
      CUSTOM_PERMALINKS_PATH . 'frontend/class-custom-permalinks-form.php'
    );
    $cp_form = new CustomPermalinksForm();
    $cp_form->init();

    if ( is_admin() ) {
      require_once(
        CUSTOM_PERMALINKS_PATH . 'admin/class-custom-permalinks-admin.php'
      );
      new CustomPermalinksAdmin();

      register_activation_hook( CUSTOM_PERMALINKS_FILE, array( 'CustomPermalinks', 'pluginActivate' ) );
    }
  }

  /**
   * Loads the plugin language files.
   *
   * @since 1.2.22
   * @access public
   */
  public static function pluginActivate() {
    $role = get_role( 'administrator' );
    if ( ! empty( $role ) ) {
      $role->add_cap( 'cp_view_post_permalinks' );
      $role->add_cap( 'cp_view_category_permalinks' );
    }

    add_role(
      'custom_permalinks_manager',
      __( 'Custom Permalinks Manager' ),
      array(
        'cp_view_post_permalinks'     => true,
        'cp_view_category_permalinks' => true
      )
    );
  }

  /**
   * Loads the plugin language files.
   *
   * @since 1.2.18
   * @access public
   */
  public function loadTextDomain() {
    $current_version = get_option( 'custom_permalinks_plugin_version', -1 );
    if ( -1 === $current_version || CUSTOM_PERMALINKS_PLUGIN_VERSION < $current_version ) {
      CustomPermalinks::pluginActivate();
      update_option( 'custom_permalinks_plugin_version', CUSTOM_PERMALINKS_PLUGIN_VERSION );
    }
    load_plugin_textdomain( 'custom-permalinks', FALSE,
      basename( dirname( CUSTOM_PERMALINKS_FILE ) ) . '/languages/'
    );
  }
}

new CustomPermalinks();
