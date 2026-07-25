/**
 * CADnest theme — small, self-written progressive enhancements.
 *
 * The navigation itself is 100% CSS-driven (the mobile menu is a checkbox
 * toggle; the desktop "more" dropdown opens on hover/focus), so the site is
 * fully usable with JavaScript disabled. This only adds two nicities and
 * contains no third-party / platform code.
 */
(function () {
	'use strict';

	// Close the mobile menu after a nav link is tapped.
	var toggle = document.getElementById('menuToggleTrigger');
	if (toggle) {
		var links = document.querySelectorAll('.navigation-list a');
		for (var i = 0; i < links.length; i++) {
			links[i].addEventListener('click', function () {
				toggle.checked = false;
			});
		}
	}

	// Touch support for the desktop "more options" dropdown (tap to open/close).
	var moreBtn = document.querySelector('.navigation-more-button');
	var moreList = document.querySelector('.navigation-list-more');
	if (moreBtn && moreList) {
		moreBtn.setAttribute('aria-expanded', 'false');
		moreBtn.addEventListener('click', function (e) {
			e.preventDefault();
			var open = moreList.classList.toggle('cadnest-more-open');
			moreBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
		document.addEventListener('click', function (e) {
			if (!moreBtn.contains(e.target) && !moreList.contains(e.target)) {
				moreList.classList.remove('cadnest-more-open');
				moreBtn.setAttribute('aria-expanded', 'false');
			}
		});
	}
})();
