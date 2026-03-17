<?php
/**
 * Datei: src/Admin/MenuVisibility.php
 */

declare(strict_types=1);

namespace BS_Admin_Branding\Admin;

use BS_Admin_Branding\Settings\Defaults;

final class MenuVisibility {

	private const PREVIEW_META_KEY = 'bsab_admin_branding_preview_role';

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

		$settings = get_option(Defaults::OPTION_KEY, []);
		$settings = Defaults::merge(is_array($settings) ? $settings : []);

		$rules = $settings['role_menu_rules'] ?? [];

		if (!is_array($rules) || !$rules) {
			return;
		}

		$role_rule = $this->resolve_role_rule($rules, $user);

		if ($role_rule === null) {
			return;
		}

		global $menu, $submenu;

		$menu_slugs = [];
		$legacy_menu_map = [];

		if (is_array($menu)) {
			foreach ($menu as $menu_item) {
				$menu_slug = isset($menu_item[2]) ? (string) $menu_item[2] : '';
				if ($menu_slug === '') {
					continue;
				}
				$menu_slugs[$menu_slug] = true;
				$legacy_key = sanitize_key($menu_slug);
				if ($legacy_key !== '') {
					$legacy_menu_map[$legacy_key] = $menu_slug;
				}
			}
		}

		if (!empty($role_rule['hide']) && is_array($role_rule['hide'])) {
			foreach ($role_rule['hide'] as $stored_slug) {
				$stored_slug = (string) $stored_slug;
				if ($stored_slug === '') {
					continue;
				}

				$target_slug = null;

				if (isset($menu_slugs[$stored_slug])) {
					$target_slug = $stored_slug;
				} elseif (isset($legacy_menu_map[$stored_slug])) {
					$target_slug = $legacy_menu_map[$stored_slug];
				}

				if ($target_slug !== null) {
					remove_menu_page($target_slug);
				}
			}
		}

		if (!empty($role_rule['hide_submenus']) && is_array($role_rule['hide_submenus'])) {
			foreach ($role_rule['hide_submenus'] as $stored_parent_slug => $sub_slugs) {
				if (!is_array($sub_slugs)) {
					continue;
				}

				$stored_parent_slug = (string) $stored_parent_slug;
				if ($stored_parent_slug === '') {
					continue;
				}

				$parent_slug = null;

				if (isset($menu_slugs[$stored_parent_slug])) {
					$parent_slug = $stored_parent_slug;
				} elseif (isset($legacy_menu_map[$stored_parent_slug])) {
					$parent_slug = $legacy_menu_map[$stored_parent_slug];
				}

				if ($parent_slug === null) {
					continue;
				}

				$submenu_items = $submenu[$parent_slug] ?? [];
				$submenu_slugs = [];
				$legacy_submenu_map = [];

				if (is_array($submenu_items)) {
					foreach ($submenu_items as $submenu_item) {
						$sub_slug = isset($submenu_item[2]) ? (string) $submenu_item[2] : '';
						if ($sub_slug === '') {
							continue;
						}
						$submenu_slugs[$sub_slug] = true;
						$legacy_sub_key = sanitize_key($sub_slug);
						if ($legacy_sub_key !== '') {
							$legacy_submenu_map[$legacy_sub_key] = $sub_slug;
						}
					}
				}

				foreach ($sub_slugs as $stored_sub_slug) {
					$stored_sub_slug = (string) $stored_sub_slug;
					if ($stored_sub_slug === '') {
						continue;
					}

					$sub_slug = null;

					if (isset($submenu_slugs[$stored_sub_slug])) {
						$sub_slug = $stored_sub_slug;
					} elseif (isset($legacy_submenu_map[$stored_sub_slug])) {
						$sub_slug = $legacy_submenu_map[$stored_sub_slug];
					}

					if ($sub_slug !== null) {
						remove_submenu_page($parent_slug, $sub_slug);
					}
				}
			}
		}
	}

	/**
	 * Ermittelt die effektiven Rollenregeln für den aktuellen Benutzer.
	 *
	 * Bevorzugt wird in Zukunft ein ggf. gesetzter Vorschaustatus (user_meta),
	 * fällt ansonsten auf die echten Rollen des Benutzers zurück.
	 */
	private function resolve_role_rule(array $all_rules, \WP_User $user): ?array {
		if (!$all_rules) {
			return null;
		}

		$roles = (array) $user->roles;

		// Rollen-Vorschau für Administrator:innen: wenn ein Vorschaustatus gesetzt ist,
		// wird dieser bevorzugt genutzt, ohne die echten Rollen zu verändern.
		if (in_array('administrator', $roles, true)) {
			$preview_role = \BS_Admin_Branding\Admin\RolePreview::get_preview_role_for_user($user->ID);

			if ($preview_role !== '' && isset($all_rules[$preview_role])) {
				return $all_rules[$preview_role];
			}

			// kein Vorschaustatus: Administrator behält vollen Zugriff
			return null;
		}

		foreach ($roles as $role) {
			if (isset($all_rules[$role])) {
				return $all_rules[$role];
			}
		}

		return null;
	}
}

