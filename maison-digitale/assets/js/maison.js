/*!
 * Maison Digitale — site behaviour.
 * Ported from the script blocks of the approved design files:
 * panel tracking, typed hero word, showreel, filters, questionnaire scoring
 * and the multi-step form.
 */
(function () {
	'use strict';

	var doc = document;
	var html = doc.documentElement;
	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var cfg = window.MD || { ajax: '', nonce: '', i18n: {} };
	var t = function (k, fallback) { return (cfg.i18n && cfg.i18n[k]) || fallback; };
	var isEditor = function () { return !!(doc.body && doc.body.classList.contains('elementor-editor-active')) || html.classList.contains('md-editor'); };

	function $all(sel, root) { return Array.prototype.slice.call((root || doc).querySelectorAll(sel)); }
	function once(el, key) {
		if (!el || el['__md_' + key]) { return false; }
		el['__md_' + key] = true;
		return true;
	}
	function fmt(str, a, b) { return String(str || '').replace('%1$s', a).replace('%2$s', b).replace('%s', a); }
	function esc(s) { var d = doc.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; }

	/* ------------------------------------------------------------------
	 * Reveal on scroll — fallback where CSS scroll-driven animations are missing
	 * ---------------------------------------------------------------- */
	var supportsSDA = window.CSS && CSS.supports && CSS.supports('animation-timeline: view()');
	var revealIO = null;
	function initReveal(root) {
		if (supportsSDA || isEditor()) { return; }
		if (!('IntersectionObserver' in window)) { return; }
		html.classList.add('md-io');
		if (!revealIO) {
			revealIO = new IntersectionObserver(function (entries) {
				entries.forEach(function (e) {
					if (e.isIntersecting) { e.target.classList.add('is-in'); revealIO.unobserve(e.target); }
				});
			}, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
		}
		$all('.rv', root).forEach(function (el) { if (once(el, 'rv')) { revealIO.observe(el); } });
	}

	/* ------------------------------------------------------------------
	 * Scroll lock (showreel and mobile menu)
	 * ---------------------------------------------------------------- */
	var locks = 0;
	function lock() { locks += 1; html.classList.add('md-lock'); }
	function unlock() { locks = Math.max(0, locks - 1); if (!locks) { html.classList.remove('md-lock'); } }

	/* ------------------------------------------------------------------
	 * Navigation: services dropdown and full-screen mobile menu
	 * ---------------------------------------------------------------- */
	function initNav(root) {
		$all('[data-md-nav]', root).forEach(function (nav) {
			if (!once(nav, 'nav')) { return; }
			var btn = nav.querySelector('[data-md-dd]');
			var dd = btn ? doc.getElementById(btn.getAttribute('aria-controls')) : null;
			function setDd(open) {
				if (!btn || !dd) { return; }
				btn.setAttribute('aria-expanded', open ? 'true' : 'false');
				dd.hidden = !open;
			}
			if (btn && dd) {
				btn.addEventListener('click', function (e) { e.stopPropagation(); setDd(dd.hidden); });
				doc.addEventListener('click', function (e) { if (!dd.hidden && !dd.contains(e.target)) { setDd(false); } });
				doc.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !dd.hidden) { setDd(false); btn.focus(); } });
			}
			var openB = nav.querySelector('[data-md-menu-open]');
			var menu = nav.querySelector('[data-md-menu]');
			var closeB = nav.querySelector('[data-md-menu-close]');
			function setMenu(open) {
				if (!menu) { return; }
				if (open === !menu.hidden) { return; }
				menu.hidden = !open;
				if (openB) { openB.setAttribute('aria-expanded', open ? 'true' : 'false'); }
				if (open) { lock(); if (closeB) { closeB.focus(); } } else { unlock(); if (openB) { openB.focus(); } }
			}
			if (openB && menu) {
				openB.addEventListener('click', function () { setMenu(true); });
				if (closeB) { closeB.addEventListener('click', function () { setMenu(false); }); }
				menu.addEventListener('keydown', function (e) { if (e.key === 'Escape') { setMenu(false); } });
				$all('a', menu).forEach(function (a) { a.addEventListener('click', function () { if ((a.getAttribute('href') || '').charAt(0) === '#') { setMenu(false); } }); });
			}
		});
	}

	/* ------------------------------------------------------------------
	 * Homepage panels: side dots, active panel, light/dark navigation
	 * ---------------------------------------------------------------- */
	var panelState = null;
	function initPanels() {
		var panels = $all('[data-md-panel]');
		if (!panels.length) { return; }
		var home = doc.body.classList.contains('md-mode-home');
		var navs = $all('.md-header [data-md-nav]');
		var dots = null;

		if (panelState && panelState.dots) { panelState.dots.remove(); }

		if (home && panels.length > 1 && !isEditor()) {
			dots = doc.createElement('nav');
			dots.className = 'md-dots';
			dots.setAttribute('aria-label', html.getAttribute('dir') === 'rtl' ? 'الأقسام' : 'Panels');
			panels.forEach(function (p, i) {
				var a = doc.createElement('a');
				a.href = '#' + p.id;
				a.setAttribute('aria-label', p.getAttribute('data-md-dot') || String(i + 1));
				a.appendChild(doc.createElement('span'));
				a.addEventListener('click', function (e) {
					e.preventDefault();
					p.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
				});
				dots.appendChild(a);
			});
			doc.body.appendChild(dots);
		}

		panelState = { panels: panels, dots: dots, active: -1 };

		function update() {
			var mid = window.innerHeight / 2;
			var idx = 0;
			for (var i = 0; i < panels.length; i += 1) {
				if (panels[i].getBoundingClientRect().top <= mid) { idx = i; }
			}
			if (idx === panelState.active) { return; }
			panelState.active = idx;
			var dark = panels[idx].getAttribute('data-md-tone') === 'dark';
			if (home) {
				navs.forEach(function (n) { n.classList.toggle('dk', dark); });
			}
			if (dots) {
				dots.classList.toggle('on-dark', dark);
				$all('a', dots).forEach(function (a, i) {
					a.classList.toggle('on', i === idx);
					if (i === idx) { a.setAttribute('aria-current', 'true'); } else { a.removeAttribute('aria-current'); }
				});
			}
		}
		var ticking = false;
		function onScroll() {
			if (ticking) { return; }
			ticking = true;
			window.requestAnimationFrame(function () { ticking = false; update(); });
		}
		if (!panelState.bound) {
			window.addEventListener('scroll', onScroll, { passive: true });
			window.addEventListener('resize', onScroll);
		}
		panelState.bound = true;
		update();
		initWheel();

		// "What we do" cue: go to the next panel.
		$all('[data-md-next]').forEach(function (cue) {
			if (!once(cue, 'next')) { return; }
			cue.addEventListener('click', function (e) {
				var p = cue.closest('[data-md-panel]');
				var i = panels.indexOf(p);
				var next = panels[i + 1];
				if (next) {
					e.preventDefault();
					next.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
				}
			});
		});
	}

	/* ------------------------------------------------------------------
	 * One scroll gesture = one panel.
	 * CSS snapping handles keyboard and touch. Mouse wheels and trackpads
	 * are steered here so a gesture (including trackpad inertia) always
	 * moves exactly one panel, never zero and never two.
	 * ---------------------------------------------------------------- */
	var wheelBound = false;
	function snapActive() {
		var v = window.getComputedStyle(html).scrollSnapType || '';
		return html.classList.contains('md-snap') && v.indexOf('none') === -1 && v !== '';
	}
	function initWheel() {
		if (wheelBound || reduce || isEditor() || !html.classList.contains('md-snap')) { return; }
		wheelBound = true;
		var busy = false;
		var fresh = true;
		var acc = 0;
		var quiet = null;
		var LOCK = 800;

		function alignedIndex() {
			var panels = panelState ? panelState.panels : [];
			var best = -1;
			var bestD = Infinity;
			for (var i = 0; i < panels.length; i += 1) {
				var d = Math.abs(panels[i].getBoundingClientRect().top);
				if (d < bestD) { bestD = d; best = i; }
			}
			return bestD <= 4 ? best : -1;
		}

		window.addEventListener('wheel', function (e) {
			if (!snapActive() || e.ctrlKey || html.classList.contains('md-lock')) { return; }
			if (Math.abs(e.deltaX) > Math.abs(e.deltaY)) { return; }
			var panels = panelState ? panelState.panels : [];
			var dir = e.deltaY > 0 ? 1 : (e.deltaY < 0 ? -1 : 0);
			if (!dir) { return; }

			// A gesture ends after a short pause in wheel events (trackpad inertia keeps firing).
			window.clearTimeout(quiet);
			quiet = window.setTimeout(function () { fresh = true; acc = 0; }, 160);

			if (busy || !fresh) { e.preventDefault(); return; }

			var idx = alignedIndex();
			// Not resting on a panel (e.g. a normal section added after the panels): scroll normally.
			if (idx < 0) { return; }
			var target = idx + dir;
			if (target < 0 || target >= panels.length) { return; }

			e.preventDefault();
			var px = e.deltaMode === 1 ? e.deltaY * 32 : (e.deltaMode === 2 ? e.deltaY * window.innerHeight : e.deltaY);
			acc += px;
			if (Math.abs(acc) < 24 && e.deltaMode === 0) { return; }

			fresh = false;
			busy = true;
			acc = 0;
			panels[target].scrollIntoView({ behavior: 'smooth', block: 'start' });
			window.setTimeout(function () { busy = false; }, LOCK);
		}, { passive: false });
	}

	/* ------------------------------------------------------------------
	 * Typed hero word (types, deletes, retypes through the list)
	 * ---------------------------------------------------------------- */
	function initTyped(root) {
		$all('[data-md-typed]', root).forEach(function (el) {
			if (!once(el, 'typed')) { return; }
			var words;
			try { words = JSON.parse(el.getAttribute('data-md-typed') || '[]'); } catch (e) { words = []; }
			if (!words.length) { return; }
			el.textContent = words[0];
			if (reduce || words.length < 2) { return; }
			var w = 0, c = words[0].length, del = false;
			var loaderOn = !!doc.querySelector('.md-ld') && !html.classList.contains('md-ld-seen') && !isEditor();
			var tick = function () {
				var wait = 85;
				if (!del) {
					if (c < words[w].length) { c += 1; } else { del = true; wait = 1700; }
				} else if (c > 0) { c -= 1; wait = 40; } else { del = false; w = (w + 1) % words.length; wait = 280; }
				el.textContent = words[w].slice(0, c);
				el.__mdTimer = setTimeout(tick, wait);
			};
			el.__mdTimer = setTimeout(tick, loaderOn ? 3600 : 1000);
		});
	}

	/* ------------------------------------------------------------------
	 * Background video: desktop only; still image for reduced motion
	 * ---------------------------------------------------------------- */
	function initBgVideo(root) {
		$all('[data-md-bgvideo]', root).forEach(function (v) {
			if (!once(v, 'bgv')) { return; }
			var small = window.matchMedia('(max-width: 820px)').matches;
			if (reduce || small) {
				v.removeAttribute('autoplay');
				try { v.pause(); } catch (e) {}
				if (small) { v.preload = 'none'; }
			}
		});
	}

	/* ------------------------------------------------------------------
	 * Showreel: full-screen player; page scroll locks while open
	 * ---------------------------------------------------------------- */
	function embedFor(src) {
		var m = src.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([\w-]{6,})/);
		if (m) { return 'https://www.youtube-nocookie.com/embed/' + m[1] + '?autoplay=1&rel=0'; }
		m = src.match(/vimeo\.com\/(?:video\/)?(\d+)/);
		if (m) { return 'https://player.vimeo.com/video/' + m[1] + '?autoplay=1'; }
		return '';
	}
	function initReel(root) {
		$all('[data-md-reel-open]', root).forEach(function (btn) {
			if (!once(btn, 'reel')) { return; }
			var dlg = doc.getElementById(btn.getAttribute('data-md-reel-open'));
			if (!dlg) { return; }
			if (dlg.parentNode !== doc.body && !isEditor()) { doc.body.appendChild(dlg); }
			var box = dlg.querySelector('.rl-v');
			var close = dlg.querySelector('[data-md-reel-close]');
			var src = dlg.getAttribute('data-src') || '';
			var placeholder = box ? box.innerHTML : '';
			function open() {
				if (src && box) {
					var emb = embedFor(src);
					box.classList.add('has-media');
					box.innerHTML = emb
						? '<iframe src="' + esc(emb) + '" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen title="' + esc(dlg.getAttribute('aria-label') || '') + '"></iframe>'
						: '<video src="' + esc(src) + '" controls autoplay playsinline></video>';
				}
				dlg.hidden = false;
				lock();
				if (close) { close.focus(); }
			}
			function shut() {
				if (dlg.hidden) { return; }
				dlg.hidden = true;
				if (box && src) { box.classList.remove('has-media'); box.innerHTML = placeholder; }
				unlock();
				btn.focus();
			}
			btn.addEventListener('click', open);
			if (close) { close.addEventListener('click', shut); }
			dlg.addEventListener('keydown', function (e) { if (e.key === 'Escape') { shut(); } });
		});
	}

	/* ------------------------------------------------------------------
	 * Chip filters (portfolio and blog)
	 * ---------------------------------------------------------------- */
	function bindChips(group, onPick) {
		$all('.chip', group).forEach(function (chip) {
			chip.addEventListener('click', function () {
				$all('.chip', group).forEach(function (c) {
					var on = c === chip;
					c.classList.toggle('on', on);
					c.setAttribute('aria-pressed', on ? 'true' : 'false');
				});
				onPick(chip.getAttribute('data-v') || '');
			});
		});
	}
	function resetChips(group) {
		if (!group) { return; }
		$all('.chip', group).forEach(function (c, i) {
			c.classList.toggle('on', i === 0);
			c.setAttribute('aria-pressed', i === 0 ? 'true' : 'false');
		});
	}
	function parseList(v) {
		try { var a = JSON.parse(v || '[]'); return Array.isArray(a) ? a : [a]; } catch (e) { return v ? [v] : []; }
	}

	function initPortfolio(root) {
		$all('[data-md-portfolio]', root).forEach(function (wrap) {
			if (!once(wrap, 'pf')) { return; }
			var state = { svc: '', ind: '' };
			var cards = $all('.pgrid .card', wrap);
			var empty = wrap.querySelector('.md-empty');
			function apply() {
				var filtered = state.svc !== '' || state.ind !== '';
				var shown = 0;
				cards.forEach(function (card) {
					var svc = parseList(card.getAttribute('data-svc'));
					var ok = (state.svc === '' || svc.indexOf(state.svc) >= 0) && (state.ind === '' || card.getAttribute('data-ind') === state.ind);
					card.hidden = !ok;
					if (ok) { shown += 1; }
					card.style.gridColumn = filtered ? 'span 6' : card.getAttribute('data-col');
					card.style.marginTop = filtered ? '0' : card.getAttribute('data-mt');
					var im = card.querySelector('.im');
					if (im) { im.style.aspectRatio = filtered ? '4 / 3' : card.getAttribute('data-ratio'); }
				});
				if (empty) { empty.hidden = shown > 0; }
			}
			$all('[data-md-filter]', wrap).forEach(function (g) {
				var key = g.getAttribute('data-md-filter');
				bindChips(g, function (v) { state[key] = v; apply(); });
			});
			var reset = wrap.querySelector('[data-md-reset]');
			if (reset) {
				reset.addEventListener('click', function () {
					state.svc = ''; state.ind = '';
					$all('[data-md-filter]', wrap).forEach(resetChips);
					apply();
				});
			}
		});
	}

	function initBlog(root) {
		$all('[data-md-blog]', root).forEach(function (wrap) {
			if (!once(wrap, 'blog')) { return; }
			var per = parseInt(wrap.getAttribute('data-per'), 10) || 6;
			var cards = $all('.bgrid .card', wrap);
			var more = wrap.querySelector('[data-md-more]');
			var state = { cat: '', limit: per };
			function apply() {
				var match = cards.filter(function (c) {
					return state.cat === '' || parseList(c.getAttribute('data-cat')).indexOf(state.cat) >= 0;
				});
				cards.forEach(function (c) { c.hidden = true; });
				match.slice(0, state.limit).forEach(function (c) { c.hidden = false; });
				if (more) { more.parentNode.hidden = match.length <= state.limit; }
			}
			var g = wrap.querySelector('[data-md-filter]');
			if (g) { bindChips(g, function (v) { state.cat = v; state.limit = per; apply(); }); }
			if (more) { more.addEventListener('click', function () { state.limit += per; apply(); }); }
			apply();
		});
	}

	/* ------------------------------------------------------------------
	 * Forms: validation states from the design and sending
	 * ---------------------------------------------------------------- */
	var EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

	function labelOf(input) {
		var l = input.id ? input.form && input.form.querySelector('label[for="' + input.id + '"]') : null;
		if (!l) { l = doc.querySelector('label[for="' + input.id + '"]'); }
		if (!l) { return input.name || ''; }
		var c = l.cloneNode(true);
		$all('.opt-l', c).forEach(function (n) { n.remove(); });
		return c.textContent.trim();
	}
	function clearError(input) {
		input.classList.remove('err');
		input.removeAttribute('aria-invalid');
		var m = input.parentNode.querySelector('.msg.err');
		if (m) { m.remove(); }
		input.removeAttribute('aria-describedby');
	}
	function setError(input, text) {
		clearError(input);
		input.classList.add('err');
		input.setAttribute('aria-invalid', 'true');
		var m = doc.createElement('p');
		m.className = 'msg err';
		m.id = (input.id || 'f') + '-err';
		m.innerHTML = '<svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M8 1l7 13H1z" fill="none" stroke="currentColor" stroke-width="1.5"></path><path d="M8 6v4M8 12v.5" stroke="currentColor" stroke-width="1.5"></path></svg>' + esc(text);
		input.parentNode.appendChild(m);
		input.setAttribute('aria-describedby', m.id);
	}
	function validate(scope, form) {
		var bad = [];
		$all('input[required], textarea[required], input[type="email"]', scope).forEach(function (input) {
			if (input.closest('[hidden]') && input.closest('[hidden]') !== scope) { return; }
			var v = input.value.trim();
			if (input.required && !v) { setError(input, t('required', 'This field is required.')); bad.push(input); return; }
			if (input.type === 'email' && v && !EMAIL.test(v)) { setError(input, t('email', 'Enter a complete email address.')); bad.push(input); return; }
			clearError(input);
		});
		var sum = (form || scope).querySelector('.md-sum');
		if (sum) {
			if (bad.length) {
				var head = bad.length === 1 ? t('summary1', 'One field needs attention before we can send this.') : fmt(t('summaryN', '%d fields need attention before we can send this.').replace('%d', '%1$s'), bad.length);
				sum.innerHTML = '<p class="msg err"><svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M8 1l7 13H1z" fill="none" stroke="currentColor" stroke-width="1.5"></path><path d="M8 6v4M8 12v.5" stroke="currentColor" stroke-width="1.5"></path></svg>' + esc(head) + '</p><p class="md-sum-list">' + esc(bad.map(labelOf).join(' · ')) + '</p>';
				sum.hidden = false;
			} else {
				sum.hidden = true;
				sum.innerHTML = '';
			}
		}
		if (bad.length) { bad[0].focus(); }
		return !bad.length;
	}
	function collect(form) {
		var out = {};
		$all('input[name], textarea[name], select[name]', form).forEach(function (el) {
			if (el.name === 'md_hp' || el.type === 'submit') { return; }
			if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked) { return; }
			out[el.name] = el.value;
		});
		return out;
	}
	function send(form, fields, done) {
		var type = form.getAttribute('data-md-form') || 'contact';
		var body = new FormData();
		body.append('action', 'md_form');
		body.append('nonce', cfg.nonce || '');
		body.append('form', type);
		body.append('page', window.location.href);
		var hp = form.querySelector('[name="md_hp"]');
		body.append('md_hp', hp ? hp.value : '');
		Object.keys(fields).forEach(function (k) { body.append('fields[' + k + ']', fields[k]); });
		if (!cfg.ajax || isEditor()) { done(true); return; }
		form.classList.add('md-busy');
		fetch(cfg.ajax, { method: 'POST', body: body, credentials: 'same-origin' })
			.then(function (r) { return r.json(); })
			.then(function (j) { form.classList.remove('md-busy'); done(!!(j && j.success)); })
			.catch(function () { form.classList.remove('md-busy'); done(false); });
	}
	function showFailure(form) {
		var sum = form.querySelector('.md-sum');
		if (!sum) { window.alert(t('failed', 'Something went wrong while sending. Please try again.')); return; }
		sum.innerHTML = '<p class="msg err">' + esc(t('failed', 'Something went wrong while sending. Please try again.')) + '</p>';
		sum.hidden = false;
	}
	function initForms(root) {
		$all('form.md-form', root).forEach(function (form) {
			if (!once(form, 'form')) { return; }
			form.addEventListener('input', function (e) {
				if (e.target.classList && e.target.classList.contains('err')) {
					var v = e.target.value.trim();
					if (v && (e.target.type !== 'email' || EMAIL.test(v))) { clearError(e.target); }
				}
			});
			if (form.hasAttribute('data-md-manual')) { return; }
			form.addEventListener('submit', function (e) {
				e.preventDefault();
				if (!validate(form, form)) { return; }
				send(form, collect(form), function (ok) {
					if (!ok) { showFailure(form); return; }
					var sent = form.parentNode.querySelector('.md-sent, .md-sent-line');
					form.hidden = true;
					if (sent) { sent.hidden = false; sent.focus(); }
				});
			});
		});
	}

	/* ------------------------------------------------------------------
	 * Brand audit: 12 questions, 0–3 per answer, score out of 100 per area
	 * ---------------------------------------------------------------- */
	function initAudit(root) {
		$all('[data-md-audit]', root).forEach(function (wrap) {
			if (!once(wrap, 'audit')) { return; }
			var data;
			try { data = JSON.parse(wrap.getAttribute('data-md-audit')); } catch (e) { return; }
			var Q = data.q || [];
			if (!Q.length) { return; }
			var keys = ['A', 'B', 'C', 'D'];
			var st = { phase: 'intro', q: 0, answers: [] };
			var phases = {};
			$all('[data-phase]', wrap).forEach(function (p) { phases[p.getAttribute('data-phase')] = p; });
			var form = wrap.querySelector('form[data-md-form="audit"]');

			function top() {
				var y = wrap.getBoundingClientRect().top + window.pageYOffset - 100;
				if (window.pageYOffset > y) { window.scrollTo({ top: Math.max(0, y), behavior: reduce ? 'auto' : 'smooth' }); }
			}
			function show(phase) {
				st.phase = phase;
				Object.keys(phases).forEach(function (k) { phases[k].hidden = k !== phase; });
				if (phase === 'quiz') { renderQ(); }
				if (phase === 'result') { renderResult(); }
				top();
			}
			function areaScore(ai) {
				var sum = 0, n = 0;
				Q.forEach(function (q, i) { if (q.a === ai) { sum += (st.answers[i] == null ? 0 : st.answers[i]); n += 1; } });
				return n ? Math.round((sum / (n * 3)) * 100) : 0;
			}
			function scores() {
				var s = (data.areas || []).map(function (_, i) { return areaScore(i); });
				var total = s.length ? Math.round(s.reduce(function (x, y) { return x + y; }, 0) / s.length) : 0;
				return { areas: s, total: total };
			}
			function renderQ() {
				var qi = Math.min(st.q, Q.length - 1);
				var cur = Q[qi];
				var p = phases.quiz;
				p.querySelector('[data-q-label]').textContent = fmt(data.qLabel, qi + 1, Q.length);
				p.querySelector('[data-q-area]').textContent = (data.areas || [])[cur.a] || '';
				var bar = p.querySelector('[data-q-bar]');
				bar.setAttribute('aria-valuenow', String(qi));
				bar.firstElementChild.style.width = Math.round((qi / Q.length) * 100) + '%';
				var set = p.querySelector('[data-q-set]');
				set.classList.remove('stp'); void set.offsetWidth; set.classList.add('stp');
				p.querySelector('[data-q-text]').textContent = cur.t;
				var box = p.querySelector('[data-q-opts]');
				box.innerHTML = '';
				cur.o.forEach(function (label, i) {
					var on = st.answers[qi] === i;
					var b = doc.createElement('button');
					b.type = 'button';
					b.className = 'opt' + (on ? ' on' : '');
					b.setAttribute('aria-pressed', on ? 'true' : 'false');
					b.innerHTML = '<span class="k" aria-hidden="true">' + keys[i] + '</span>' + esc(label);
					b.addEventListener('click', function () {
						st.answers[qi] = i;
						if (qi + 1 < Q.length) { st.q = qi + 1; renderQ(); top(); } else { show('details'); }
					});
					box.appendChild(b);
				});
			}
			function renderResult() {
				var sc = scores();
				var p = phases.result;
				var band = sc.total < 40 ? data.bands[0] : (sc.total < 70 ? data.bands[1] : data.bands[2]);
				p.querySelector('[data-r-total]').textContent = sc.total;
				p.querySelector('[data-r-title]').textContent = band.t;
				p.querySelector('[data-r-text]').textContent = band.x;
				var list = p.querySelector('[data-r-areas]');
				list.innerHTML = '';
				(data.areas || []).forEach(function (name, i) {
					var s = sc.areas[i];
					var note = s < 40 ? data.notes[0] : (s < 70 ? data.notes[1] : data.notes[2]);
					var row = doc.createElement('div');
					row.style.cssText = 'display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 260px), 1fr)); gap: 10px clamp(20px, 3cqw, 48px); align-items: center; border-bottom: 1px solid #d8cdbc; padding: 20px 0';
					row.innerHTML = '<div><p style="margin: 0; font-weight: 600">' + esc(name) + '</p><p style="margin: 2px 0 0; color: #62574d; font-size: 15px; line-height: 22px">' + esc(note) + '</p></div>' +
						'<div style="display: flex; align-items: center; gap: 16px"><div class="bar" role="img" aria-label="' + esc(fmt(data.outOf, name, s)) + '" style="flex: 1 1 auto; height: 10px; background: #d8cdbc"><i style="width: ' + s + '%"></i></div>' +
						'<span class="fh" style="font-size: 26px; width: 84px; text-align: end">' + s + '<span style="font-size: 14px; color: #62574d"> / 100</span></span></div>';
					list.appendChild(row);
				});
			}

			$all('[data-md-start]', wrap).forEach(function (b) { b.addEventListener('click', function () { st.q = 0; st.answers = []; show('quiz'); }); });
			var prev = wrap.querySelector('[data-md-prev]');
			if (prev) { prev.addEventListener('click', function () { if (st.q > 0) { st.q -= 1; renderQ(); } else { show('intro'); } }); }
			var backq = wrap.querySelector('[data-md-backq]');
			if (backq) { backq.addEventListener('click', function () { st.q = Q.length - 1; show('quiz'); }); }
			var retake = wrap.querySelector('[data-md-retake]');
			if (retake) { retake.addEventListener('click', function () { st.q = 0; st.answers = []; show('quiz'); }); }
			if (form) {
				form.addEventListener('submit', function (e) {
					e.preventDefault();
					if (!validate(form, form)) { return; }
					var sc = scores();
					var fields = collect(form);
					fields['Brand score'] = sc.total + ' / 100';
					(data.areas || []).forEach(function (name, i) { fields[name] = sc.areas[i] + ' / 100'; });
					Q.forEach(function (q, i) { fields['Q' + (i + 1) + '. ' + q.t] = q.o[st.answers[i]] || '—'; });
					send(form, fields, function () {});
					show('result');
				});
			}
		});
	}

	/* ------------------------------------------------------------------
	 * Let's work together: four steps, one question per screen
	 * ---------------------------------------------------------------- */
	function initEnquiry(root) {
		$all('[data-md-enquiry]', root).forEach(function (wrap) {
			if (!once(wrap, 'enq')) { return; }
			var data;
			try { data = JSON.parse(wrap.getAttribute('data-md-enquiry')); } catch (e) { data = { names: [], label: 'Step %1$s of %2$s' }; }
			var form = wrap.querySelector('form');
			var steps = $all('[data-step]', wrap);
			var bars = $all('[data-e-bars] span', wrap);
			var prev = wrap.querySelector('[data-e-prev]');
			var next = wrap.querySelector('[data-e-next]');
			var sendB = wrap.querySelector('[data-e-send]');
			var done = wrap.querySelector('[data-e-done]');
			var step = 1;

			$all('[data-multi] .chip', wrap).forEach(function (c) {
				c.addEventListener('click', function () {
					var on = !c.classList.contains('on');
					c.classList.toggle('on', on);
					c.setAttribute('aria-pressed', on ? 'true' : 'false');
				});
			});
			$all('[data-single]', wrap).forEach(function (g) {
				$all('.chip', g).forEach(function (c) {
					c.addEventListener('click', function () {
						$all('.chip', g).forEach(function (o) { var on = o === c; o.classList.toggle('on', on); o.setAttribute('aria-pressed', on ? 'true' : 'false'); });
					});
				});
			});

			function render() {
				steps.forEach(function (s) { s.hidden = parseInt(s.getAttribute('data-step'), 10) !== step; });
				wrap.querySelector('[data-e-label]').textContent = fmt(data.label, Math.min(step, 4), 4);
				wrap.querySelector('[data-e-name]').textContent = (data.names || [])[Math.min(step, 4) - 1] || '';
				wrap.querySelector('[data-e-bars]').setAttribute('aria-valuenow', String(step));
				bars.forEach(function (b, i) { b.style.background = i < step ? '#c9a24f' : '#3b322a'; });
				prev.hidden = step <= 1;
				next.hidden = step >= 4;
				sendB.hidden = step !== 4;
			}
			function go(n) {
				step = Math.max(1, Math.min(4, n));
				render();
				var focusable = wrap.querySelector('[data-step="' + step + '"] .fi, [data-step="' + step + '"] .chip');
				if (focusable && step > 1) { focusable.focus({ preventScroll: true }); }
			}
			next.addEventListener('click', function () { go(step + 1); });
			prev.addEventListener('click', function () { go(step - 1); });
			form.addEventListener('submit', function (e) {
				e.preventDefault();
				var scope = wrap.querySelector('[data-step="4"]');
				if (!validate(scope, scope)) { return; }
				var fields = {};
				$all('[data-multi]', wrap).forEach(function (g) {
					fields[g.getAttribute('data-multi')] = $all('.chip.on', g).map(function (c) { return c.getAttribute('data-v'); }).join(', ');
				});
				$all('[data-single]', wrap).forEach(function (g) {
					var on = g.querySelector('.chip.on');
					fields[g.getAttribute('data-single')] = on ? on.getAttribute('data-v') : '';
				});
				var rest = collect(form);
				Object.keys(rest).forEach(function (k) { fields[k] = rest[k]; });
				send(form, fields, function (ok) {
					if (!ok) { showFailure(scope); return; }
					form.hidden = true;
					done.hidden = false;
					done.focus();
				});
			});
			var restart = wrap.querySelector('[data-e-restart]');
			if (restart) {
				restart.addEventListener('click', function () {
					form.reset();
					$all('.chip', wrap).forEach(function (c) { c.classList.remove('on'); c.setAttribute('aria-pressed', 'false'); });
					$all('.fi', wrap).forEach(clearError);
					var sum = wrap.querySelector('.md-sum'); if (sum) { sum.hidden = true; }
					done.hidden = true;
					form.hidden = false;
					go(1);
				});
			}
			render();
		});
	}

	/* ------------------------------------------------------------------
	 * Copy-link button on articles
	 * ---------------------------------------------------------------- */
	function initCopy(root) {
		$all('[data-md-copy]', root).forEach(function (b) {
			if (!once(b, 'copy')) { return; }
			b.addEventListener('click', function () {
				var url = b.getAttribute('data-md-copy');
				var ok = function () {
					var old = b.getAttribute('aria-label');
					b.setAttribute('aria-label', t('copied', 'Link copied'));
					b.setAttribute('title', t('copied', 'Link copied'));
					setTimeout(function () { b.setAttribute('aria-label', old); b.removeAttribute('title'); }, 2000);
				};
				if (navigator.clipboard) { navigator.clipboard.writeText(url).then(ok); } else { window.prompt('', url); }
			});
		});
	}

	/* ------------------------------------------------------------------
	 * Boot
	 * ---------------------------------------------------------------- */
	function init(root) {
		root = root || doc;
		initNav(root);
		initTyped(root);
		initBgVideo(root);
		initReel(root);
		initPortfolio(root);
		initBlog(root);
		initForms(root);
		initAudit(root);
		initEnquiry(root);
		initCopy(root);
		initReveal(root);
	}

	function boot() {
		init(doc);
		initPanels();
	}

	if (doc.readyState === 'loading') { doc.addEventListener('DOMContentLoaded', boot); } else { boot(); }

	// Elementor editor: widgets re-render one at a time.
	window.addEventListener('elementor/frontend/init', function () {
		if (!window.elementorFrontend || !window.elementorFrontend.hooks || window.__mdHooked) { return; }
		window.__mdHooked = true;
		window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
			var el = $scope && ($scope[0] || $scope);
			if (el && el.nodeType === 1) { init(el); initPanels(); }
		});
	});
	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !window.elementorFrontend.hooks || window.__mdHooked) { return; }
			window.__mdHooked = true;
			window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
				var el = $scope && $scope[0];
				if (el) { init(el); initPanels(); }
			});
		});
	}
	window.MaisonDigitale = { init: init, panels: initPanels };
})();
