<?php
/**
 * Datei: src/Admin/Assets.php
 */

declare(strict_types=1);

namespace BS_Admin_Branding\Admin;

use BS_Admin_Branding\Settings\Defaults;

final class Assets {

	public function init(): void {
		add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
		add_action('login_enqueue_scripts', [$this, 'enqueue_login_assets']);
		add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_assets']);

		add_action('admin_head', [$this, 'print_css_variables']);
		add_action('login_head', [$this, 'print_css_variables']);
		add_action('enqueue_block_editor_assets', [$this, 'print_editor_css_variables'], 99);
	}

	public function enqueue_admin_assets(): void {
		$settings = $this->get_settings();

		if (($settings['enable_admin_css'] ?? '0') !== '1') {
			return;
		}

		$css_file = BSAB_PATH . 'assets/css/admin.css';

		if (!file_exists($css_file)) {
			return;
		}

		wp_enqueue_style(
			'bsab-admin',
			BSAB_URL . 'assets/css/admin.css',
			[],
			(string) filemtime($css_file)
		);

		if (($settings['enable_figtree_font'] ?? '1') === '1') {
			$fonts_url = BSAB_URL . 'assets/fonts/';
			$font_face_css = "
@font-face {
	font-family: 'Figtree';
	src: url('{$fonts_url}figtree-v9-latin-regular.woff2') format('woff2');
	font-weight: 400;
	font-style: normal;
	font-display: swap;
}
@font-face {
	font-family: 'Figtree';
	src: url('{$fonts_url}figtree-v9-latin-italic.woff2') format('woff2');
	font-weight: 400;
	font-style: italic;
	font-display: swap;
}
@font-face {
	font-family: 'Figtree';
	src: url('{$fonts_url}figtree-v9-latin-600.woff2') format('woff2');
	font-weight: 600;
	font-style: normal;
	font-display: swap;
}
@font-face {
	font-family: 'Figtree';
	src: url('{$fonts_url}figtree-v9-latin-600italic.woff2') format('woff2');
	font-weight: 600;
	font-style: italic;
	font-display: swap;
}
";
			wp_add_inline_style('bsab-admin', $font_face_css);
		}
	}

	public function enqueue_login_assets(): void {
		$settings = $this->get_settings();

		if (($settings['enable_login_css'] ?? '0') !== '1') {
			return;
		}

		$css_file = BSAB_PATH . 'assets/css/login.css';

		if (!file_exists($css_file)) {
			return;
		}

		wp_enqueue_style(
			'bsab-login',
			BSAB_URL . 'assets/css/login.css',
			[],
			(string) filemtime($css_file)
		);
	}

	public function enqueue_editor_assets(): void {
		$settings = $this->get_settings();

		if (($settings['enable_editor_css'] ?? '0') !== '1') {
			return;
		}

		$css_file = BSAB_PATH . 'assets/css/editor.css';

		if (!file_exists($css_file)) {
			return;
		}

		wp_enqueue_style(
			'bsab-editor',
			BSAB_URL . 'assets/css/editor.css',
			[],
			(string) filemtime($css_file)
		);
	}

