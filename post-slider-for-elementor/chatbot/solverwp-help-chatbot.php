<?php

/**
 * SolverWP Help Chatbot — shared, reusable, self-contained library.
 *
 * Everything the widget needs (this class + its JS + its CSS) lives in this
 * one "chatbot/" folder. Product-specific settings (AI server URL, product
 * info, allowed pages, knowledge base) live in a separate sibling file,
 * config.php, so future changes never touch this class. To add it to a NEW
 * product:
 *
 *   1. Copy the WHOLE "chatbot/" folder into the product's plugin.
 *   2. Replace config.php with that product's own settings (see its own
 *      docblock — it's a plain array, nothing to program).
 *   3. In the product's loader (see inc/class-nextpage-helper-chatbot.php for
 *      the exact 5-line pattern), require this file + config.php, then:
 *      new SolverWP_Help_Chatbot( $config );
 *
 * This file itself (and the .js/.css next to it) should never need editing —
 * only config.php does.
 *
 * ─────────────────────────────────────────────────────────────────────────
 * config.php SHAPE (what to put in the new product's copy)
 * ─────────────────────────────────────────────────────────────────────────
 *
 *   return array(
 *       'server_url'  => '',              // AI server URL; empty = auto (see server_url())
 *       'product'     => 'purepen',        // must match the product slug on the AI server
 *       'title'       => __( 'PurePen Assistant', 'purepen-ai' ),
 *       'text_domain' => 'purepen-ai',
 *       'placeholder' => __( 'Ask how to use PurePen…', 'purepen-ai' ),
 *
 *       // Only show the widget on these admin screens. Match either the
 *       // `page` query var (admin.php?page=xxx submenus) or the current
 *       // screen id (post type edit/list screens). Leave empty/omit to show
 *       // on every wp-admin page.
 *       'pages' => array( 'purepen-dashboard', 'purepen-settings' ),
 *
 *       'fallback_welcome' => __( "Hi! I'm the PurePen assistant…", 'purepen-ai' ),
 *       'fallback_quick'   => array( 'How do I generate a post?', 'How do credits work?' ),
 *
 *       'kb' => array(
 *           array( 'k' => array( 'generate', 'write a post' ), 'a' => 'Open a post, click Generate…' ),
 *           array( 'k' => array( 'credit', 'trial' ),          'a' => 'New sites get 20 free generations…' ),
 *       ),
 *   );
 *
 * No 'plugin_url' or asset path config is needed — this file resolves its own
 * JS/CSS from its own folder automatically.
 *
 * That is the whole integration. The widget:
 *   1. Answers instantly from the 'kb' array (no AI, no network call).
 *   2. If nothing matches and the user flips the "AI" switch, the question is
 *      proxied to the SolverWP AI Server (see the solverwp-ai-server plugin),
 *      which replies using that product's registered system prompt.
 *
 * Add the matching product entry once on the server
 * (solverwp-ai-server/includes/class-swpai-products.php, or via the
 * `swpai_products` filter) so AI replies are grounded in that product's facts.
 *
 * @package SolverWPHelpChatbot
 * @version 1.1.0
 */

if (!defined('ABSPATH')) {
	exit(); //exit if access directly
}

