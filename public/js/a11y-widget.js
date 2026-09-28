(function () {
	'use strict';

	// Einstellungen gelten seitenweit, daher ein fester Schlüssel für alle Widgets
	const LS_KEY = 'a11y-settings';
	const LEGACY_LS_KEY = 'a11y-settings-0';

	const FONT_MIN = 70;
	const FONT_MAX = 200;
	const FONT_STEP = 10;

	const PAGE_CLASSES = {
		reader: 'a11y-reader-mode',
		links: 'a11y-highlight-links',
		sans: 'a11y-sans',
		spacing: 'a11y-text-spacing',
		contrast: 'a11y-high-contrast'
	};

	const DEFAULTS = {
		font: 100,
		reader: false,
		links: false,
		sans: false,
		spacing: false,
		contrast: false
	};

	const clamp = (n, min, max) => Math.max(min, Math.min(max, n));

	const read = (key) => {
		try {
			return localStorage.getItem(key);
		} catch {
			return null;
		}
	};

	const load = () => {
		try {
			return JSON.parse(read(LS_KEY) ?? read(LEGACY_LS_KEY)) || {};
		} catch {
			return {};
		}
	};

	const stored = load();

	const state = { font: clamp(Number(stored.font) || DEFAULTS.font, FONT_MIN, FONT_MAX) };

	for (const key of Object.keys(PAGE_CLASSES)) {
		state[key] = !!stored[key];
	}

	const save = () => {
		try {
			localStorage.setItem(LS_KEY, JSON.stringify(state));
		} catch {
			// Speicher nicht verfügbar (z. B. privater Modus) – Einstellungen gelten nur für diese Seite
		}
	};

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

	const pausedVideos = new Set();

	// Modus ohne Ablenkungen: laufende und automatisch startende Videos anhalten, beim Ausschalten fortsetzen
	function applyMedia() {
		if (!document.body) return;

		document.querySelectorAll('video').forEach((video) => {
			if (state.reader) {
				if (!video.paused || video.autoplay) {
					video.autoplay = false;
					video.pause();
					pausedVideos.add(video);
				}
			} else if (pausedVideos.has(video)) {
				pausedVideos.delete(video);
				video.play().catch(() => {});
			}
		});
	}

	function applyToPage() {
		applyFontSize();
		applyMedia();

		for (const [key, className] of Object.entries(PAGE_CLASSES)) {
			root.classList.toggle(className, state[key]);

			// Klassen auch am body, damit bestehendes Theme-CSS (body.a11y-…) weiter greift
			if (document.body) {
				document.body.classList.toggle(className, state[key]);
			}
		}
	}

	// Sofort anwenden (Script steht im <head>), damit die Seite nicht erst ohne Einstellungen aufblitzt
	applyToPage();

	const widgets = [];

	function update(changes) {
		Object.assign(state, changes);
		save();
		applyToPage();
		widgets.forEach((widget) => widget.sync());
	}

	function onActivate(el, handler) {
		if (!el) return;

		el.addEventListener('click', handler);

		// Angepasste Templates können noch <span role="button"> enthalten
		if (el.tagName !== 'BUTTON') {
			el.addEventListener('keydown', (e) => {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					handler();
				}
			});
		}
	}

	function initWidget(widget) {
		const $ = (sel) => widget.querySelector(sel);

		const fab = $('[data-a11y-fab]');
		const panel = $('[data-a11y-panel]');
		const closeBtn = $('[data-a11y-close]');

		if (!fab || !panel) {
			return;
		}

		const fontInc = $('[data-a11y-font-inc]');
		const fontDec = $('[data-a11y-font-dec]');
		const fontReset = $('[data-a11y-font-reset]');
		const status = $('[data-a11y-status]');
		const resetAll = $('[data-a11y-reset-all]');

		const toggles = {};

		for (const key of Object.keys(PAGE_CLASSES)) {
			toggles[key] = $('[data-a11y-' + key + ']');
		}

		// Ältere, angepasste Templates nutzen <aside> + Backdrop-Div statt <dialog>
		const isDialog = typeof panel.showModal === 'function';
		const backdrop = $('[data-a11y-backdrop]');

		let lastFocus = null;

		const isOpen = () => (isDialog ? panel.open : panel.getAttribute('aria-hidden') === 'false');

		function openPanel() {
			if (isOpen()) return;

			lastFocus = document.activeElement;
			fab.setAttribute('aria-expanded', 'true');

			if (isDialog) {
				panel.showModal();
				return;
			}

			panel.setAttribute('aria-hidden', 'false');

			if (backdrop) {
				backdrop.dataset.open = 'true';
				backdrop.setAttribute('aria-hidden', 'false');
			}

			setTimeout(() => {
				const firstFocus = fontDec || fontReset || fontInc || Object.values(toggles).find(Boolean) || closeBtn;
				if (firstFocus) firstFocus.focus();
			}, 0);

			document.addEventListener('keydown', onLegacyEsc);
		}

		function closePanel() {
			if (!isOpen()) return;

			// Beim Dialog erledigt das (asynchrone) close-Event den Rest
			if (isDialog) {
				fab.setAttribute('aria-expanded', 'false');
				panel.close();
				return;
			}

			panel.setAttribute('aria-hidden', 'true');

			if (backdrop) {
				delete backdrop.dataset.open;
				backdrop.setAttribute('aria-hidden', 'true');
			}

			document.removeEventListener('keydown', onLegacyEsc);
			onClosed();
		}

		function onClosed() {
			fab.setAttribute('aria-expanded', 'false');

			if (lastFocus && lastFocus.isConnected) {
				lastFocus.focus();
			}

			lastFocus = null;
		}

		function onLegacyEsc(e) {
			if (e.key === 'Escape') {
				closePanel();
			}
		}

		if (isDialog) {
			// Esc schließt nativ; Klicks auf ::backdrop kommen mit dem Dialog selbst als Target an
			panel.addEventListener('close', onClosed);
			panel.addEventListener('click', (e) => {
				if (e.target === panel) closePanel();
			});
		} else if (backdrop) {
			backdrop.addEventListener('click', closePanel);
		}

		function announce(text) {
			if (!status || !text) return;

			// Gleicher Text (z. B. am Minimum/Maximum) würde sonst nicht erneut angesagt
			status.textContent = status.textContent === text ? text + '\u00a0' : text;
		}

		function changeFont(font) {
			update({ font: clamp(font, FONT_MIN, FONT_MAX) });

			const prefix = status ? status.dataset.a11yStatusPrefix || '' : '';
			announce((prefix ? prefix + ': ' : '') + state.font + ' %');
		}

		onActivate(fab, () => {
			isOpen() ? closePanel() : openPanel();
		});

		onActivate(closeBtn, closePanel);

		onActivate(fontInc, () => changeFont(state.font + FONT_STEP));
		onActivate(fontDec, () => changeFont(state.font - FONT_STEP));
		onActivate(fontReset, () => changeFont(100));

		onActivate(resetAll, () => {
			update(DEFAULTS);
			announce(status ? status.dataset.a11yResetStatus : '');
		});

		for (const [key, toggle] of Object.entries(toggles)) {
			if (toggle) {
				toggle.addEventListener('change', () => update({ [key]: toggle.checked }));
			}
		}

		function sync() {
			for (const [key, toggle] of Object.entries(toggles)) {
				if (toggle) toggle.checked = state[key];
			}

			if (fontReset) fontReset.textContent = state.font + '%';
		}

		sync();
		widgets.push({ sync, close: closePanel });
	}

	function init() {
		// Erneut messen, falls Theme-CSS erst nach diesem Script geladen wurde
		baseFontSize = null;
		applyToPage();

		document.querySelectorAll('[data-a11y-widget]').forEach(initWidget);

		window.addEventListener('hashchange', () => widgets.forEach((widget) => widget.close()));
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
