<?php

/**
 * Psea
 *
 * @package     Psea
 * @author      Solverwp
 * @license     GPL-2.0-or-later
 *
 * Plugin Name:  Post slider Elementor addons
 * Plugin URI:  https://solverwp.com/
 * Description: Post Elementor addon to display recent post.
 * Version:     2.2.1
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Tested up to: 7.0
 * Elementor tested up to: 3.26.0
 * Author:      Solverwp
 * Author URI:  https://facebook.com/quazi.sazzad.7
 * Text Domain: psea
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */


if (!defined('ABSPATH')) {
	die;
}


/*
 * Define Plugin Dir Path
 * @since 1.0.0
 * */
define('PSEA_ROOT_PATH', plugin_dir_path(__FILE__));
define('PSEA_ROOT_URL', plugin_dir_url(__FILE__));
define('PSEA_INC', PSEA_ROOT_PATH . '/inc');
define('PSEA_CSS', PSEA_ROOT_URL . 'assets/css');
define('PSEA_JS', PSEA_ROOT_URL . 'assets/js');
define('PSEA_ELEMENTOR', PSEA_ROOT_PATH . '/elementor');


/** Plugin version **/
define('PSEA_VERSION', '2.2.0');

/*
 * Flush Elementor's own file/CSS cache whenever this plugin's version
 * changes (e.g. new widgets were just added, like the 2.2.0 pack). Without
 * this, an Elementor editor session opened before the update can keep
 * showing its old, cached widget/asset list until the user manually clears
 * the cache or fully reloads the editor.
 * @since 2.2.0
 * */
add_action('plugins_loaded', 'psea_maybe_flush_elementor_cache', 20);
if (! function_exists('psea_maybe_flush_elementor_cache')) {
	function psea_maybe_flush_elementor_cache()
	{
		if (get_option('psea_version') === PSEA_VERSION) {
			return;
		}
		update_option('psea_version', PSEA_VERSION);

		if (class_exists('\Elementor\Plugin') && isset(\Elementor\Plugin::$instance->files_manager)) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
	}
}




/**
 * Load plugin textdomain.
 */
add_action('plugins_loaded', 'psea_textdomain');
if (! function_exists('psea_textdomain')) {

	function psea_textdomain()
	{
		load_plugin_textdomain('psea-post-slider', false, plugin_basename(dirname(__FILE__)) . '/language');
	}
}



/*
 * Widget registry — the single source of truth for every widget in this
 * addon pack. The Widget Manager settings page renders its toggles from
 * this list, and elementor-widgets-init.php registers only the enabled
 * ones. Adding a new widget = one entry here + one file in /elementor.
 * @since 2.2.0
 * */
function psea_widget_registry()
{
	return array(
		'post-slider' => array(
			'title' => esc_html__('Post Slider', 'psea'),
			'desc'  => esc_html__('Full-width fading slider of recent posts.', 'psea'),
			'file'  => 'psea-post-slider.php',
			'class' => '\Elementor\Psea_post_slider_widget',
		),
		'post-grid' => array(
			'title' => esc_html__('Post Grid', 'psea'),
			'desc'  => esc_html__('Responsive multi-column grid of posts.', 'psea'),
			'file'  => 'psea-post-grid.php',
			'class' => 'Psea_Post_Grid_Widget',
		),
		'post-masonry' => array(
			'title' => esc_html__('Post Masonry', 'psea'),
			'desc'  => esc_html__('Pinterest-style masonry layout of posts.', 'psea'),
			'file'  => 'psea-post-masonry.php',
			'class' => 'Psea_Post_Masonry_Widget',
		),
		'post-list' => array(
			'title' => esc_html__('Post List', 'psea'),
			'desc'  => esc_html__('Compact vertical list with small thumbnails.', 'psea'),
			'file'  => 'psea-post-list.php',
			'class' => 'Psea_Post_List_Widget',
		),
		'category-slider' => array(
			'title' => esc_html__('Post Category Slider', 'psea'),
			'desc'  => esc_html__('Sliding cards of post categories with counts.', 'psea'),
			'file'  => 'psea-category-slider.php',
			'class' => 'Psea_Category_Slider_Widget',
		),
		'recent-viewed' => array(
			'title' => esc_html__('Recent Viewed Posts', 'psea'),
			'desc'  => esc_html__('Posts the visitor recently opened (cookie based).', 'psea'),
			'file'  => 'psea-recent-viewed.php',
			'class' => 'Psea_Recent_Viewed_Widget',
		),
		'related-slider' => array(
			'title' => esc_html__('Related Post Slider', 'psea'),
			'desc'  => esc_html__('Slider of posts sharing the current post\'s categories.', 'psea'),
			'file'  => 'psea-related-slider.php',
			'class' => 'Psea_Related_Slider_Widget',
		),
		'ticker' => array(
			'title' => esc_html__('Ticker List', 'psea'),
			'desc'  => esc_html__('Breaking-news style scrolling headline ticker.', 'psea'),
			'file'  => 'psea-ticker.php',
			'class' => 'Psea_Ticker_Widget',
		),
		'timeline' => array(
			'title' => esc_html__('Timeline', 'psea'),
			'desc'  => esc_html__('Vertical timeline of posts with date badges.', 'psea'),
			'file'  => 'psea-timeline.php',
			'class' => 'Psea_Timeline_Widget',
		),
	);
}

if (file_exists(PSEA_INC . '/class-psea-settings.php')) {
	require_once PSEA_INC . '/class-psea-settings.php';
}

if (file_exists(PSEA_INC . '/class-psea-help-chatbot.php')) {
	require_once PSEA_INC . '/class-psea-help-chatbot.php';
}

