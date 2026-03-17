<?php
/**
 * Datei: src/Settings/Defaults.php
 */

declare(strict_types=1);

namespace BS_Admin_Branding\Settings;

final class Defaults {

	public const OPTION_KEY = 'bsab_settings';

	public static function get(): array {
		return [
			'enable_admin_css'         => '1',
			'enable_login_css'         => '0',
			'enable_editor_css'        => '0',
			'enable_figtree_font'      => '1',
			'enable_footer_branding'   => '1',

			'sidebar_width'            => '300px',
			'content_max_width'        => 'none',
			'border_radius'            => '10px',

			'color_sidebar_bg'         => '#f7f7f8',
			'color_sidebar_submenu_bg' => '#eceef1',
			'color_sidebar_submenu_hover_bg' => '#e9e9eb',
			'color_sidebar_text'       => '#1d2327',
			'color_sidebar_hover'      => '#2563eb',
			'color_sidebar_hover_text' => '#111827',
			'color_sidebar_active'     => '#1d4ed8',

			'color_adminbar_bg'        => '#ffffff',
			'color_adminbar_text'      => '#1d2327',

			'color_content_bg'         => '#f3f4f6',
			'color_card_bg'            => '#ffffff',
			'color_card_text'          => '#1d2327',

			'color_accent'             => '#2563eb',
			'color_accent_hover'       => '#1d4ed8',

			'footer_text'              => 'Gestaltet mit BS Admin Branding',
			'footer_url'               => 'https://bezugssysteme.de',

			'role_menu_rules'          => [],
		];
	}

	public static function merge(array $settings): array {
		return wp_parse_args($settings, self::get());
	}
}