if (!class_exists('SolverWP_Help_Chatbot')) {

	class SolverWP_Help_Chatbot
	{
		/** Counts instances on one page load, so widgets stack instead of overlapping. */
		private static $count = 0;

		private $cfg;
		private $index;

		/**
		 * @param array $config {
		 *     @type string $product          Required. Product slug (matches the AI server's product registry).
		 *     @type string $title            Widget header title.
		 *     @type string $text_domain      Text domain for translations.
		 *     @type string $capability       Capability required to see the widget. Default 'edit_posts'.
		 *     @type array  $pages            Admin `page` query var values and/or screen ids to restrict the
		 *                                    widget to. Empty/omitted = show on every wp-admin page.
		 *     @type string $version          Asset cache-busting version. Defaults to the bundled JS file's mtime.
		 *     @type string $server_url       Optional explicit AI server base URL, else SOLVERWP_AI_SERVER
		 *                                    constant, else this site's own REST URL.
		 *     @type array  $kb               Knowledge base: array of array('k' => array of keywords, 'a' => answer).
		 *     @type string $fallback_welcome Greeting used if the server is unreachable.
		 *     @type array  $fallback_quick   Quick-reply chips used if the server is unreachable.
		 *     @type string $placeholder      Input placeholder text.
		 * }
		 * */
		public function __construct($config)
		{
			$defaults = array(
				'product'           => '',
				'title'             => __('Assistant', 'solverwp'),
				'text_domain'       => 'solverwp',
				'capability'        => 'edit_posts',
				'pages'             => array(),
				'version'           => '',
				'server_url'        => '',
				'kb'                => array(),
				'fallback_welcome'  => __('Hi! 👋 How can I help you today?', 'solverwp'),
				'fallback_quick'    => array(),
				'placeholder'       => __('Type your question…', 'solverwp'),
				'unreachable_reply' => __("I'm having trouble reaching the AI service right now, so I can't give a smart answer to that one. Please try again shortly, or email support@solverwp.com and our team will help directly. 💌", 'solverwp'),
			);
			$this->cfg = wp_parse_args($config, $defaults);

			if (empty($this->cfg['product'])) {
				return; // misconfigured: do nothing rather than fatal.
			}

			if (empty($this->cfg['version'])) {
				$js_path = __DIR__ . '/solverwp-help-chatbot.js';
				$this->cfg['version'] = file_exists($js_path) ? (string) filemtime($js_path) : '1.1.0';
			}

			add_action('admin_enqueue_scripts', array($this, 'assets'));
			add_action('wp_ajax_' . $this->ajax_action(), array($this, 'ajax_chat'));
			add_action('wp_ajax_' . $this->lead_action(), array($this, 'ajax_lead'));
		}

		/**
		 * Per-product AJAX action name, so multiple SolverWP chatbots can be
		 * active on the same site without colliding.
		 * */
		private function ajax_action()
		{
			return 'solverwp_help_chat_' . sanitize_key($this->cfg['product']);
		}

		/**
		 * Per-product AJAX action name for the "get in touch" lead form.
		 * */
		private function lead_action()
		{
			return 'solverwp_help_lead_' . sanitize_key($this->cfg['product']);
		}

		/**
		 * Resolve the AI server base URL: explicit config, else the
		 * SOLVERWP_AI_SERVER constant, else this site's own REST URL (useful
		 * when the server plugin is active locally). Filterable per product.
		 * */
		private function server_url()
		{
			$product = sanitize_key($this->cfg['product']);

			$url = $this->cfg['server_url'];
			if (!$url) {
				$url = defined('SOLVERWP_AI_SERVER') ? SOLVERWP_AI_SERVER : home_url('/wp-json/solverwp-ai/v1');
			}
			$url = apply_filters('solverwp_help_chatbot_server_url', $url, $product);
			$url = apply_filters("solverwp_help_chatbot_server_url_{$product}", $url);

			return trailingslashit($url);
		}

		/**
		 * Whether the widget should render on the current admin screen, based
		 * on the 'pages' config (matches $_GET['page'] or the screen id).
		 * Empty 'pages' = show everywhere.
		 * */
		private function should_display()
		{
			$pages = array_filter((array) $this->cfg['pages']);
			if (empty($pages)) {
				return true;
			}

			$current_page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ($current_page && in_array($current_page, $pages, true)) {
				return true;
			}

			if (function_exists('get_current_screen')) {
				$screen = get_current_screen();
				if ($screen && in_array($screen->id, $pages, true)) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Match a message against a knowledge base. Returns the best-scoring
		 * answer, or null if nothing matched well enough.
		 *
		 * Multi-word keywords (e.g. "block ip") match if ALL of their words
		 * appear anywhere in the message, in any order/spacing — so "how do I
		 * block an IP address" matches "block ip" even though "an" sits
		 * between them. This also means the highest-scoring (most specific,
		 * most words matched) entry wins rather than whichever happens to be
		 * listed first, so a broad one-word keyword in one entry can no
		 * longer accidentally steal a question that a more specific entry
		 * also matches.
		 * */
		public static function match_kb($message, $kb)
		{
			$text  = strtolower($message);
			$best  = null;
			$score = 0;

			foreach ((array) $kb as $entry) {
				foreach ((array) $entry['k'] as $keyword) {
					$keyword = strtolower(trim($keyword));
					if ('' === $keyword) {
						continue;
					}

					$words        = preg_split('/\s+/', $keyword);
					$words_found  = 0;
					foreach ($words as $word) {
						if ('' !== $word && false !== strpos($text, $word)) {
							$words_found++;
						}
					}

					// Every word in the keyword phrase must appear for it to count.
					if ($words_found === count($words) && $words_found > $score) {
						$score = $words_found;
						$best  = $entry['a'];
					}
				}
			}

			return $best;
		}

		/**
		 * Enqueue the (shared) widget assets + this instance's config, only on
		 * the allowed admin screens.
		 * */
		public function assets()
		{
			if (!current_user_can($this->cfg['capability']) || !$this->should_display()) {
				return;
			}

			// Assign the stacking index only now that we know this widget will
			// actually render on this page. Other SolverWP plugins may also be
			// active and construct their own instance in the same request —
			// counting those (which never display here) would push this
			// widget's CSS "bottom" offset far too high on the page.
			$this->index = self::$count++;

			$product = sanitize_key($this->cfg['product']);
			$handle  = 'solverwp-help-chatbot-' . $product;
			$base_url = plugin_dir_url(__FILE__); // this "chatbot/" folder, wherever it was copied to.

			wp_enqueue_style($handle, $base_url . 'solverwp-help-chatbot.css', array(), $this->cfg['version']);
			wp_enqueue_script($handle, $base_url . 'solverwp-help-chatbot.js', array(), $this->cfg['version'], true);

			$welcome = $this->cfg['fallback_welcome'];
			$quick   = $this->cfg['fallback_quick'];

			// Try to fetch the product's greeting/quick-replies from the server.
			$remote = wp_remote_get($this->server_url() . 'welcome?product=' . $product, array('timeout' => 5));
			if (!is_wp_error($remote) && 200 === wp_remote_retrieve_response_code($remote)) {
				$data = json_decode(wp_remote_retrieve_body($remote), true);
				if (!empty($data['welcome'])) {
					$welcome = $data['welcome'];
				}
				if (!empty($data['quick']) && is_array($data['quick'])) {
					$quick = $data['quick'];
				}
			}

			$config = array(
				'slug'        => $product,
				'index'       => $this->index,
				'ajaxUrl'     => admin_url('admin-ajax.php'),
				'action'      => $this->ajax_action(),
				'nonce'       => wp_create_nonce($this->ajax_action()),
				'leadAction'  => $this->lead_action(),
				'leadNonce'   => wp_create_nonce($this->lead_action()),
				'welcome'     => $welcome,
				'quick'       => array_values($quick),
				'title'       => $this->cfg['title'],
				'placeholder' => $this->cfg['placeholder'],
				'i18n'        => array(
					'send'          => __('Send', $this->cfg['text_domain']),
					'thinking'      => __('Thinking…', $this->cfg['text_domain']),
					'getInTouch'    => __('💡 Missing something? Write us', $this->cfg['text_domain']),
					'leadName'      => __('Your name (optional)', $this->cfg['text_domain']),
					'leadEmail'     => __('Your email', $this->cfg['text_domain']),
					'leadMessage'   => __('What can we help with? (optional)', $this->cfg['text_domain']),
					'leadNote'      => __('🔒 Your chat isn\'t saved anywhere — only what you share here is sent to us.', $this->cfg['text_domain']),
					'leadSubmit'    => __('Send', $this->cfg['text_domain']),
					'leadCancel'    => __('Cancel', $this->cfg['text_domain']),
					'leadSending'   => __('Sending…', $this->cfg['text_domain']),
					'leadInvalid'   => __('Please enter a valid email address.', $this->cfg['text_domain']),
					'leadError'     => __('Something went wrong. Please try again.', $this->cfg['text_domain']),
				),
			);

			wp_add_inline_script(
				$handle,
				'window.SolverWPHelpChatConfigs = window.SolverWPHelpChatConfigs || {};'
					. 'window.SolverWPHelpChatConfigs[' . wp_json_encode($product) . '] = ' . wp_json_encode($config) . ';',
				'before'
			);
			wp_add_inline_script(
				$handle,
				'window.SolverWPHelpChatbot && window.SolverWPHelpChatbot.init(' . wp_json_encode($product) . ');'
			);
		}

		/**
		 * AJAX: answer a chat message. Instant canned answer from the
		 * knowledge base when there's a match; otherwise automatically asks
		 * the AI server — no manual "AI" toggle needed.
		 * */
		public function ajax_chat()
		{
			check_ajax_referer($this->ajax_action(), 'nonce');
			if (!current_user_can($this->cfg['capability'])) {
				wp_send_json_error(array('reply' => __('Not allowed.', $this->cfg['text_domain'])));
			}

			$message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';
			$message = trim($message);

			if ('' === $message) {
				wp_send_json_error(array('reply' => __('Please type a question.', $this->cfg['text_domain'])));
			}

			// 1. Instant canned answer.
			$canned = self::match_kb($message, $this->cfg['kb']);
			if (null !== $canned) {
				wp_send_json_success(array('reply' => $canned, 'source' => 'kb'));
			}

			// 2. No canned match — automatically ask the AI server.
			$response = wp_remote_post($this->server_url() . 'chat', array(
				'timeout' => 45,
				'headers' => array('Content-Type' => 'application/json'),
				'body'    => wp_json_encode(array(
					'product'  => sanitize_key($this->cfg['product']),
					'site_url' => home_url(),
					'message'  => $message,
				)),
			));

			if (is_wp_error($response)) {
				wp_send_json_success(array('reply' => $this->cfg['unreachable_reply'], 'source' => 'fallback'));
			}

			$data  = json_decode(wp_remote_retrieve_body($response), true);
			$reply = (is_array($data) && !empty($data['reply'])) ? $data['reply'] : __('Sorry, I couldn\'t get a reply just now. Please try again, or email support@solverwp.com for direct help. 💌', $this->cfg['text_domain']);

			wp_send_json_success(array('reply' => $reply, 'source' => 'ai'));
		}

		/**
		 * AJAX: forward a voluntarily-submitted name/email/message to the AI
		 * server's lead endpoint. Only ever called when the user fills in and
		 * submits the widget's "get in touch" form — never automatic. This is
		 * the only user content this chatbot ever sends for storage; regular
		 * chat messages are answered but never saved.
		 * */
		public function ajax_lead()
		{
			check_ajax_referer($this->lead_action(), 'nonce');
			if (!current_user_can($this->cfg['capability'])) {
				wp_send_json_error(array('message' => __('Not allowed.', $this->cfg['text_domain'])));
			}

			$name    = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
			$email   = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
			$message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';

			if (empty($email) || !is_email($email)) {
				wp_send_json_error(array('message' => __('Please enter a valid email address.', $this->cfg['text_domain'])));
			}

			$response = wp_remote_post($this->server_url() . 'lead', array(
				'timeout' => 20,
				'headers' => array('Content-Type' => 'application/json'),
				'body'    => wp_json_encode(array(
					'product'  => sanitize_key($this->cfg['product']),
					'site_url' => home_url(),
					'name'     => $name,
					'email'    => $email,
					'message'  => $message,
				)),
			));

			if (is_wp_error($response)) {
				wp_send_json_error(array('message' => __('Could not reach the server. Please try again.', $this->cfg['text_domain'])));
			}

			$data = json_decode(wp_remote_retrieve_body($response), true);
			if (is_array($data) && !empty($data['success'])) {
				wp_send_json_success(array('message' => isset($data['message']) ? $data['message'] : __('Thanks!', $this->cfg['text_domain'])));
			}

			wp_send_json_error(array('message' => (is_array($data) && !empty($data['message'])) ? $data['message'] : __('Something went wrong. Please try again.', $this->cfg['text_domain'])));
		}
	} //end class
}