if (file_exists(PSEA_ELEMENTOR . '/elementor-widgets-init.php')) {
	require_once PSEA_ELEMENTOR . '/elementor-widgets-init.php';
}

/*
* Register + enqueue assets.
*
* Registered (not only enqueued) so the widget can declare them via
* get_script_depends()/get_style_depends() — that is how Elementor knows to
* load them inside its EDITOR preview iframe too, where widgets are rendered
* over AJAX. Registration runs early (priority 5) so the handles exist before
* Elementor resolves widget dependencies.
*/

add_action('wp_enqueue_scripts', 'psea_register_assets', 5);

function psea_register_assets()
{

	wp_register_style('psea_css', PSEA_CSS . '/slider-style.css', array(), PSEA_VERSION, 'all');
	wp_register_script('psea_js', PSEA_JS . '/slider-style.js', array('jquery'), PSEA_VERSION, true);

	// Shared styles/behaviour for the 2.2.0 widget pack (grid, masonry,
	// list, carousels, ticker, timeline). Declared per-widget through
	// get_style_depends()/get_script_depends().
	wp_register_style('psea_widgets_css', PSEA_CSS . '/widgets.css', array(), PSEA_VERSION, 'all');
	wp_register_script('psea_widgets_js', PSEA_JS . '/widgets.js', array(), PSEA_VERSION, true);
}

add_action('wp_enqueue_scripts', 'psea_script', 99);

function psea_script()
{

	wp_enqueue_style('psea_css');
	wp_enqueue_script('psea_js');
	wp_enqueue_style('psea_widgets_css');
	wp_enqueue_script('psea_widgets_js');
}

add_image_size('psea-img', 1900, 800, true);

/*
 * Track the posts a visitor opens, for the "Recent Viewed Posts" widget.
 * Stored in a simple comma-separated cookie (newest first, max 12) —
 * template_redirect runs before any output so setcookie() is safe here.
 * @since 2.2.0
 * */
add_action('template_redirect', 'psea_track_recently_viewed');
if (! function_exists('psea_track_recently_viewed')) {
	function psea_track_recently_viewed()
	{
		if (! is_singular('post')) {
			return;
		}
		$id = get_queried_object_id();
		if (! $id) {
			return;
		}

		$viewed = array();
		if (isset($_COOKIE['psea_recently_viewed'])) {
			$viewed = array_filter(array_map('absint', explode(',', sanitize_text_field(wp_unslash($_COOKIE['psea_recently_viewed'])))));
		}
		$viewed = array_values(array_diff($viewed, array($id)));
		array_unshift($viewed, $id);
		$viewed = array_slice($viewed, 0, 12);

		setcookie('psea_recently_viewed', implode(',', $viewed), time() + MONTH_IN_SECONDS, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN);
	}
}




//select category
if (!function_exists('psea_blog_post_category')) :
	function psea_blog_post_category()
	{

		$terms = get_terms(array(
			'taxonomy'       => 'category',
			'hide_empty'     => false,
			'number'         => 0,
		));

		$category_list = [];
		foreach ($terms as $post) {
			$category_list[$post->term_id] = [$post->name];
		}

		return $category_list;
	}
endif;


//select tag

if (!function_exists('psea_blog_post_tag')) :
	function psea_blog_post_tag()
	{

		$terms = get_terms(array(
			'taxonomy'       => 'post_tag',
			'hide_empty'     => false,
			'number'         => 0,
		));

		$tag_list = [];
		foreach ($terms as $post) {
			$tag_list[$post->term_id] = [$post->name];
		}

		return $tag_list;
	}
endif;

/*
 * Record the first-activation date so the review-nudge notice can wait a few
 * days before appearing. register_activation_hook() only fires on activation,
 * so sites that activated before this existed still get it set on their next
 * plugin load below.
 * @since 2.1.0
 * */
register_activation_hook(__FILE__, 'psea_set_activation_date');
if (! function_exists('psea_set_activation_date')) {
	function psea_set_activation_date()
	{
		if (! get_option('pseaactive_date')) {
			add_option('pseaactive_date', current_time('mysql'));
		}
	}
}
// Backfill for sites already active before this option existed.
if (! get_option('pseaactive_date')) {
	add_option('pseaactive_date', current_time('mysql'));
}

$pseadiff_days          = 0;
$pseainstallation_date  = get_option('pseaactive_date');

if ($pseainstallation_date) {
	try {
		$pseainstall_date = new DateTime($pseainstallation_date);
		$pseacurrent_date = new DateTime(current_time('mysql'));
		$pseadiff_days    = $pseainstall_date->diff($pseacurrent_date)->days;
	} catch (Exception $e) {
		$pseadiff_days = 0;
	}
}

if ($pseadiff_days >= 3) {
	add_action('admin_notices', 'psea_blog_notice');
}

//admin notice

function psea_blog_notice()
{
	$user_id = get_current_user_id();
	if (!get_user_meta($user_id, 'psea_blog_notice_dismissed'))
		echo '<div class="notice-warning notice"><a style="text-decoration:none;float:right;padding-top:5px;" href="?psea_blog-dismissed">Dismiss</a><p>Dear Elementor Post Slider User, Thank you for using this plugin. We expect a  rating from you.</p> Please <a href="https://wordpress.org/support/plugin/post-slider-for-elementor/reviews/#new-post">Rate Now! ★★★★★ </a>
         <p>Any Question ? Or Need any support related WordPress ? Fell Free To Contact Us at <b>solverwp21@gmail.com</b></p>
      </div>';
}

function psea_blog_notice_dismissed()
{
	$user_id = get_current_user_id();
	if (isset($_GET['psea_blog-dismissed']))
		add_user_meta($user_id, 'psea_blog_notice_dismissed', 'true', true);
}
add_action('admin_init', 'psea_blog_notice_dismissed');
