<?php
/**
 * Datei: src/Admin/SettingsPage.php
 */

declare(strict_types=1);

namespace BS_Admin_Branding\Admin;

use BS_Admin_Branding\Settings\Defaults;
use BS_Admin_Branding\Settings\Sanitizer;

final class SettingsPage {

	public function init(): void {
		add_action('admin_menu', [$this, 'register_menu']);
		add_action('admin_init', [$this, 'register_settings']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_settings_assets']);
	}

	public function register_menu(): void {
		add_menu_page(
			__('BS Admin Branding', 'bs-admin-branding'),
			__('Admin Branding', 'bs-admin-branding'),
			'manage_options',
			'bs-admin-branding',
			[$this, 'render_page'],
			'dashicons-art',
			81
		);
	}

	public function register_settings(): void {
		register_setting(
			'bsab_settings_group',
			Defaults::OPTION_KEY,
			[
				'type'              => 'array',
				'sanitize_callback' => [Sanitizer::class, 'sanitize'],
				'default'           => Defaults::get(),
			]
		);

		add_settings_section(
			'bsab_section_general',
			__('Allgemein', 'bs-admin-branding'),
			'__return_false',
			'bs-admin-branding'
		);

		add_settings_section(
			'bsab_section_layout',
			__('Layout', 'bs-admin-branding'),
			'__return_false',
			'bs-admin-branding'
		);

		add_settings_section(
			'bsab_section_colors',
			__('Farben', 'bs-admin-branding'),
			'__return_false',
			'bs-admin-branding'
		);

		add_settings_section(
			'bsab_section_branding',
			__('Branding', 'bs-admin-branding'),
			'__return_false',
			'bs-admin-branding'
		);

		$this->add_checkbox_field('enable_admin_css', __('Admin-CSS aktivieren', 'bs-admin-branding'), 'bsab_section_general');
		$this->add_checkbox_field('enable_login_css', __('Login-CSS aktivieren', 'bs-admin-branding'), 'bsab_section_general');
		$this->add_checkbox_field('enable_editor_css', __('Editor-CSS aktivieren', 'bs-admin-branding'), 'bsab_section_general');
		$this->add_checkbox_field('enable_figtree_font', __('Figtree-Schrift verwenden', 'bs-admin-branding'), 'bsab_section_general');
		$this->add_checkbox_field('enable_footer_branding', __('Footer-Branding aktivieren', 'bs-admin-branding'), 'bsab_section_general');

		$this->add_text_field('sidebar_width', __('Sidebar-Breite', 'bs-admin-branding'), 'bsab_section_layout');
		$this->add_text_field('content_max_width', __('Maximale Content-Breite', 'bs-admin-branding'), 'bsab_section_layout');
		$this->add_text_field('border_radius', __('Border-Radius', 'bs-admin-branding'), 'bsab_section_layout');

		$this->add_color_field('color_sidebar_bg', __('Sidebar Hintergrund', 'bs-admin-branding'), 'bsab_section_colors');
		$this->add_color_field('color_sidebar_submenu_bg', __('Sidebar Untermenü Hintergrund', 'bs-admin-branding'), 'bsab_section_colors');
		$this->add_color_field('color_sidebar_submenu_hover_bg', __('Sidebar Untermenü Hover', 'bs-admin-branding'), 'bsab_section_colors');
		$this->add_color_field('color_sidebar_text', __('Sidebar Text', 'bs-admin-branding'), 'bsab_section_colors');
		$this->add_color_field('color_sidebar_hover', __('Sidebar Hover', 'bs-admin-branding'), 'bsab_section_colors');
		$this->add_color_field('color_sidebar_hover_text', __('Sidebar Hover Text', 'bs-admin-branding'), 'bsab_section_colors');
		$this->add_color_field('color_sidebar_active', __('Sidebar Aktiv', 'bs-admin-branding'), 'bsab_section_colors');
		$this->add_color_field('color_adminbar_bg', __('Adminbar Hintergrund', 'bs-admin-branding'), 'bsab_section_colors');
		$this->add_color_field('color_adminbar_text', __('Adminbar Text', 'bs-admin-branding'), 'bsab_section_colors');
		$this->add_color_field('color_content_bg', __('Seitenhintergrund', 'bs-admin-branding'), 'bsab_section_layout');
		$this->add_color_field('color_card_bg', __('Card Hintergrund', 'bs-admin-branding'), 'bsab_section_layout');
		$this->add_color_field('color_card_text', __('Card Text', 'bs-admin-branding'), 'bsab_section_layout');
		$this->add_color_field('color_accent', __('Akzentfarbe', 'bs-admin-branding'), 'bsab_section_colors');
		$this->add_color_field('color_accent_hover', __('Akzent Hover', 'bs-admin-branding'), 'bsab_section_colors');

		$this->add_text_field('footer_text', __('Footer-Text', 'bs-admin-branding'), 'bsab_section_branding');
		$this->add_text_field('footer_url', __('Footer-URL', 'bs-admin-branding'), 'bsab_section_branding');
	}

	public function enqueue_settings_assets(string $hook_suffix): void {
		if ($hook_suffix !== 'toplevel_page_bs-admin-branding') {
			return;
		}

		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker');

		$css_file = BSAB_PATH . 'assets/css/settings.css';

		if (file_exists($css_file)) {
			wp_enqueue_style(
				'bsab-settings',
				BSAB_URL . 'assets/css/settings.css',
				['wp-color-picker'],
				(string) filemtime($css_file)
			);
		}

		$js_file = BSAB_PATH . 'assets/js/settings.js';

		if (file_exists($js_file)) {
			wp_enqueue_script(
				'bsab-settings',
				BSAB_URL . 'assets/js/settings.js',
				['wp-color-picker', 'jquery'],
				(string) filemtime($js_file),
				true
			);
		}
	}

	public function render_page(): void {
		if (!current_user_can('manage_options')) {
			return;
		}

		$settings = $this->get_settings();
		$roles    = function_exists('wp_roles') ? wp_roles() : null;
		$all_roles = $roles ? $roles->roles : [];
		$selected_role = isset($_GET['bsab_role']) ? sanitize_key((string) $_GET['bsab_role']) : '';
		$active_tab = isset($_GET['bsab_tab']) ? sanitize_key((string) $_GET['bsab_tab']) : 'general';
		$is_admin = current_user_can('manage_options');

		$allowed_tabs = ['general', 'sidebar', 'content', 'branding', 'roles'];
		if (!in_array($active_tab, $allowed_tabs, true)) {
			$active_tab = 'general';
		}

		if ($selected_role === '' && is_array($all_roles)) {
			foreach (array_keys($all_roles) as $role_key) {
				if ($role_key === 'administrator') {
					continue;
				}
				$selected_role = $role_key;
				break;
			}
		}

		if ($selected_role === 'administrator') {
			$selected_role = '';
		}

		$role_menu_rules = $settings['role_menu_rules'] ?? [];
		?>
		<div class="wrap bsab-settings-page">
			<h1><?php echo esc_html__('BS Admin Branding', 'bs-admin-branding'); ?></h1>

			<?php settings_errors('bsab_settings_group'); ?>

			<h2 class="nav-tab-wrapper bsab-tabs" role="tablist">
				<a href="#bsab-tab-general" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>" data-bsab-tab="general" role="tab" aria-selected="<?php echo $active_tab === 'general' ? 'true' : 'false'; ?>" aria-controls="bsab-tab-panel-general" id="bsab-tab-general">
					<?php echo esc_html__('Allgemein', 'bs-admin-branding'); ?>
				</a>
				<a href="#bsab-tab-sidebar" class="nav-tab <?php echo $active_tab === 'sidebar' ? 'nav-tab-active' : ''; ?>" data-bsab-tab="sidebar" role="tab" aria-selected="<?php echo $active_tab === 'sidebar' ? 'true' : 'false'; ?>" aria-controls="bsab-tab-panel-sidebar" id="bsab-tab-sidebar">
					<?php echo esc_html__('Sidebar', 'bs-admin-branding'); ?>
				</a>
				<a href="#bsab-tab-content" class="nav-tab <?php echo $active_tab === 'content' ? 'nav-tab-active' : ''; ?>" data-bsab-tab="content" role="tab" aria-selected="<?php echo $active_tab === 'content' ? 'true' : 'false'; ?>" aria-controls="bsab-tab-panel-content" id="bsab-tab-content">
					<?php echo esc_html__('Content', 'bs-admin-branding'); ?>
				</a>
				<a href="#bsab-tab-branding" class="nav-tab <?php echo $active_tab === 'branding' ? 'nav-tab-active' : ''; ?>" data-bsab-tab="branding" role="tab" aria-selected="<?php echo $active_tab === 'branding' ? 'true' : 'false'; ?>" aria-controls="bsab-tab-panel-branding" id="bsab-tab-branding">
					<?php echo esc_html__('Branding', 'bs-admin-branding'); ?>
				</a>
				<a href="#bsab-tab-roles" class="nav-tab <?php echo $active_tab === 'roles' ? 'nav-tab-active' : ''; ?>" data-bsab-tab="roles" role="tab" aria-selected="<?php echo $active_tab === 'roles' ? 'true' : 'false'; ?>" aria-controls="bsab-tab-panel-roles" id="bsab-tab-roles">
					<?php echo esc_html__('Rollen & Menüs', 'bs-admin-branding'); ?>
				</a>
			</h2>

			<form method="post" action="options.php">
				<?php settings_fields('bsab_settings_group'); ?>

				<?php submit_button(__('Änderungen speichern', 'bs-admin-branding'), 'primary', 'submit', false, ['class' => 'bsab-submit-top']); ?>

				<div class="bsab-tab-panels">
					<div class="bsab-tab-panel <?php echo $active_tab === 'general' ? 'is-active' : ''; ?>" data-bsab-tab-panel="general" role="tabpanel" aria-labelledby="bsab-tab-general" id="bsab-tab-panel-general" aria-hidden="<?php echo $active_tab === 'general' ? 'false' : 'true'; ?>">
						<div class="bsab-settings-layout">
							<div class="bsab-settings-column">
								<div class="bsab-settings-card">
									<h2><?php echo esc_html__('Allgemein', 'bs-admin-branding'); ?></h2>
									<table class="form-table" role="presentation">
										<tbody>
										<?php do_settings_fields('bs-admin-branding', 'bsab_section_general'); ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>

					<div class="bsab-tab-panel <?php echo $active_tab === 'sidebar' ? 'is-active' : ''; ?>" data-bsab-tab-panel="sidebar" role="tabpanel" aria-labelledby="bsab-tab-sidebar" id="bsab-tab-panel-sidebar" aria-hidden="<?php echo $active_tab === 'sidebar' ? 'false' : 'true'; ?>">
						<div class="bsab-settings-layout">
							<div class="bsab-settings-column">
								<div class="bsab-settings-card">
									<h2><?php echo esc_html__('Sidebar & Menü', 'bs-admin-branding'); ?></h2>
									<table class="form-table" role="presentation">
										<tbody>
										<?php
										do_settings_fields('bs-admin-branding', 'bsab_section_colors');
										?>
										</tbody>
									</table>
								</div>
							</div>

							<div class="bsab-settings-column">
								<div class="bsab-settings-card">
									<h2><?php echo esc_html__('Hinweis', 'bs-admin-branding'); ?></h2>
									<p><?php echo esc_html__('Weitere spezifische Sidebar-Optionen können hier zukünftig ergänzt werden.', 'bs-admin-branding'); ?></p>
								</div>
							</div>
						</div>
					</div>

					<div class="bsab-tab-panel <?php echo $active_tab === 'content' ? 'is-active' : ''; ?>" data-bsab-tab-panel="content" role="tabpanel" aria-labelledby="bsab-tab-content" id="bsab-tab-panel-content" aria-hidden="<?php echo $active_tab === 'content' ? 'false' : 'true'; ?>">
						<div class="bsab-settings-layout">
							<div class="bsab-settings-column">
								<div class="bsab-settings-card">
									<h2><?php echo esc_html__('Content & Layout', 'bs-admin-branding'); ?></h2>
									<table class="form-table" role="presentation">
										<tbody>
										<?php do_settings_fields('bs-admin-branding', 'bsab_section_layout'); ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>

					<div class="bsab-tab-panel <?php echo $active_tab === 'branding' ? 'is-active' : ''; ?>" data-bsab-tab-panel="branding" role="tabpanel" aria-labelledby="bsab-tab-branding" id="bsab-tab-panel-branding" aria-hidden="<?php echo $active_tab === 'branding' ? 'false' : 'true'; ?>">
						<div class="bsab-settings-layout">
							<div class="bsab-settings-column">
								<div class="bsab-settings-card">
									<h2><?php echo esc_html__('Branding', 'bs-admin-branding'); ?></h2>
									<table class="form-table" role="presentation">
										<tbody>
										<?php do_settings_fields('bs-admin-branding', 'bsab_section_branding'); ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>

					<div class="bsab-tab-panel <?php echo $active_tab === 'roles' ? 'is-active' : ''; ?>" data-bsab-tab-panel="roles" role="tabpanel" aria-labelledby="bsab-tab-roles" id="bsab-tab-panel-roles" aria-hidden="<?php echo $active_tab === 'roles' ? 'false' : 'true'; ?>">
						<div class="bsab-settings-layout">
							<div class="bsab-settings-column">
								<div class="bsab-settings-card">
									<h2><?php echo esc_html__('Rollen-Auswahl', 'bs-admin-branding'); ?></h2>
									<?php if ($all_roles) : ?>
										<?php
										$role_labels = [
											'administrator' => __('Administrator', 'bs-admin-branding'),
											'editor'        => __('Redakteur', 'bs-admin-branding'),
											'author'        => __('Autor', 'bs-admin-branding'),
											'contributor'   => __('Mitarbeiter', 'bs-admin-branding'),
											'subscriber'    => __('Abonnent', 'bs-admin-branding'),
										];
										?>
										<table class="form-table" role="presentation">
											<tbody>
											<tr>
												<th scope="row"><?php echo esc_html__('Rolle', 'bs-admin-branding'); ?></th>
												<td>
													<select name="bsab_role" class="bsab-role-select">
														<?php foreach ($all_roles as $role_key => $role_data) : ?>
															<?php
															$label = $role_labels[$role_key] ?? $role_data['name'];
															if ($role_key === 'administrator') :
																?>
																<option value="<?php echo esc_attr($role_key); ?>" disabled>
																	<?php echo esc_html($label . ' (' . __('immer alle Menüs', 'bs-admin-branding') . ')'); ?>
																</option>
																<?php continue; ?>
															<?php endif; ?>
															<option value="<?php echo esc_attr($role_key); ?>" <?php selected($selected_role, $role_key); ?>>
																<?php echo esc_html($label); ?>
															</option>
														<?php endforeach; ?>
													</select>
													<p class="description">
														<?php echo esc_html__('Wähle eine Rolle, um die sichtbaren Menüpunkte zu steuern.', 'bs-admin-branding'); ?>
													</p>
													<?php if ($is_admin && $selected_role !== '') : ?>
														<?php
														$nonce = wp_create_nonce('bsab_preview');
														$preview_url = add_query_arg(
															[
																'page'               => 'bs-admin-branding',
																'bsab_tab'           => 'roles',
																'bsab_role'          => $selected_role,
																'bsab_preview_action'=> 'start',
																'bsab_preview_role'  => $selected_role,
																'bsab_preview_nonce' => $nonce,
															],
															admin_url('admin.php')
														);
														?>
														<p>
															<a href="<?php echo esc_url($preview_url); ?>" class="button button-secondary">
																<?php echo esc_html__('Rollen-Vorschau für diese Rolle starten', 'bs-admin-branding'); ?>
															</a>
														</p>
													<?php endif; ?>
												</td>
											</tr>
											</tbody>
										</table>
									<?php else : ?>
										<p><?php echo esc_html__('Keine Rollen gefunden.', 'bs-admin-branding'); ?></p>
									<?php endif; ?>
								</div>
							</div>

							<div class="bsab-settings-column">
								<div class="bsab-settings-card">
									<h2><?php echo esc_html__('Menüpunkte für Rolle', 'bs-admin-branding'); ?></h2>
									<?php
									global $menu, $submenu;
									$current_role_rules = ($selected_role && isset($role_menu_rules[$selected_role])) ? $role_menu_rules[$selected_role] : ['hide' => [], 'hide_submenus' => []];
									$hidden_main = $current_role_rules['hide'] ?? [];
									$hidden_subs = $current_role_rules['hide_submenus'] ?? [];
									?>
									<p class="description">
										<?php echo esc_html__('Wichtig: Diese Einstellungen steuern nur, welche Menüs sichtbar sind. Rechte und Zugriffe selbst bleiben unverändert und werden weiterhin von WordPress und anderen Plugins kontrolliert.', 'bs-admin-branding'); ?>
									</p>
									<?php if (!empty($menu)) : ?>
										<table class="form-table" role="presentation">
											<tbody>
											<?php foreach ($menu as $menu_item) :
												$slug = isset($menu_item[2]) ? (string) $menu_item[2] : '';
												$title = isset($menu_item[0]) ? wp_strip_all_tags((string) $menu_item[0]) : $slug;
												if ($slug === '' || $slug === 'separator1' || $slug === 'separator2' || $slug === 'separator-last') {
													continue;
												}
												$legacy_slug = sanitize_key($slug);
												$checked = in_array($slug, $hidden_main, true) || in_array($legacy_slug, $hidden_main, true);
												?>
												<tr>
													<th scope="row">
														<label>
															<input
																type="checkbox"
																name="<?php echo esc_attr(Defaults::OPTION_KEY . '[role_menu_rules][' . $selected_role . '][hide][]'); ?>"
																value="<?php echo esc_attr($slug); ?>"
																<?php checked($checked); ?>
															/>
															<?php echo esc_html($title); ?>
														</label>
													</th>
													<td>
														<?php
														$sub_items = $submenu[$slug] ?? [];
														if ($sub_items) :
															$hidden_for_parent = $hidden_subs[$slug] ?? $hidden_subs[$legacy_slug] ?? [];
															foreach ($sub_items as $sub_item) :
																$sub_slug = isset($sub_item[2]) ? (string) $sub_item[2] : '';
																$sub_title = isset($sub_item[0]) ? wp_strip_all_tags((string) $sub_item[0]) : $sub_slug;
																if ($sub_slug === '') {
																	continue;
																}
																$legacy_sub_slug = sanitize_key($sub_slug);
																$sub_checked = in_array($sub_slug, $hidden_for_parent, true) || in_array($legacy_sub_slug, $hidden_for_parent, true);
																?>
																<label style="display:block;margin-bottom:4px;">
																	<input
																		type="checkbox"
																		name="<?php echo esc_attr(Defaults::OPTION_KEY . '[role_menu_rules][' . $selected_role . '][hide_submenus][' . $slug . '][]'); ?>"
																		value="<?php echo esc_attr($sub_slug); ?>"
																		<?php checked($sub_checked); ?>
																	/>
																	<?php echo esc_html($sub_title); ?>
																</label>
															<?php endforeach; ?>
														<?php else : ?>
															<span class="description"><?php echo esc_html__('Keine Untermenüpunkte.', 'bs-admin-branding'); ?></span>
														<?php endif; ?>
													</td>
												</tr>
											<?php endforeach; ?>
											</tbody>
										</table>
									<?php else : ?>
										<p><?php echo esc_html__('Keine Menüeinträge gefunden.', 'bs-admin-branding'); ?></p>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php submit_button(__('Änderungen speichern', 'bs-admin-branding')); ?>
			</form>
		</div>
		<?php
	}

	private function add_checkbox_field(string $key, string $label, string $section): void {
		add_settings_field(
			$key,
			$label,
			function () use ($key): void {
				$settings = $this->get_settings();
				$value = $settings[$key] ?? '0';
				?>
				<label>
					<input
						type="checkbox"
						name="<?php echo esc_attr(Defaults::OPTION_KEY . '[' . $key . ']'); ?>"
						value="1"
						<?php checked($value, '1'); ?>
					/>
				</label>
				<?php if ($key === 'enable_login_css' || $key === 'enable_editor_css') : ?>
					<p class="description">
						<?php echo esc_html__('Diese Option ist in der aktuellen Version als Vorbereitung vorgesehen und hat möglicherweise noch keinen sichtbaren Effekt.', 'bs-admin-branding'); ?>
					</p>
				<?php endif; ?>
				<?php
			},
			'bs-admin-branding',
			$section
		);
	}

	private function add_text_field(string $key, string $label, string $section): void {
		add_settings_field(
			$key,
			$label,
			function () use ($key): void {
				$settings = $this->get_settings();
				$value = (string) ($settings[$key] ?? '');
				?>
				<input
					type="text"
					class="regular-text"
					name="<?php echo esc_attr(Defaults::OPTION_KEY . '[' . $key . ']'); ?>"
					value="<?php echo esc_attr($value); ?>"
				/>
				<?php
			},
			'bs-admin-branding',
			$section
		);
	}

	private function add_color_field(string $key, string $label, string $section): void {
		add_settings_field(
			$key,
			$label,
			function () use ($key): void {
				$settings = $this->get_settings();
				$value = (string) ($settings[$key] ?? '');
				$css_vars = $this->get_css_variables_for_color_key($key);
				?>
				<input
					type="text"
					class="bsab-color-field"
					name="<?php echo esc_attr(Defaults::OPTION_KEY . '[' . $key . ']'); ?>"
					value="<?php echo esc_attr($value); ?>"
					data-default-color="<?php echo esc_attr($value); ?>"
					<?php if ($css_vars !== '') : ?>
						data-css-vars="<?php echo esc_attr($css_vars); ?>"
					<?php endif; ?>
				/>
				<?php
			},
			'bs-admin-branding',
			$section
		);
	}

	private function get_settings(): array {
		$saved = get_option(Defaults::OPTION_KEY, []);

		return Defaults::merge(is_array($saved) ? $saved : []);
	}

	private function get_css_variables_for_color_key(string $key): string {
		switch ($key) {
			case 'color_sidebar_bg':
				return '--sidebar-bg,--bsab-sidebar-bg';
			case 'color_sidebar_submenu_bg':
				return '--sidebar-submenu-bg,--bsab-sidebar-submenu-bg';
			case 'color_sidebar_submenu_hover_bg':
				return '--sidebar-submenu-hover-bg,--bsab-sidebar-submenu-hover-bg';
			case 'color_sidebar_text':
				return '--sidebar-text,--bsab-sidebar-text';
			case 'color_sidebar_hover':
				return '--sidebar-hover,--bsab-sidebar-hover';
			case 'color_sidebar_hover_text':
				return '--sidebar-hover-text,--bsab-sidebar-hover-text';
			case 'color_sidebar_active':
				return '--sidebar-active,--bsab-sidebar-active';
			case 'color_adminbar_bg':
				return '--adminbar-bg,--bsab-adminbar-bg';
			case 'color_adminbar_text':
				return '--adminbar-text,--bsab-adminbar-text';
			case 'color_content_bg':
				return '--content-bg,--bsab-content-bg';
			case 'color_card_bg':
				return '--card-bg,--bsab-card-bg';
			case 'color_card_text':
				return '--card-text,--bsab-card-text';
			case 'color_accent':
				return '--accent,--bsab-accent';
			case 'color_accent_hover':
				return '--accent-hover,--bsab-accent-hover';
			default:
				return '';
		}
	}
}