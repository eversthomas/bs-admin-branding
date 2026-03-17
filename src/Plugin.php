<?php
/**
 * Datei: src/Plugin.php
 */

declare(strict_types=1);

namespace BS_Admin_Branding;

require_once __DIR__ . '/Settings/Defaults.php';
require_once __DIR__ . '/Settings/Sanitizer.php';
require_once __DIR__ . '/Admin/Assets.php';
require_once __DIR__ . '/Admin/SettingsPage.php';
require_once __DIR__ . '/Admin/FooterBranding.php';
require_once __DIR__ . '/Admin/MenuVisibility.php';

use BS_Admin_Branding\Admin\Assets;
use BS_Admin_Branding\Admin\FooterBranding;
use BS_Admin_Branding\Admin\SettingsPage;
use BS_Admin_Branding\Admin\MenuVisibility;

final class Plugin {

	private Assets $assets;
	private SettingsPage $settings_page;
	private FooterBranding $footer_branding;
	private MenuVisibility $menu_visibility;

	public function __construct() {
		$this->assets = new Assets();
		$this->settings_page = new SettingsPage();
		$this->footer_branding = new FooterBranding();
		$this->menu_visibility = new MenuVisibility();
	}

	public function init(): void {
		$this->assets->init();
		$this->settings_page->init();
		$this->footer_branding->init();
		$this->menu_visibility->init();
	}
}