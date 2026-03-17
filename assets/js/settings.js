 (function ($) {
 	$(function () {
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

 			$tab
 				.addClass('nav-tab-active')
 				.siblings('.nav-tab')
 				.removeClass('nav-tab-active');

 			$('.bsab-tab-panel')
 				.removeClass('is-active')
 				.filter('[data-bsab-tab-panel="' + target + '"]')
 				.addClass('is-active');
 		});

		$('.bsab-role-select').on('change', function () {
			var role = $(this).val();
			var url = new URL(window.location.href);
			if (role) {
				url.searchParams.set('bsab_role', role);
			} else {
				url.searchParams.delete('bsab_role');
			}
			window.location.href = url.toString();
		});
 	});
 })(jQuery);

