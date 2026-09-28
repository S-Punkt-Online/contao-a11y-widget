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

		// Einstellungen gelten seitenweit, daher ein fester Schlüssel für alle Widgets
		const LS_KEY = 'a11y-settings';
		const LEGACY_LS_KEY = 'a11y-settings-' + (widget.id || index);

		const load = () => {
			try {
				const raw = localStorage.getItem(LS_KEY) ?? localStorage.getItem(LEGACY_LS_KEY);
				return JSON.parse(raw) || {};
			} catch {
				return {};
			}
		};

		const save = (obj) => {
			try {
				localStorage.setItem(LS_KEY, JSON.stringify(obj));
			} catch {
				// Speicher nicht verfügbar (z. B. privater Modus) – Einstellungen gelten nur für diese Seite
			}
		};

		const state = Object.assign({
			font: 100,
			reader: false,
			links: false,
			sans: false
		}, load());

		const root = document.documentElement;
		let baseFontSize = null;

		// Skaliert relativ zur Theme-Schriftgröße, statt sie zu überschreiben
		function applyFontSize() {
			if (baseFontSize === null) {
				root.style.removeProperty('font-size');
				baseFontSize = parseFloat(getComputedStyle(root).fontSize);
			}

			root.style.setProperty('--a11y-font-scale', state.font + '%');

			if (state.font === 100) {
				root.style.removeProperty('font-size');
			} else {
				root.style.fontSize = (baseFontSize * state.font / 100) + 'px';
			}
		}

		function applyState() {
			applyFontSize();

			document.body.classList.toggle('a11y-reader-mode', !!state.reader);
			document.body.classList.toggle('a11y-highlight-links', !!state.links);
			document.body.classList.toggle('a11y-sans', !!state.sans);

			if (toggleReader) toggleReader.checked = !!state.reader;
			if (toggleLinks) toggleLinks.checked = !!state.links;
			if (toggleSans) toggleSans.checked = !!state.sans;
			if (fontReset) fontReset.textContent = state.font + '%';
		}

		let lastFocus = null;

		const isOpen = () => panel.getAttribute('aria-hidden') === 'false';

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
		}

		function closePanel() {
			if (!isOpen()) {
				return;
			}

			panel.setAttribute('aria-hidden', 'true');
			fab.setAttribute('aria-expanded', 'false');
			delete backdrop.dataset.open;
			backdrop.setAttribute('aria-hidden', 'true');
			document.removeEventListener('keydown', onEsc);

			if (lastFocus) {
				lastFocus.focus();
				lastFocus = null;
			}
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
			isOpen() ? closePanel() : openPanel();
		});

		onActivate(closeBtn, closePanel);
		backdrop.addEventListener('click', closePanel);

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