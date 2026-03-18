<?php
/**
 * Datei: src/Admin/SchemaExport.php
 */

declare(strict_types=1);

namespace BS_Admin_Branding\Admin;

use BS_Admin_Branding\Settings\Defaults;
use BS_Admin_Branding\Settings\Sanitizer;

final class SchemaExport {
	private const LOG_PREFIX = '[BSAB SchemaExport] ';

	public function init(): void {
		add_action('admin_post_bsab_export', [$this, 'handle_export']);
		add_action('admin_post_bsab_import', [$this, 'handle_import']);
	}

	public function handle_export(): void {
		error_log(self::LOG_PREFIX . 'handle_export start');
		if (!current_user_can('manage_options')) {
			error_log(self::LOG_PREFIX . 'handle_export forbidden');
			wp_die(esc_html__('Nicht erlaubt.', 'bs-admin-branding'));
		}

		if (
			!isset($_POST['bsab_export_nonce'])
			|| !is_string($_POST['bsab_export_nonce'])
			|| !wp_verify_nonce($_POST['bsab_export_nonce'], 'bsab_export_nonce')
		) {
			error_log(self::LOG_PREFIX . 'handle_export nonce invalid');
			wp_die(esc_html__('Ungültige Anfrage (Nonce).', 'bs-admin-branding'), 400);
		}

		$settings = get_option(Defaults::OPTION_KEY, []);
		$settings = is_array($settings) ? $settings : [];

		$export = [
			'_bsab_export_version' => '1',
			'_bsab_export_date'    => current_time('Y-m-d'),
			'settings'             => $settings,
		];

		header('Content-Type: application/json; charset=utf-8');
		header('Content-Disposition: attachment; filename="bsab-schema-' . current_time('Y-m-d') . '.json"');
		header('Cache-Control: no-cache');

		echo wp_json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		error_log(self::LOG_PREFIX . 'handle_export success');
		exit;
	}

	public function handle_import(): void {
		error_log(self::LOG_PREFIX . 'handle_import start');
		if (!current_user_can('manage_options')) {
			error_log(self::LOG_PREFIX . 'handle_import forbidden');
			wp_die(esc_html__('Nicht erlaubt.', 'bs-admin-branding'));
		}

		if (
			!isset($_POST['bsab_import_nonce'])
			|| !is_string($_POST['bsab_import_nonce'])
			|| !wp_verify_nonce($_POST['bsab_import_nonce'], 'bsab_import_nonce')
		) {
			error_log(self::LOG_PREFIX . 'handle_import nonce invalid');
			wp_die(esc_html__('Ungültige Anfrage (Nonce).', 'bs-admin-branding'), 400);
		}

		$success = false;

		if (
			isset($_FILES['bsab_import_file'])
			&& is_array($_FILES['bsab_import_file'])
			&& isset($_FILES['bsab_import_file']['error'], $_FILES['bsab_import_file']['tmp_name'])
			&& (int) $_FILES['bsab_import_file']['error'] === UPLOAD_ERR_OK
			&& is_string($_FILES['bsab_import_file']['tmp_name'])
			&& $_FILES['bsab_import_file']['tmp_name'] !== ''
		) {
			$content = file_get_contents($_FILES['bsab_import_file']['tmp_name']);
			if (is_string($content) && $content !== '') {
				$data = json_decode($content, true);
				if (is_array($data) && isset($data['settings']) && is_array($data['settings'])) {
					$sanitized = Sanitizer::sanitize($data['settings']);
					update_option(Defaults::OPTION_KEY, $sanitized);
					$success = true;
				} else {
					error_log(self::LOG_PREFIX . 'handle_import invalid json structure');
				}
			} else {
				error_log(self::LOG_PREFIX . 'handle_import empty file content');
			}
		} else {
			error_log(self::LOG_PREFIX . 'handle_import missing/invalid upload');
		}

		wp_redirect(
			add_query_arg(
				[
					'page'        => 'bs-admin-branding',
					'bsab_tab'    => 'general',
					'bsab_import' => $success ? 'success' : 'error',
				],
				admin_url('admin.php')
			)
		);
		error_log(self::LOG_PREFIX . 'handle_import redirect ' . ($success ? 'success' : 'error'));
		exit;
	}
}

