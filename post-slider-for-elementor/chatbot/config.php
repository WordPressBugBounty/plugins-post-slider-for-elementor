<?php

/**
 * Smart Post Showcase (Post Slider Elementor Addons) chatbot configuration.
 *
 * The ONLY file you should need to edit for this product's chatbot: the AI
 * server URL, product identity, allowed admin pages, and the knowledge base.
 * The library itself (solverwp-help-chatbot.php/.js/.css) stays generic and
 * untouched — do not edit those.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; //exit if access directly
}

return array(

	// ── AI Server ──────────────────────────────────────────────────────────
	// Leave empty to use the SOLVERWP_AI_SERVER constant (wp-config.php) or,
	// if neither is set, this site's own REST API.
	'server_url' => 'https://solverwp.com/wp-json/solverwp-ai/v1',

	// ── Product identity ──────────────────────────────────────────────────
	// Must match the product slug registered on the AI server
	// (solverwp-ai-server/includes/class-swpai-products.php).
	'product'     => 'smart-post-showcase',
	'title'       => __( 'Smart Post Showcase Assistant', 'psea' ),
	'text_domain' => 'psea',
	'capability'  => 'manage_options', // matches this plugin's own admin page
	'placeholder' => __( 'Ask how to use the widgets…', 'psea' ),

	// ── Visibility ─────────────────────────────────────────────────────────
	// Only show the widget on the plugin's own admin screen.
	'pages' => array(
		'psea-settings',
	),

	// ── Greeting shown if the AI server is unreachable ──────────────────────
	'fallback_welcome' => __( "Hi! 🎠 I'm the Smart Post Showcase assistant. Ask me about any of the 9 Elementor widgets, the Widget Manager, or how to filter posts by category/tag.", 'psea' ),
	'fallback_quick'   => array(
		__( 'What widgets are included?', 'psea' ),
		__( 'How do I filter posts by category?', 'psea' ),
		__( 'Need a custom plugin or theme?', 'psea' ),
		__( 'Need maintenance, SEO, or speed help?', 'psea' ),
	),

	// ── Knowledge base ────────────────────────────────────────────────────
	// Instant, no-AI answers. Add an entry here any time a question keeps
	// getting asked — no server change needed, takes effect immediately.
	'kb' => array(
		array(
			'k' => array( 'what widget', 'which widget', 'list of widget', 'included widget', 'all widget', 'what is this plugin', 'what does this plugin' ),
			'a' => "This plugin adds 9 Elementor widgets: Post Slider, Post Grid, Post Masonry, Post List, Post Category Slider, Recent Viewed Posts, Related Post Slider, Ticker List, and Timeline. Find them under the “Post Slider” category in the Elementor editor's widget panel. 🎠",
		),
		array(
			'k' => array( 'widget manager', 'enable widget', 'disable widget', 'turn off widget', 'turn on widget', 'hide widget' ),
			'a' => "Go to Smart Post Showcase in the WordPress admin menu — every widget has an on/off checkbox there. Disabled widgets aren't loaded at all, which keeps the Elementor editor faster if you only use a few.",
		),
		array(
			'k' => array( 'post slider widget', 'fading slider', 'slider widget' ),
			'a' => "The Post Slider widget shows a full-width fading slider of recent posts, with category/tag filters and its own typography/color style controls.",
		),
		array(
			'k' => array( 'post grid', 'grid widget', 'grid layout' ),
			'a' => "Post Grid shows a responsive multi-column grid (2/3/4 columns) with optional excerpt and category/date meta — great for a magazine-style section.",
		),
		array(
			'k' => array( 'masonry', 'pinterest style' ),
			'a' => "Post Masonry lays posts out Pinterest-style using CSS columns, so uncropped featured images naturally create varying card heights — no JS library needed.",
		),
		array(
			'k' => array( 'post list widget', 'list widget', 'compact list', 'numbered list' ),
			'a' => "Post List is a compact vertical list with small thumbnails — good for sidebars. It has an optional numbered mode (01, 02, 03…).",
		),
		array(
			'k' => array( 'category slider', 'category card', 'browse categories' ),
			'a' => "Post Category Slider shows a sliding row of your post categories as image cards with post counts, each linking to that category's archive.",
		),
		array(
			'k' => array( 'recent viewed', 'recently viewed', 'reading history', 'browsing history' ),
			'a' => "Recent Viewed Posts shows the posts each visitor has personally opened, tracked via a lightweight cookie — no login or external service needed. It's empty until a visitor has actually read a post or two.",
		),
		array(
			'k' => array( 'related post', 'related slider', 'similar post' ),
			'a' => "Related Post Slider shows posts from the same categories as the post currently being viewed — place it on your single-post template. Elsewhere (like the homepage) it shows the latest posts instead.",
		),
		array(
			'k' => array( 'ticker', 'breaking news', 'scrolling headline', 'news ticker' ),
			'a' => "Ticker List is a breaking-news style scrolling headline bar — pure CSS animation, with a customizable label and scroll speed.",
		),
		array(
			'k' => array( 'timeline widget', 'timeline layout' ),
			'a' => "Timeline shows posts on a vertical timeline with date badges, alternating left/right on desktop and stacking on mobile.",
		),
		array(
			'k' => array( 'filter by category', 'filter category', 'select category', 'exclude category', 'filter by tag', 'select tag' ),
			'a' => "Every widget has a Post Filter section: choose categories to include, categories to exclude, tags, and the order (date/title/author/random, ASC/DESC).",
		),
		array(
			'k' => array( 'elementor', 'requires elementor', 'need elementor' ),
			'a' => "Yes — this is an Elementor addon, so the free Elementor page builder plugin must be installed and active. Once it is, all 9 widgets appear under the “Post Slider” category in the editor panel.",
		),
		array(
			'k' => array( 'not showing in editor', 'widget not showing', 'blank in editor', 'not appearing' ),
			'a' => "If a widget you just enabled doesn't appear, fully reload the Elementor editor tab (not just the preview) — the widget panel loads once per editor session. Still missing? Check Smart Post Showcase → make sure it's checked and saved.",
		),
		array(
			'k' => array( 'custom plugin', 'custom theme', 'build me a', 'build a plugin', 'build a theme', 'need a custom', 'custom tool', 'custom feature', 'custom wordpress' ),
			'a' => "Yes! SolverWP's professional development team builds fully custom WordPress plugins and themes — tailored to exactly what you need, coded to WordPress standards, and delivered fast. Tell us your idea at support@solverwp.com and we'll scope it for you. 🛠️",
		),
		array(
			// Includes 2-word combos drawn straight from the quick-reply chip
			// text ("maintenance", "SEO", "speed" all appear together in
			// "Need maintenance, SEO, or speed help?") so this entry scores
			// higher than any single-word keyword elsewhere and reliably wins.
			'k' => array( 'maintenance', 'maintenance plan', 'maintenance seo', 'seo service', 'seo speed', 'speed help', 'speed up my site', 'speed up website', 'website slow', 'optimize my site', 'website maintain', 'improve my seo', 'improve seo' ),
			'a' => "Absolutely — SolverWP offers ongoing website maintenance, SEO optimization, and speed/performance tuning as professional services, so your site stays fast, secure, and ranking well. Email support@solverwp.com with your site URL and goals, and we'll put together a plan. 🚀",
		),
		array(
			// Narrow, intentional phrases only — broad words like "help" or
			// "human" would false-positive on unrelated questions and steal
			// them from the smarter, promotion-aware AI reply below.
			'k' => array( 'contact support', 'contact us', 'talk to a human', 'reach your team', 'human support', 'support forum' ),
			'a' => "Happy to help here! Post in the WordPress.org support forum for Post Slider Elementor Addons, or email our team at support@solverwp.com. 🚀",
		),
	),

);