	public function print_css_variables(): void {
		$settings = $this->get_settings();

		echo '<style id="bsab-css-vars">';
		echo ':root{';

		$use_figtree = ($settings['enable_figtree_font'] ?? '1') === '1';
		$font_stack_figtree = 'Figtree, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen-Sans, Ubuntu, Cantarell, Helvetica Neue, sans-serif';
		$font_stack_system = '-apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen-Sans, Ubuntu, Cantarell, Helvetica Neue, sans-serif';
		$font_stack = $use_figtree ? $font_stack_figtree : $font_stack_system;

		/* === Primäre BSAB-Variablen === */
		echo '--bsab-font-family:' . $font_stack . ';';
		echo '--bsab-sidebar-width:' . esc_attr($settings['sidebar_width']) . ';';
		echo '--bsab-content-max-width:' . esc_attr($settings['content_max_width']) . ';';
		echo '--bsab-border-radius:' . esc_attr($settings['border_radius']) . ';';

		echo '--bsab-sidebar-bg:' . esc_attr($settings['color_sidebar_bg']) . ';';
		echo '--bsab-sidebar-submenu-bg:' . esc_attr($settings['color_sidebar_submenu_bg']) . ';';
		echo '--bsab-sidebar-submenu-hover-bg:' . esc_attr($settings['color_sidebar_submenu_hover_bg']) . ';';
		echo '--bsab-sidebar-text:' . esc_attr($settings['color_sidebar_text']) . ';';
		echo '--bsab-sidebar-hover:' . esc_attr($settings['color_sidebar_hover']) . ';';
		echo '--bsab-sidebar-hover-text:' . esc_attr($settings['color_sidebar_hover_text']) . ';';
		echo '--bsab-sidebar-active:' . esc_attr($settings['color_sidebar_active']) . ';';
		echo '--bsab-sidebar-active-text:' . esc_attr($settings['color_sidebar_active_text']) . ';';

		echo '--bsab-adminbar-bg:' . esc_attr($settings['color_adminbar_bg']) . ';';
		echo '--bsab-adminbar-text:' . esc_attr($settings['color_adminbar_text']) . ';';

		echo '--bsab-content-bg:' . esc_attr($settings['color_content_bg']) . ';';
		echo '--bsab-card-bg:' . esc_attr($settings['color_card_bg']) . ';';
		echo '--bsab-card-text:' . esc_attr($settings['color_card_text']) . ';';

		echo '--bsab-accent:' . esc_attr($settings['color_accent']) . ';';
		echo '--bsab-accent-hover:' . esc_attr($settings['color_accent_hover']) . ';';

		// Zusätzliche BSAB-Variablen für Rahmen, Tabellen, Texte und Footer
		echo '--bsab-border:' . esc_attr($settings['color_border']) . ';';
		echo '--bsab-table-header-bg:' . esc_attr($settings['color_table_header_bg']) . ';';
		echo '--bsab-table-row-hover:' . esc_attr($settings['color_table_row_hover']) . ';';
		echo '--bsab-text-heading:' . esc_attr($settings['color_text_heading']) . ';';
		echo '--bsab-text-input:' . esc_attr($settings['color_text_input']) . ';';
		echo '--bsab-button-border-hover:' . esc_attr($settings['color_button_border_hover']) . ';';
		echo '--bsab-button-border:' . esc_attr($settings['color_button_border']) . ';';
		echo '--bsab-button-bg:' . esc_attr($settings['color_button_bg']) . ';';
		echo '--bsab-button-hover-bg:' . esc_attr($settings['color_button_hover_bg']) . ';';
		echo '--bsab-button-text:' . esc_attr($settings['color_button_text']) . ';';
		echo '--bsab-button-text-hover:' . esc_attr($settings['color_button_text_hover']) . ';';
		echo '--bsab-footer-bg:' . esc_attr($settings['color_footer_bg']) . ';';
		echo '--bsab-adminbar-hover-bg:' . esc_attr($settings['color_adminbar_hover_bg']) . ';';

		/* === Kompatibilitäts-Aliases === */
		echo '--font-family:' . $font_stack . ';';
		echo '--custom-font:' . $font_stack . ';';
		echo '--sidebar-width:' . esc_attr($settings['sidebar_width']) . ';';
		echo '--content-max-width:' . esc_attr($settings['content_max_width']) . ';';
		echo '--border-radius:' . esc_attr($settings['border_radius']) . ';';

		echo '--sidebar-bg:' . esc_attr($settings['color_sidebar_bg']) . ';';
		echo '--sidebar-submenu-bg:' . esc_attr($settings['color_sidebar_submenu_bg']) . ';';
		echo '--sidebar-bg-sub:' . esc_attr($settings['color_sidebar_submenu_bg']) . ';';
		echo '--sidebar-submenu-hover-bg:' . esc_attr($settings['color_sidebar_submenu_hover_bg']) . ';';
		echo '--sidebar-text:' . esc_attr($settings['color_sidebar_text']) . ';';
		echo '--sidebar-text-sub:' . esc_attr($settings['color_sidebar_text']) . ';'; // Alias
		echo '--sidebar-hover:' . esc_attr($settings['color_sidebar_hover']) . ';';
		echo '--sidebar-hover-bg:' . esc_attr($settings['color_sidebar_hover']) . ';';
		echo '--sidebar-hover-text:' . esc_attr($settings['color_sidebar_hover_text']) . ';';
		echo '--sidebar-active:' . esc_attr($settings['color_sidebar_active']) . ';';
		echo '--sidebar-active-bg:' . esc_attr($settings['color_sidebar_active']) . ';';
		echo '--sidebar-active-text:' . esc_attr($settings['color_sidebar_active_text']) . ';';
		// Kompatibilitäts-Aliases
		echo '--sidebar-border:' . esc_attr($settings['color_border']) . ';';

		echo '--adminbar-bg:' . esc_attr($settings['color_adminbar_bg']) . ';';
		echo '--adminbar-text:' . esc_attr($settings['color_adminbar_text']) . ';';
		echo '--adminbar-text-sub:' . esc_attr($settings['color_adminbar_text']) . ';'; // Alias
		echo '--adminbar-border:' . esc_attr($settings['color_border']) . ';';
		echo '--adminbar-hover-bg:' . esc_attr($settings['color_adminbar_hover_bg']) . ';';

		echo '--body-bg:' . esc_attr($settings['color_content_bg']) . ';';
		echo '--content-bg:' . esc_attr($settings['color_content_bg']) . ';';
		echo '--card-bg:' . esc_attr($settings['color_card_bg']) . ';';
		echo '--card-text:' . esc_attr($settings['color_card_text']) . ';';

		echo '--accent:' . esc_attr($settings['color_accent']) . ';';
		echo '--accent-hover:' . esc_attr($settings['color_accent_hover']) . ';';

		echo '}';
		echo '</style>';
	}

