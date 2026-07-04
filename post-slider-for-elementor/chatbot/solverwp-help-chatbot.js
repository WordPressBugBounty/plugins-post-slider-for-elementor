/**
 * SolverWP Help Chatbot — shared, reusable widget script.
 *
 * Generic on purpose: copy this file verbatim into any product plugin. Each
 * product's PHP (see solverwp-help-chatbot.php) registers its config under
 * window.SolverWPHelpChatConfigs[slug] and calls
 * SolverWPHelpChatbot.init(slug). Multiple products' widgets can coexist on
 * one page — each gets its own DOM ids/config and stacks vertically.
 *
 * UX: the toggle button pulses to draw the eye; on a visitor's first ever
 * visit to a page with the widget it auto-opens itself after 3 seconds (once
 * per browser, remembered via localStorage) with a soft two-tone chime
 * (synthesized, no audio file needed) and a bouncing-dots "typing" indicator
 * while waiting for a reply.
 */
(function () {
	window.SolverWPHelpChatConfigs = window.SolverWPHelpChatConfigs || {};

	window.SolverWPHelpChatbot = window.SolverWPHelpChatbot || (function () {
		var built = {};

		function el(tag, cls, html) {
			var n = document.createElement(tag);
			if (cls) {
				n.className = cls;
			}
			if (html !== undefined) {
				n.innerHTML = html;
			}
			return n;
		}

		function escapeHtml(s) {
			return String(s).replace(/[&<>"]/g, function (c) {
				return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c];
			});
		}

		/**
		 * Short, pleasant two-note notification chime, synthesized on the fly
		 * so the widget never needs to ship/load an audio file.
		 */
		function playChime() {
			try {
				var Ctx = window.AudioContext || window.webkitAudioContext;
				if (!Ctx) {
					return;
				}
				var ctx = new Ctx();
				var now = ctx.currentTime;
				[659.25, 987.77].forEach(function (freq, i) {
					var start = now + i * 0.11;
					var osc = ctx.createOscillator();
					var gain = ctx.createGain();
					osc.type = 'sine';
					osc.frequency.value = freq;
					gain.gain.setValueAtTime(0.0001, start);
					gain.gain.exponentialRampToValueAtTime(0.18, start + 0.02);
					gain.gain.exponentialRampToValueAtTime(0.0001, start + 0.28);
					osc.connect(gain);
					gain.connect(ctx.destination);
					osc.start(start);
					osc.stop(start + 0.3);
				});
			} catch (e) {
				// Audio not available (autoplay policy, unsupported browser) — silently skip.
			}
		}

		function build(slug) {
			if (built[slug]) {
				return;
			}
			var cfg = window.SolverWPHelpChatConfigs[slug];
			if (!cfg) {
				return;
			}
			built[slug] = true;

			var offset = 24 + (cfg.index || 0) * 72;

			var toggle = el('button', 'solverwp-chat-toggle pulse', '<span class="solverwp-chat-toggle-icon">💬</span><span class="solverwp-chat-badge">1</span>');
			toggle.id = 'solverwp-chat-toggle-' + slug;
			toggle.style.bottom = offset + 'px';
			toggle.setAttribute('aria-label', cfg.title);

			var panel = el('div', 'solverwp-chat-panel');
			panel.id = 'solverwp-chat-panel-' + slug;
			panel.style.bottom = (offset + 72) + 'px';

			var body;

			var head = el('div', 'solverwp-chat-head');
			var headInfo = el('div', 'solverwp-chat-head-info');
			headInfo.appendChild(el('span', 'solverwp-chat-avatar', '🤖'));
			var headText = el('div');
			headText.appendChild(el('strong', null, escapeHtml(cfg.title)));
			headText.appendChild(el('small', null, 'Online now'));
			headInfo.appendChild(headText);
			head.appendChild(headInfo);

			body = el('div', 'solverwp-chat-body');

			function clearBadge() {
				toggle.classList.remove('pulse');
				var badge = toggle.querySelector('.solverwp-chat-badge');
				if (badge) {
					badge.remove();
				}
			}

			function addMessage(text, who) {
				var row = el('div', 'solverwp-chat-row ' + who);
				if (who === 'bot') {
					row.appendChild(el('span', 'solverwp-chat-avatar solverwp-chat-avatar-sm', '🤖'));
				}
				var m = el('div', 'solverwp-chat-msg ' + who, escapeHtml(text));
				row.appendChild(m);
				body.appendChild(row);
				body.scrollTop = body.scrollHeight;
				return m;
			}

			function addTyping() {
				var row = el('div', 'solverwp-chat-row bot');
				row.appendChild(el('span', 'solverwp-chat-avatar solverwp-chat-avatar-sm', '🤖'));
				var m = el('div', 'solverwp-chat-msg bot solverwp-chat-typing', '<span></span><span></span><span></span>');
				row.appendChild(m);
				body.appendChild(row);
				body.scrollTop = body.scrollHeight;
				return m;
			}

			function send(text) {
				if (!text) {
					return;
				}
				addMessage(text, 'user');
				var pending = addTyping();

				var form = new FormData();
				form.append('action', cfg.action);
				form.append('nonce', cfg.nonce);
				form.append('message', text);

				fetch(cfg.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: form })
					.then(function (r) { return r.json(); })
					.then(function (res) {
						var reply = (res && res.data && res.data.reply) ? res.data.reply : 'Sorry, something went wrong.';
						pending.classList.remove('solverwp-chat-typing');
						pending.textContent = reply;
						body.scrollTop = body.scrollHeight;
						playChime();
					})
					.catch(function () {
						pending.classList.remove('solverwp-chat-typing');
						pending.textContent = 'Network error. Please try again.';
					});
			}

			var quickWrap = el('div', 'solverwp-chat-quick');
			(cfg.quick || []).forEach(function (q) {
				var b = el('button', null, escapeHtml(q));
				b.addEventListener('click', function () {
					send(q);
					quickWrap.style.display = 'none';
				});
				quickWrap.appendChild(b);
			});

			// --- "Get in touch" lead form: only submitted if the visitor
			// fills it in themselves, never sent automatically. ---
			var leadBar = el('div', 'solverwp-chat-leadbar');
			var leadToggle = el('button', 'solverwp-chat-leadbar-toggle', escapeHtml(cfg.i18n.getInTouch));
			leadBar.appendChild(leadToggle);

			var leadForm = el('div', 'solverwp-chat-leadform');
			leadForm.style.display = 'none';
			var leadName = el('input');
			leadName.type = 'text';
			leadName.placeholder = cfg.i18n.leadName;
			var leadEmail = el('input');
			leadEmail.type = 'email';
			leadEmail.placeholder = cfg.i18n.leadEmail;
			var leadTextarea = el('textarea', 'solverwp-chat-leadform-textarea');
			leadTextarea.rows = 3;
			leadTextarea.placeholder = cfg.i18n.leadMessage;
			var leadNote = el('p', 'solverwp-chat-leadform-note', escapeHtml(cfg.i18n.leadNote));
			var leadMsg = el('div', 'solverwp-chat-leadform-msg');
			var leadBtns = el('div', 'solverwp-chat-leadform-btns');
			var leadSubmit = el('button', 'solverwp-chat-leadform-submit', escapeHtml(cfg.i18n.leadSubmit));
			var leadCancel = el('button', 'solverwp-chat-leadform-cancel', escapeHtml(cfg.i18n.leadCancel));
			leadBtns.appendChild(leadSubmit);
			leadBtns.appendChild(leadCancel);
			leadForm.appendChild(leadName);
			leadForm.appendChild(leadEmail);
			leadForm.appendChild(leadTextarea);
			leadForm.appendChild(leadBtns);
			leadForm.appendChild(leadMsg);
			leadForm.appendChild(leadNote);
			leadBar.appendChild(leadForm);

			leadToggle.addEventListener('click', function () {
				var showing = leadForm.style.display !== 'none';
				leadForm.style.display = showing ? 'none' : 'block';
			});
			leadCancel.addEventListener('click', function () {
				leadForm.style.display = 'none';
			});
			leadSubmit.addEventListener('click', function () {
				var email = leadEmail.value.trim();
				if (!email || email.indexOf('@') === -1) {
					leadMsg.textContent = cfg.i18n.leadInvalid;
					leadMsg.style.color = '#b32d2e';
					return;
				}
				leadSubmit.disabled = true;
				leadMsg.textContent = cfg.i18n.leadSending;
				leadMsg.style.color = '#646970';

				var form = new FormData();
				form.append('action', cfg.leadAction);
				form.append('nonce', cfg.leadNonce);
				form.append('name', leadName.value.trim());
				form.append('email', email);
				form.append('message', leadTextarea.value.trim());

				fetch(cfg.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: form })
					.then(function (r) { return r.json(); })
					.then(function (res) {
						leadSubmit.disabled = false;
						if (res && res.success) {
							leadForm.style.display = 'none';
							leadName.value = '';
							leadEmail.value = '';
							leadTextarea.value = '';
							leadMsg.textContent = '';
							addMessage((res.data && res.data.message) || 'Thanks!', 'bot');
							playChime();
						} else {
							leadMsg.textContent = (res && res.data && res.data.message) || cfg.i18n.leadError;
							leadMsg.style.color = '#b32d2e';
						}
					})
					.catch(function () {
						leadSubmit.disabled = false;
						leadMsg.textContent = cfg.i18n.leadError;
						leadMsg.style.color = '#b32d2e';
					});
			});

			var foot = el('div', 'solverwp-chat-foot');
			var input = el('input');
			input.type = 'text';
			input.placeholder = cfg.placeholder;
			var sendBtn = el('button', 'solverwp-chat-send', '➤');
			sendBtn.setAttribute('aria-label', cfg.i18n.send);

			function submit() {
				var v = input.value.trim();
				if (v) {
					send(v);
					input.value = '';
				}
			}
			sendBtn.addEventListener('click', submit);
			input.addEventListener('keydown', function (e) {
				if (e.key === 'Enter') {
					submit();
				}
			});

			foot.appendChild(input);
			foot.appendChild(sendBtn);

			panel.appendChild(head);
			panel.appendChild(body);
			panel.appendChild(quickWrap);
			panel.appendChild(leadBar);
			panel.appendChild(foot);

			function openPanel(withChime) {
				panel.classList.add('open');
				clearBadge();
				if (!body.dataset.greeted) {
					addMessage(cfg.welcome, 'bot');
					body.dataset.greeted = '1';
					if (withChime) {
						playChime();
					}
				}
			}

			toggle.addEventListener('click', function () {
				if (panel.classList.contains('open')) {
					panel.classList.remove('open');
				} else {
					openPanel(true);
				}
			});

			document.body.appendChild(toggle);
			document.body.appendChild(panel);

			// Auto-open once per browser, 3 seconds after the widget first
			// appears on a page — a gentle nudge, not a repeat annoyance.
			var seenKey = 'solverwp_chat_seen_' + slug;
			var alreadySeen = false;
			try {
				alreadySeen = !!window.localStorage.getItem(seenKey);
			} catch (e) {
				alreadySeen = false;
			}

			if (!alreadySeen) {
				setTimeout(function () {
					if (!panel.classList.contains('open')) {
						openPanel(true);
						try {
							window.localStorage.setItem(seenKey, '1');
						} catch (e) {
							// Storage unavailable — the pulse animation still invites a click.
						}
					}
				}, 3000);
			}
		}

		return {
			init: function (slug) {
				if (document.readyState !== 'loading') {
					build(slug);
				} else {
					document.addEventListener('DOMContentLoaded', function () {
						build(slug);
					});
				}
			},
		};
	})();
})();
