<?php
/**
 * Datei: src/Admin/FooterBranding.php
 */

declare(strict_types=1);

namespace BS_Admin_Branding\Admin;

use BS_Admin_Branding\Settings\Defaults;

final class FooterBranding {

	public function init(): void {
		add_filter('admin_footer_text', [$this, 'filter_admin_footer_text']);
	}

	public function filter_admin_footer_text(string $footer_text): string {
		$settings = $this->get_settings();

		if (($settings['enable_footer_branding'] ?? '0') !== '1') {
			return $footer_text;
		}

		$text = trim((string) ($settings['footer_text'] ?? ''));
		$url  = trim((string) ($settings['footer_url'] ?? ''));

		if ($text === '') {
			return $footer_text;
		}

		if ($url !== '') {
			return sprintf(
				'<span class="bsab-footer-branding"><a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a></span>',
				esc_url($url),
				esc_html($text)
			);
		}

		return '<span class="bsab-footer-branding">' . esc_html($text) . '</span>';
	}

	private function get_settings(): array {
		$saved = get_option(Defaults::OPTION_KEY, []);

		return Defaults::merge(is_array($saved) ? $saved : []);
	}
}