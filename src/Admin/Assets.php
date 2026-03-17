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

		/* Plugin-Variablen */
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

		echo '--bsab-adminbar-bg:' . esc_attr($settings['color_adminbar_bg']) . ';';
		echo '--bsab-adminbar-text:' . esc_attr($settings['color_adminbar_text']) . ';';

		echo '--bsab-content-bg:' . esc_attr($settings['color_content_bg']) . ';';
		echo '--bsab-card-bg:' . esc_attr($settings['color_card_bg']) . ';';
		echo '--bsab-card-text:' . esc_attr($settings['color_card_text']) . ';';

		echo '--bsab-accent:' . esc_attr($settings['color_accent']) . ';';
		echo '--bsab-accent-hover:' . esc_attr($settings['color_accent_hover']) . ';';

		/* Kompatibilitäts-Variablen für bestehendes CSS */
		echo '--font-family:' . $font_stack . ';';
		echo '--custom-font:' . $font_stack . ';';
		echo '--sidebar-width:' . esc_attr($settings['sidebar_width']) . ';';
		echo '--content-max-width:' . esc_attr($settings['content_max_width']) . ';';
		echo '--border-radius:' . esc_attr($settings['border_radius']) . ';';

		echo '--sidebar-bg:' . esc_attr($settings['color_sidebar_bg']) . ';';
		echo '--sidebar-submenu-bg:' . esc_attr($settings['color_sidebar_submenu_bg']) . ';';
		echo '--sidebar-submenu-hover-bg:' . esc_attr($settings['color_sidebar_submenu_hover_bg']) . ';';
		echo '--sidebar-text:' . esc_attr($settings['color_sidebar_text']) . ';';
		echo '--sidebar-hover:' . esc_attr($settings['color_sidebar_hover']) . ';';
		echo '--sidebar-hover-text:' . esc_attr($settings['color_sidebar_hover_text']) . ';';
		echo '--sidebar-active:' . esc_attr($settings['color_sidebar_active']) . ';';

		echo '--adminbar-bg:' . esc_attr($settings['color_adminbar_bg']) . ';';
		echo '--adminbar-text:' . esc_attr($settings['color_adminbar_text']) . ';';

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
			. '--bsab-adminbar-bg:' . esc_attr($settings['color_adminbar_bg']) . ';'
			. '--bsab-adminbar-text:' . esc_attr($settings['color_adminbar_text']) . ';'
			. '--bsab-content-bg:' . esc_attr($settings['color_content_bg']) . ';'
			. '--bsab-card-bg:' . esc_attr($settings['color_card_bg']) . ';'
			. '--bsab-card-text:' . esc_attr($settings['color_card_text']) . ';'
			. '--bsab-accent:' . esc_attr($settings['color_accent']) . ';'
			. '--bsab-accent-hover:' . esc_attr($settings['color_accent_hover']) . ';'

			. '--font-family:' . $font_stack . ';'
			. '--custom-font:' . $font_stack . ';'
			. '--sidebar-width:' . esc_attr($settings['sidebar_width']) . ';'
			. '--content-max-width:' . esc_attr($settings['content_max_width']) . ';'
			. '--border-radius:' . esc_attr($settings['border_radius']) . ';'
			. '--sidebar-bg:' . esc_attr($settings['color_sidebar_bg']) . ';'
			. '--sidebar-submenu-bg:' . esc_attr($settings['color_sidebar_submenu_bg']) . ';'
			. '--sidebar-submenu-hover-bg:' . esc_attr($settings['color_sidebar_submenu_hover_bg']) . ';'
			. '--sidebar-text:' . esc_attr($settings['color_sidebar_text']) . ';'
			. '--sidebar-hover:' . esc_attr($settings['color_sidebar_hover']) . ';'
			. '--sidebar-hover-text:' . esc_attr($settings['color_sidebar_hover_text']) . ';'
			. '--sidebar-active:' . esc_attr($settings['color_sidebar_active']) . ';'
			. '--adminbar-bg:' . esc_attr($settings['color_adminbar_bg']) . ';'
			. '--adminbar-text:' . esc_attr($settings['color_adminbar_text']) . ';'
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