	public function print_editor_css_variables(): void {
		$settings = $this->get_settings();

		if (($settings['enable_editor_css'] ?? '0') !== '1') {
			return;
		}

		if (!wp_style_is('bsab-editor', 'enqueued')) {
			return;
		}

		$use_figtree = ($settings['enable_figtree_font'] ?? '1') === '1';
		$font_stack_figtree = 'Figtree, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen-Sans, Ubuntu, Cantarell, Helvetica Neue, sans-serif';
		$font_stack_system = '-apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen-Sans, Ubuntu, Cantarell, Helvetica Neue, sans-serif';
		$font_stack = $use_figtree ? $font_stack_figtree : $font_stack_system;

		// Hinweis: Siehe admin.css für die Liste der verwendeten Variablen.
		$css = ':root{'
			. '--bsab-font-family:' . $font_stack . ';'
			. '--bsab-sidebar-width:' . esc_attr($settings['sidebar_width']) . ';'
			. '--bsab-content-max-width:' . esc_attr($settings['content_max_width']) . ';'
			. '--bsab-border-radius:' . esc_attr($settings['border_radius']) . ';'
			. '--bsab-sidebar-bg:' . esc_attr($settings['color_sidebar_bg']) . ';'
			. '--bsab-sidebar-submenu-bg:' . esc_attr($settings['color_sidebar_submenu_bg']) . ';'
			. '--bsab-sidebar-submenu-hover-bg:' . esc_attr($settings['color_sidebar_submenu_hover_bg']) . ';'
			. '--bsab-sidebar-text:' . esc_attr($settings['color_sidebar_text']) . ';'
			. '--bsab-sidebar-hover:' . esc_attr($settings['color_sidebar_hover']) . ';'
			. '--bsab-sidebar-hover-text:' . esc_attr($settings['color_sidebar_hover_text']) . ';'
			. '--bsab-sidebar-active:' . esc_attr($settings['color_sidebar_active']) . ';'
			. '--bsab-sidebar-active-text:' . esc_attr($settings['color_sidebar_active_text']) . ';'
			. '--bsab-adminbar-bg:' . esc_attr($settings['color_adminbar_bg']) . ';'
			. '--bsab-adminbar-text:' . esc_attr($settings['color_adminbar_text']) . ';'
			. '--bsab-content-bg:' . esc_attr($settings['color_content_bg']) . ';'
			. '--bsab-card-bg:' . esc_attr($settings['color_card_bg']) . ';'
			. '--bsab-card-text:' . esc_attr($settings['color_card_text']) . ';'
			. '--bsab-accent:' . esc_attr($settings['color_accent']) . ';'
			. '--bsab-accent-hover:' . esc_attr($settings['color_accent_hover']) . ';'
			. '--bsab-border:' . esc_attr($settings['color_border']) . ';'
			. '--bsab-table-header-bg:' . esc_attr($settings['color_table_header_bg']) . ';'
			. '--bsab-table-row-hover:' . esc_attr($settings['color_table_row_hover']) . ';'
			. '--bsab-text-heading:' . esc_attr($settings['color_text_heading']) . ';'
			. '--bsab-text-input:' . esc_attr($settings['color_text_input']) . ';'
			. '--bsab-button-border-hover:' . esc_attr($settings['color_button_border_hover']) . ';'
			. '--bsab-button-border:' . esc_attr($settings['color_button_border']) . ';'
			. '--bsab-button-bg:' . esc_attr($settings['color_button_bg']) . ';'
			. '--bsab-button-hover-bg:' . esc_attr($settings['color_button_hover_bg']) . ';'
			. '--bsab-button-text:' . esc_attr($settings['color_button_text']) . ';'
			. '--bsab-button-text-hover:' . esc_attr($settings['color_button_text_hover']) . ';'
			. '--bsab-footer-bg:' . esc_attr($settings['color_footer_bg']) . ';'
			. '--bsab-adminbar-hover-bg:' . esc_attr($settings['color_adminbar_hover_bg']) . ';'

			. '--font-family:' . $font_stack . ';'
			. '--custom-font:' . $font_stack . ';'
			. '--sidebar-width:' . esc_attr($settings['sidebar_width']) . ';'
			. '--content-max-width:' . esc_attr($settings['content_max_width']) . ';'
			. '--border-radius:' . esc_attr($settings['border_radius']) . ';'
			. '--sidebar-bg:' . esc_attr($settings['color_sidebar_bg']) . ';'
			. '--sidebar-submenu-bg:' . esc_attr($settings['color_sidebar_submenu_bg']) . ';'
			. '--sidebar-bg-sub:' . esc_attr($settings['color_sidebar_submenu_bg']) . ';'
			. '--sidebar-submenu-hover-bg:' . esc_attr($settings['color_sidebar_submenu_hover_bg']) . ';'
			. '--sidebar-text:' . esc_attr($settings['color_sidebar_text']) . ';'
			. '--sidebar-text-sub:' . esc_attr($settings['color_sidebar_text']) . ';' // Alias
			. '--sidebar-hover:' . esc_attr($settings['color_sidebar_hover']) . ';'
			. '--sidebar-hover-bg:' . esc_attr($settings['color_sidebar_hover']) . ';'
			. '--sidebar-hover-text:' . esc_attr($settings['color_sidebar_hover_text']) . ';'
			. '--sidebar-active:' . esc_attr($settings['color_sidebar_active']) . ';'
			. '--sidebar-active-bg:' . esc_attr($settings['color_sidebar_active']) . ';'
			. '--sidebar-active-text:' . esc_attr($settings['color_sidebar_active_text']) . ';'
			. '--sidebar-border:' . esc_attr($settings['color_border']) . ';'
			. '--adminbar-bg:' . esc_attr($settings['color_adminbar_bg']) . ';'
			. '--adminbar-text:' . esc_attr($settings['color_adminbar_text']) . ';'
			. '--adminbar-text-sub:' . esc_attr($settings['color_adminbar_text']) . ';'
			. '--adminbar-border:' . esc_attr($settings['color_border']) . ';'
			. '--adminbar-hover-bg:' . esc_attr($settings['color_adminbar_hover_bg']) . ';'
			. '--body-bg:' . esc_attr($settings['color_content_bg']) . ';'
			. '--content-bg:' . esc_attr($settings['color_content_bg']) . ';'
			. '--card-bg:' . esc_attr($settings['color_card_bg']) . ';'
			. '--card-text:' . esc_attr($settings['color_card_text']) . ';'
			. '--accent:' . esc_attr($settings['color_accent']) . ';'
			. '--accent-hover:' . esc_attr($settings['color_accent_hover']) . ';'
			. '}';

		wp_add_inline_style('bsab-editor', $css);
	}

	private function get_settings(): array {
		$saved = get_option(Defaults::OPTION_KEY, []);

		return Defaults::merge(is_array($saved) ? $saved : []);
	}
}