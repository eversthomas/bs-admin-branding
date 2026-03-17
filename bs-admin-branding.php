<?php
/**
 * Plugin Name: BS Admin Branding
 * Plugin URI: https://bezugssysteme.de
 * Description: Eigenes Branding und Styling für den WordPress-Adminbereich, Login und Editor.
 * Version: 1.0.0
 * Author: Tom
 * Author URI: https://bezugssysteme.de
 * Text Domain: bs-admin-branding
 * Domain Path: /languages
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

define('BSAB_VERSION', '1.0.0');
define('BSAB_FILE', __FILE__);
define('BSAB_PATH', plugin_dir_path(__FILE__));
define('BSAB_URL', plugin_dir_url(__FILE__));

require_once BSAB_PATH . 'src/Plugin.php';
require_once BSAB_PATH . 'src/Admin/Assets.php';
require_once BSAB_PATH . 'src/Admin/SettingsPage.php';
require_once BSAB_PATH . 'src/Admin/FooterBranding.php';

function bsab(): BS_Admin_Branding\Plugin {
	static $instance = null;

	if ($instance === null) {
		$instance = new BS_Admin_Branding\Plugin();
	}

	return $instance;
}

add_action('plugins_loaded', static function (): void {
	bsab()->init();
});