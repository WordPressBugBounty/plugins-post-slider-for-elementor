/**
 * Post Slider Addons — shared widget behaviour (2.2.0 widget pack).
 *
 * Only the carousels (Category Slider, Related Post Slider) need JS: prev/next
 * buttons scrolling a scroll-snap flex track. Dependency-free, and hooked into
 * Elementor's frontend/element_ready so it re-initializes widgets rendered
 * over AJAX inside the editor preview too.
 */
(function () {
	function initCarousels(scope) {
		var root = scope && scope.querySelectorAll ? scope : document;
		var carousels = root.querySelectorAll('.psea-carousel:not([data-psea-init])');

		Array.prototype.forEach.call(carousels, function (carousel) {
			carousel.setAttribute('data-psea-init', '1');

			var track = carousel.querySelector('.psea-carousel-track');
			var prev = carousel.querySelector('.psea-carousel-prev');
			var next = carousel.querySelector('.psea-carousel-next');
			if (!track) {
				return;
			}

			function step() {
				var item = track.querySelector('.psea-carousel-item');
				return item ? item.getBoundingClientRect().width + 20 : 300;
			}

			if (prev) {
				prev.addEventListener('click', function () {
					track.scrollBy({ left: -step(), behavior: 'smooth' });
				});
			}
			if (next) {
				next.addEventListener('click', function () {
					track.scrollBy({ left: step(), behavior: 'smooth' });
				});
			}
		});
	}

	// Normal frontend load.
	if (document.readyState !== 'loading') {
		initCarousels(document);
	} else {
		document.addEventListener('DOMContentLoaded', function () {
			initCarousels(document);
		});
	}

	// Elementor editor preview + frontend: re-init whenever any element is
	// (re)rendered, since editor widgets arrive over AJAX after page load.
	window.addEventListener('elementor/frontend/init', function () {
		if (window.elementorFrontend && window.elementorFrontend.hooks) {
			window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
				initCarousels($scope && $scope[0] ? $scope[0] : document);
			});
		}
	});
})();
