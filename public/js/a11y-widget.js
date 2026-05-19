document.addEventListener('DOMContentLoaded', function () {
	'use strict';

	document.querySelectorAll('[data-a11y-widget]').forEach((widget, index) => {
		const $ = (sel) => widget.querySelector(sel);

		const fab = $('[data-a11y-fab]');
		const panel = $('[data-a11y-panel]');
		const closeBtn = $('[data-a11y-close]');
		const backdrop = $('[data-a11y-backdrop]');

		if (!fab || !panel || !closeBtn || !backdrop) {
			return;
		}

		const fontInc = $('[data-a11y-font-inc]');
		const fontDec = $('[data-a11y-font-dec]');
		const fontReset = $('[data-a11y-font-reset]');

		const toggleReader = $('[data-a11y-reader]');
		const toggleLinks = $('[data-a11y-links]');
		const toggleSans = $('[data-a11y-sans]');

		const LS_KEY = 'a11y-settings-' + (widget.id || index);

		const load = () => {
			try {
				return JSON.parse(localStorage.getItem(LS_KEY)) || {};
			} catch {
				return {};
			}
		};

		const save = (obj) => {
			localStorage.setItem(LS_KEY, JSON.stringify(obj));
		};

		const state = Object.assign({
			font: 100,
			reader: false,
			links: false,
			sans: false
		}, load());

		function applyState() {
			document.documentElement.style.setProperty('--a11y-font-scale', state.font + '%');

			document.body.classList.toggle('a11y-reader-mode', !!state.reader);
			document.body.classList.toggle('a11y-highlight-links', !!state.links);
			document.body.classList.toggle('a11y-sans', !!state.sans);

			if (toggleReader) toggleReader.checked = !!state.reader;
			if (toggleLinks) toggleLinks.checked = !!state.links;
			if (toggleSans) toggleSans.checked = !!state.sans;
			if (fontReset) fontReset.textContent = state.font + '%';
		}

		let lastFocus = null;

		function openPanel() {
			lastFocus = document.activeElement;

			panel.setAttribute('aria-hidden', 'false');
			fab.setAttribute('aria-expanded', 'true');

			backdrop.dataset.open = 'true';
			backdrop.setAttribute('aria-hidden', 'false');

			setTimeout(() => {
				const firstFocus = fontInc || fontDec || fontReset || toggleReader || toggleLinks || toggleSans || closeBtn;
				if (firstFocus) firstFocus.focus();
			}, 0);

			document.addEventListener('keydown', onEsc);
			backdrop.addEventListener('click', closePanel, { once: true });
		}

		function closePanel() {
			if (lastFocus) {
				lastFocus.focus();
			}
			panel.setAttribute('aria-hidden', 'true');
			fab.setAttribute('aria-expanded', 'false');
			delete backdrop.dataset.open;
			backdrop.setAttribute('aria-hidden', 'true');
			document.removeEventListener('keydown', onEsc);
		}

		function onEsc(e) {
			if (e.key === 'Escape') {
				closePanel();
			}
		}

		function clamp(n, min, max) {
			return Math.max(min, Math.min(max, n));
		}

		function changeFont(delta) {
			state.font = clamp(state.font + delta, 70, 200);
			save(state);
			applyState();
		}

		function onActivate(el, handler) {
			if (!el) return;

			el.addEventListener('click', handler);

			el.addEventListener('keydown', (e) => {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					handler();
				}
			});
		}

		onActivate(fab, () => {
			const isHidden = panel.getAttribute('aria-hidden') !== 'false';
			isHidden ? openPanel() : closePanel();
		});

		onActivate(closeBtn, closePanel);

		onActivate(fontInc, () => changeFont(10));
		onActivate(fontDec, () => changeFont(-10));
		onActivate(fontReset, () => {
			state.font = 100;
			save(state);
			applyState();
		});

		if (toggleReader) {
			toggleReader.addEventListener('change', () => {
				state.reader = toggleReader.checked;
				save(state);
				applyState();
			});
		}

		if (toggleLinks) {
			toggleLinks.addEventListener('change', () => {
				state.links = toggleLinks.checked;
				save(state);
				applyState();
			});
		}

		if (toggleSans) {
			toggleSans.addEventListener('change', () => {
				state.sans = toggleSans.checked;
				save(state);
				applyState();
			});
		}

		applyState();

		window.addEventListener('hashchange', closePanel);
	});
});