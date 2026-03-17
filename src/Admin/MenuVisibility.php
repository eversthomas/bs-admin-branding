<?php
/**
 * Datei: src/Admin/MenuVisibility.php
 */

declare(strict_types=1);

namespace BS_Admin_Branding\Admin;

use BS_Admin_Branding\Settings\Defaults;

final class MenuVisibility {

	public function init(): void {
		add_action('admin_menu', [$this, 'filter_menu'], 999);
	}

	public function filter_menu(): void {
		if (!is_admin()) {
		 return;
		}

		if (!function_exists('wp_get_current_user')) {
			return;
		}

		$user = wp_get_current_user();

		if (!$user instanceof \WP_User) {
			return;
		}

		$roles = (array) $user->roles;

		if (in_array('administrator', $roles, true)) {
			return;
		}

		$settings = get_option(Defaults::OPTION_KEY, []);
		$settings = Defaults::merge(is_array($settings) ? $settings : []);

		$rules = $settings['role_menu_rules'] ?? [];

		if (!is_array($rules) || !$rules) {
			return;
		}

		$role_rule = null;

		foreach ($roles as $role) {
			if (isset($rules[$role])) {
				$role_rule = $rules[$role];
				break;
			}
		}

		if (!$role_rule) {
			return;
		}

		if (!empty($role_rule['hide']) && is_array($role_rule['hide'])) {
			foreach ($role_rule['hide'] as $slug) {
				remove_menu_page((string) $slug);
			}
		}

		if (!empty($role_rule['hide_submenus']) && is_array($role_rule['hide_submenus'])) {
			foreach ($role_rule['hide_submenus'] as $parent_slug => $sub_slugs) {
				if (!is_array($sub_slugs)) {
					continue;
				}

				foreach ($sub_slugs as $sub_slug) {
					remove_submenu_page((string) $parent_slug, (string) $sub_slug);
				}
			}
		}
	}
}

