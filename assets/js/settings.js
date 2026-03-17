 (function ($) {
 	$(function () {
		var storageKey = 'bsab_active_tab';

		function setActiveTab(tabId) {
			if (!tabId) {
				return;
			}

			var $tabs = $('.bsab-tabs .nav-tab');
			var $panels = $('.bsab-tab-panel');

			$tabs.each(function () {
				var $t = $(this);
				var tId = $t.data('bsab-tab');
				var isActive = tId === tabId;

				$t.toggleClass('nav-tab-active', isActive);
				$t.attr('aria-selected', isActive ? 'true' : 'false');
			});

			$panels.each(function () {
				var $p = $(this);
				var pId = $p.data('bsab-tab-panel');
				var isActive = pId === tabId;

				$p.toggleClass('is-active', isActive);
				if (isActive) {
					$p.attr('aria-hidden', 'false');
				} else {
					$p.attr('aria-hidden', 'true');
				}
			});

			try {
				var url = new URL(window.location.href);
				url.searchParams.set('bsab_tab', tabId);
				window.history.replaceState({}, '', url.toString());
			} catch (e) {
				// ignore URL errors
			}

			try {
				window.localStorage.setItem(storageKey, tabId);
			} catch (e) {
				// ignore storage errors
			}
		}

 		$('.bsab-color-field').each(function () {
 			var $input = $(this);
 			var cssVars = ($input.data('css-vars') || '').toString().split(',').map(function (v) {
 				return v.trim();
 			}).filter(Boolean);

 			$input.wpColorPicker({
 				change: function (event, ui) {
 					var color = ui.color.toString();
 					updatePreview(cssVars, color);
 				},
 				clear: function () {
 					updatePreview(cssVars, '');
 				}
 			});

 			if (cssVars.length && $input.val()) {
 				updatePreview(cssVars, $input.val());
 			}
 		});

 		function updatePreview(cssVars, value) {
 			if (!cssVars.length) {
 				return;
 			}

 			var root = document.documentElement;

 			cssVars.forEach(function (name) {
 				if (!name) {
 					return;
 				}
 				if (value) {
 					root.style.setProperty(name, value);
 				} else {
 					root.style.removeProperty(name);
 				}
 			});
 		}

 		var $figtreeToggle = $('input[name="bsab_settings[enable_figtree_font]"]');

 		if ($figtreeToggle.length) {
 			$figtreeToggle.on('change', function () {
 				var useFigtree = $(this).is(':checked');
 				var fontStackFigtree = 'Figtree, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen-Sans, Ubuntu, Cantarell, Helvetica Neue, sans-serif';
 				var fontStackSystem = '-apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen-Sans, Ubuntu, Cantarell, Helvetica Neue, sans-serif';
 				var fontStack = useFigtree ? fontStackFigtree : fontStackSystem;

 				document.documentElement.style.setProperty('--custom-font', fontStack);
 				document.documentElement.style.setProperty('--bsab-font-family', fontStack);
 				document.documentElement.style.setProperty('--font-family', fontStack);
 			});
 		}

 		$('.bsab-tabs .nav-tab').on('click', function (event) {
 			event.preventDefault();

 			var $tab = $(this);
 			var target = $tab.data('bsab-tab');

 			if (!target) {
 				return;
 			}

			setActiveTab(target);
 		});

		(function initActiveTab() {
			var initial = 'general';

			try {
				var url = new URL(window.location.href);
				var fromUrl = url.searchParams.get('bsab_tab');
				if (fromUrl) {
					initial = fromUrl;
				} else {
					var fromStorage = window.localStorage.getItem(storageKey);
					if (fromStorage) {
						initial = fromStorage;
					}
				}
			} catch (e) {
				// fallback auf default
			}

			setActiveTab(initial);
		})();

		$('.bsab-role-select').on('change', function () {
			var role = $(this).val();
			try {
				var url = new URL(window.location.href);
				if (role) {
					url.searchParams.set('bsab_role', role);
				} else {
					url.searchParams.delete('bsab_role');
				}
				window.location.href = url.toString();
			} catch (e) {
				this.form.submit();
			}
		});
 	});
 })(jQuery);

