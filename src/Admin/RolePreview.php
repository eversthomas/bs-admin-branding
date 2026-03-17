<?php
/**
 * Datei: src/Admin/RolePreview.php
 */

declare(strict_types=1);

namespace BS_Admin_Branding\Admin;

final class RolePreview {

	private const PREVIEW_META_KEY = 'bsab_admin_branding_preview_role';

	public function init(): void {
		add_action('admin_init', [$this, 'handle_preview_actions']);
		add_action('admin_notices', [$this, 'render_preview_notice']);
		add_action('admin_bar_menu', [$this, 'add_admin_bar_item'], 100);
	}

	public static function get_preview_role_for_user(int $user_id): string {
		if ($user_id <= 0) {
			return '';
		}

		$role = get_user_meta($user_id, self::PREVIEW_META_KEY, true);

		return is_string($role) ? $role : '';
	}

	public function handle_preview_actions(): void {
		if (!is_admin() || !current_user_can('manage_options')) {
			return;
		}

		if (!isset($_GET['bsab_preview_action'])) {
			return;
		}

		$action = sanitize_key((string) $_GET['bsab_preview_action']);

		if (!in_array($action, ['start', 'stop'], true)) {
			return;
		}

		if (!isset($_GET['bsab_preview_nonce']) || !wp_verify_nonce((string) $_GET['bsab_preview_nonce'], 'bsab_preview')) {
			return;
		}

		$user_id = get_current_user_id();

		if ($user_id <= 0) {
			return;
		}

		if ($action === 'stop') {
			delete_user_meta($user_id, self::PREVIEW_META_KEY);
		}

		if ($action === 'start') {
			$role = isset($_GET['bsab_preview_role']) ? sanitize_key((string) $_GET['bsab_preview_role']) : '';

			if ($role !== '' && $role !== 'administrator') {
				update_user_meta($user_id, self::PREVIEW_META_KEY, $role);
			}
		}

		$redirect = remove_query_arg(
			[
				'bsab_preview_action',
				'bsab_preview_role',
				'bsab_preview_nonce',
			]
		);

		wp_safe_redirect($redirect);
		exit;
	}

	public function render_preview_notice(): void {
		if (!is_admin() || !current_user_can('manage_options')) {
			return;
		}

		$user_id = get_current_user_id();
		$role = self::get_preview_role_for_user($user_id);

		if ($role === '') {
			return;
		}

		$role_name = $role;
		if (function_exists('wp_roles')) {
			$roles = wp_roles();
			if ($roles && isset($roles->roles[$role]['name'])) {
				$role_name = $roles->roles[$role]['name'];
			}
		}

		$nonce = wp_create_nonce('bsab_preview');
		$exit_url = add_query_arg(
			[
				'bsab_preview_action' => 'stop',
				'bsab_preview_nonce'  => $nonce,
			]
		);
		?>
		<div class="notice notice-info">
			<p>
				<strong><?php echo esc_html__('Rollen-Vorschau aktiv', 'bs-admin-branding'); ?></strong>
				<?php
				printf(
					' %s ',
					esc_html(
						sprintf(
							/* translators: %s: role name */
							__('Du siehst das Backend aktuell aus Sicht der Rolle „%s“.', 'bs-admin-branding'),
							$role_name
						)
					)
				);
				?>
				<a href="<?php echo esc_url($exit_url); ?>" class="button button-secondary">
					<?php echo esc_html__('Vorschau beenden', 'bs-admin-branding'); ?>
				</a>
			</p>
		</div>
		<?php
	}

	public function add_admin_bar_item(\WP_Admin_Bar $wp_admin_bar): void {
		if (!is_admin() || !current_user_can('manage_options')) {
			return;
		}

		$user_id = get_current_user_id();
		$role = self::get_preview_role_for_user($user_id);

		if ($role === '') {
			return;
		}

		$role_name = $role;
		if (function_exists('wp_roles')) {
			$roles = wp_roles();
			if ($roles && isset($roles->roles[$role]['name'])) {
				$role_name = $roles->roles[$role]['name'];
			}
		}

		$nonce = wp_create_nonce('bsab_preview');
		$exit_url = add_query_arg(
			[
				'bsab_preview_action' => 'stop',
				'bsab_preview_nonce'  => $nonce,
			],
			admin_url()
		);

		$wp_admin_bar->add_node(
			[
				'id'    => 'bsab-role-preview',
				'title' => esc_html(
					sprintf(
						/* translators: %s: role name */
						__('Rollen-Vorschau: %s', 'bs-admin-branding'),
						$role_name
					)
				),
				'href'  => $exit_url,
				'meta'  => [
					'class' => 'bsab-role-preview-node',
				],
			]
		);
	}
}

