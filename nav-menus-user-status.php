<?php
/**
 * Plugin Name:  Nav Menus User Status
 * Plugin URI:   https://github.com/ControlledChaos/nav-menus-user-status
 * Description:  Quickly customize your menus with a user's name & avatar, or show items based on user role.
 * Version:      0.2.9
 * Text Domain:  user-menus
 * Minimum PHP: 5.6
 * Minimum WP: 4.6
 *
 * @author      Daniel Iser
 * @package     User Menus
 * @copyright   Copyright (c) 2019, Code Atlantic LLC
 *
 * Prior Work Credits. Big thanks to the following:
 * - No Conflict Nav Menu Walker (Modified) - Nav Menu Roles @helgatheviking
 * - Menu Importer (Modified) - Kathy Darling
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class JP_User_Menus
 */
class JP_User_Menus {

	/**
	 * Plugin Name
	 *
	 * @var string
	 */
	public static $NAME = 'User Menus';

	/**
	 * Plugin Version
	 *
	 * @var string
	 */
	public static $VER = '1.2.9';

	/**
	 * Minimum PHP version
	 *
	 * @var string
	 */
	public static $MIN_PHP_VER = '5.6';

	/**
	 * Minimum WP version
	 *
	 * @var string
	 */
	public static $MIN_WP_VER = '4.6';

	/**
	 * Plugin URL
	 *
	 * @var string
	 */
	public static $URL = '';

	/**
	 * Plugin Directory
	 *
	 * @var string
	 */
	public static $DIR = '';

	/**
	 * Plugin File
	 *
	 * @var string
	 */
	public static $FILE = '';

	/**
	 * Plugin Template Directory
	 *
	 * @var string
	 */
	public static $TEMPLATE_PATH = 'jp/user-menus/';

	/**
	 * Text Domain
	 *
	 * @var string
	 */
	public static $TD = 'user-menus';

	/**
	 * Instance of the plugin class
	 *
	 * @var         JP_User_Menus $instance The one true JP_User_Menus
	 */
	private static $instance;

	/**
	 * Get active instance
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      object self::$instance The one true JP_User_Menus
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new static();
			self::$instance->setup_constants();

			add_action( 'plugins_loaded', [ self::$instance, 'load_textdomain' ] );

			self::$instance->includes();
		}

		return self::$instance;
	}

	/**
	 * Setup plugin constants
	 *
	 * @since       1.0.0
	 */
	private function setup_constants() {
		self::$DIR  = self::$instance->plugin_path();
		self::$URL  = self::$instance->plugin_url();
		self::$FILE = __FILE__;
	}

	/**
	 * Include necessary files
	 *
	 * @since       1.0.0
	 */
	private function includes() {
		// Menu Items.
		require_once self::$DIR . 'includes/classes/menu/item.php';
		require_once self::$DIR . 'includes/classes/menu/items.php';
		require_once self::$DIR . 'includes/classes/user/codes.php';
		if ( is_admin() ) {
			// Admin Menu Editor.
			require_once self::$DIR . 'includes/classes/admin/menu-editor.php';
			require_once self::$DIR . 'includes/classes/admin/menu-settings.php';
			require_once self::$DIR . 'includes/classes/admin/menu-importer.php';
		} else {
			// Site Menu Filter.
			require_once self::$DIR . 'includes/classes/site/menus.php';
		}
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return plugin_dir_path( __FILE__ );
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return plugins_url( '/', __FILE__ );
	}

	/**
	 * Plugin Activation hook function to check for Minimum PHP and WordPress versions
	 */
	public static function activation_check() {
		global $wp_version;

		if ( version_compare( PHP_VERSION, self::$MIN_PHP_VER, '<' ) ) {
			$flag = 'PHP';
		} elseif ( version_compare( $wp_version, self::$MIN_WP_VER, '<' ) ) {
			$flag = 'WordPress';
		} else {
			return;
		}

		$version = 'PHP' === $flag ? self::$MIN_PHP_VER : self::$MIN_WP_VER;

		// Deactivate automatically due to insufficient PHP or WP Version.
		deactivate_plugins( basename( __FILE__ ) );

		/* translators: 1: Plugin Name, 2: Flagged software (PHP or WP), 3: PHP or WordPress version */
		$notice = sprintf( __( 'The %4$s %1$s %5$s plugin requires %2$s version %3$s or greater.', 'user-menus' ), self::$NAME, $flag, $version, '<strong>', '</strong>' );

		/* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
		wp_die( sprintf( '<p>%s</p>', $notice ), __( 'Plugin Activation Error', 'user-menus' ), [
			'response'  => 200,
			'back_link' => true,
		] );
	}

	/**
	 * Internationalization
	 *
	 * @since       1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'user-menus' );
	}

}

/**
 * Globally available function to get plugin instance.
 *
 * @return object
 */
function jp_user_menus() {
	return JP_User_Menus::instance();
}

jp_user_menus();

// Ensure plugin & environment compatibility.
register_activation_hook( __FILE__, [ 'JP_User_Menus', 'activation_check' ] );
