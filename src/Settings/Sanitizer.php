<?php
/**
 * Datei: src/Settings/Sanitizer.php
 */

declare(strict_types=1);

namespace BS_Admin_Branding\Settings;

final class Sanitizer {

	public static function sanitize(array $input): array {
		$defaults = Defaults::get();

		$output = [];

		$output['enable_admin_css']       = !empty($input['enable_admin_css']) ? '1' : '0';
		$output['enable_login_css']       = !empty($input['enable_login_css']) ? '1' : '0';
		$output['enable_editor_css']      = !empty($input['enable_editor_css']) ? '1' : '0';
		$output['enable_figtree_font']    = !empty($input['enable_figtree_font']) ? '1' : '0';
		$output['enable_footer_branding'] = !empty($input['enable_footer_branding']) ? '1' : '0';

		$output['sidebar_width']     = self::sanitize_css_size($input['sidebar_width'] ?? $defaults['sidebar_width'], $defaults['sidebar_width']);
		$output['content_max_width'] = self::sanitize_css_size_or_keyword($input['content_max_width'] ?? $defaults['content_max_width'], $defaults['content_max_width']);
		$output['border_radius']     = self::sanitize_css_size($input['border_radius'] ?? $defaults['border_radius'], $defaults['border_radius']);

		$output['color_sidebar_bg']         = self::sanitize_hex($input['color_sidebar_bg'] ?? $defaults['color_sidebar_bg'], $defaults['color_sidebar_bg']);
		$output['color_sidebar_submenu_bg'] = self::sanitize_hex($input['color_sidebar_submenu_bg'] ?? $defaults['color_sidebar_submenu_bg'], $defaults['color_sidebar_submenu_bg']);
		$output['color_sidebar_submenu_hover_bg'] = self::sanitize_hex($input['color_sidebar_submenu_hover_bg'] ?? $defaults['color_sidebar_submenu_hover_bg'], $defaults['color_sidebar_submenu_hover_bg']);
		$output['color_sidebar_text']       = self::sanitize_hex($input['color_sidebar_text'] ?? $defaults['color_sidebar_text'], $defaults['color_sidebar_text']);
		$output['color_sidebar_hover']      = self::sanitize_hex($input['color_sidebar_hover'] ?? $defaults['color_sidebar_hover'], $defaults['color_sidebar_hover']);
		$output['color_sidebar_hover_text'] = self::sanitize_hex($input['color_sidebar_hover_text'] ?? $defaults['color_sidebar_hover_text'], $defaults['color_sidebar_hover_text']);
		$output['color_sidebar_active']     = self::sanitize_hex($input['color_sidebar_active'] ?? $defaults['color_sidebar_active'], $defaults['color_sidebar_active']);
		$output['color_sidebar_active_text'] = self::sanitize_hex($input['color_sidebar_active_text'] ?? $defaults['color_sidebar_active_text'], $defaults['color_sidebar_active_text']);

		$output['color_adminbar_bg']   = self::sanitize_hex($input['color_adminbar_bg'] ?? $defaults['color_adminbar_bg'], $defaults['color_adminbar_bg']);
		$output['color_adminbar_text'] = self::sanitize_hex($input['color_adminbar_text'] ?? $defaults['color_adminbar_text'], $defaults['color_adminbar_text']);

		$output['color_content_bg'] = self::sanitize_hex($input['color_content_bg'] ?? $defaults['color_content_bg'], $defaults['color_content_bg']);
		$output['color_card_bg']    = self::sanitize_hex($input['color_card_bg'] ?? $defaults['color_card_bg'], $defaults['color_card_bg']);
		$output['color_card_text']  = self::sanitize_hex($input['color_card_text'] ?? $defaults['color_card_text'], $defaults['color_card_text']);

		$output['color_accent']       = self::sanitize_hex($input['color_accent'] ?? $defaults['color_accent'], $defaults['color_accent']);
		$output['color_accent_hover'] = self::sanitize_hex($input['color_accent_hover'] ?? $defaults['color_accent_hover'], $defaults['color_accent_hover']);

		$output['color_border']              = self::sanitize_hex($input['color_border'] ?? $defaults['color_border'], $defaults['color_border']);
		$output['color_table_header_bg']     = self::sanitize_hex($input['color_table_header_bg'] ?? $defaults['color_table_header_bg'], $defaults['color_table_header_bg']);
		$output['color_table_row_hover']     = self::sanitize_hex($input['color_table_row_hover'] ?? $defaults['color_table_row_hover'], $defaults['color_table_row_hover']);
		$output['color_text_heading']        = self::sanitize_hex($input['color_text_heading'] ?? $defaults['color_text_heading'], $defaults['color_text_heading']);
		$output['color_text_input']          = self::sanitize_hex($input['color_text_input'] ?? $defaults['color_text_input'], $defaults['color_text_input']);
		$output['color_button_border_hover'] = self::sanitize_hex($input['color_button_border_hover'] ?? $defaults['color_button_border_hover'], $defaults['color_button_border_hover']);
		$output['color_button_border']       = self::sanitize_hex($input['color_button_border'] ?? $defaults['color_button_border'], $defaults['color_button_border']);
		$output['color_button_bg']           = self::sanitize_hex($input['color_button_bg'] ?? $defaults['color_button_bg'], $defaults['color_button_bg']);
		$output['color_button_hover_bg']     = self::sanitize_hex($input['color_button_hover_bg'] ?? $defaults['color_button_hover_bg'], $defaults['color_button_hover_bg']);
		$output['color_button_text']         = self::sanitize_hex($input['color_button_text'] ?? $defaults['color_button_text'], $defaults['color_button_text']);
		$output['color_button_text_hover']   = self::sanitize_hex($input['color_button_text_hover'] ?? $defaults['color_button_text_hover'], $defaults['color_button_text_hover']);
		$output['color_footer_bg']           = self::sanitize_hex($input['color_footer_bg'] ?? $defaults['color_footer_bg'], $defaults['color_footer_bg']);
		$output['color_adminbar_hover_bg']   = self::sanitize_hex($input['color_adminbar_hover_bg'] ?? $defaults['color_adminbar_hover_bg'], $defaults['color_adminbar_hover_bg']);

		$output['footer_text'] = sanitize_text_field($input['footer_text'] ?? $defaults['footer_text']);
		$output['footer_url']  = esc_url_raw($input['footer_url'] ?? $defaults['footer_url']);

		$output['role_menu_rules'] = self::sanitize_role_menu_rules($input['role_menu_rules'] ?? []);

		return $output;
	}

	private static function sanitize_hex(string $value, string $fallback): string {
		$sanitized = sanitize_hex_color($value);
		return $sanitized ?: $fallback;
	}

	private static function sanitize_css_size(string $value, string $fallback): string {
		$value = trim(wp_strip_all_tags($value));

		if (preg_match('/^\d+(\.\d+)?(px|rem|em|%)$/', $value)) {
			return $value;
		}

		return $fallback;
	}

	private static function sanitize_css_size_or_keyword(string $value, string $fallback): string {
		$value = trim(wp_strip_all_tags($value));

		if (in_array($value, ['none', 'auto', 'normal'], true)) {
			return $value;
		}

		if (preg_match('/^\d+(\.\d+)?(px|rem|em|%)$/', $value)) {
			return $value;
		}

		return $fallback;
	}

	private static function sanitize_role_menu_rules($value): array {
		if (!is_array($value)) {
			return [];
		}

		if (!function_exists('wp_roles')) {
			return [];
		}

		$roles = wp_roles();

		if (!$roles) {
			return [];
		}

		$allowed_roles = array_keys($roles->roles);
		$sanitized = [];

		foreach ($value as $role => $rules) {
			if (!in_array($role, $allowed_roles, true)) {
				continue;
			}

			if ($role === 'administrator') {
				continue;
			}

			if (!is_array($rules)) {
				continue;
			}

			$role_rules = [
				'hide'          => [],
				'hide_submenus' => [],
			];

			if (!empty($rules['hide']) && is_array($rules['hide'])) {
				foreach ($rules['hide'] as $slug) {
					$slug = trim(wp_strip_all_tags((string) $slug));
					if ($slug !== '') {
						$role_rules['hide'][] = $slug;
					}
				}
			}

			if (!empty($rules['hide_submenus']) && is_array($rules['hide_submenus'])) {
				foreach ($rules['hide_submenus'] as $parent_slug => $sub_slugs) {
					$parent_slug = trim(wp_strip_all_tags((string) $parent_slug));
					if ($parent_slug === '' || !is_array($sub_slugs)) {
						continue;
					}

					$sanitized_subs = [];

					foreach ($sub_slugs as $sub_slug) {
						$sub_slug = trim(wp_strip_all_tags((string) $sub_slug));
						if ($sub_slug !== '') {
							$sanitized_subs[] = $sub_slug;
						}
					}

					if ($sanitized_subs) {
						$role_rules['hide_submenus'][$parent_slug] = $sanitized_subs;
					}
				}
			}

			if ($role_rules['hide'] || $role_rules['hide_submenus']) {
				$sanitized[$role] = $role_rules;
			}
		}

		return $sanitized;
	}
}