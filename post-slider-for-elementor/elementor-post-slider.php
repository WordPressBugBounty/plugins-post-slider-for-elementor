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
 * Version:     2.0.0
 * Author:      Solverwp
 * Author URI:  https://facebook.com/quazi.sazzad.7
 * Text Domain: psea
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */


if( !defined( 'ABSPATH' ) ) {
    die;
}


/*
 * Define Plugin Dir Path
 * @since 1.0.0
 * */
define('PSEA_ROOT_PATH',plugin_dir_path(__FILE__));
define('PSEA_ROOT_URL',plugin_dir_url(__FILE__));
define('PSEA_INC',PSEA_ROOT_PATH .'/inc');
define('PSEA_CSS',PSEA_ROOT_URL .'assets/css');
define('PSEA_JS',PSEA_ROOT_URL .'assets/js');
define('PSEA_ELEMENTOR',PSEA_ROOT_PATH .'/elementor');


/** Plugin version **/
define('PSEA_VERSION','1.0.0');



  
/**
 * Load plugin textdomain.
 */
add_action( 'plugins_loaded', 'psea_textdomain' );
if ( ! function_exists( 'psea_textdomain' ) ) {

	function psea_textdomain() {
	   load_plugin_textdomain( 'psea-post-slider', false, plugin_basename( dirname( __FILE__ ) ) . '/language' ); 
	}

}



if ( file_exists( PSEA_ELEMENTOR.'/elementor-widgets-init.php' ) ){
	  require_once PSEA_ELEMENTOR.'/elementor-widgets-init.php';
	}

/*
* enqueue style
*/

add_action( 'wp_enqueue_scripts', 'psea_script', 99 );

function psea_script(){

	wp_enqueue_style( 'psea_css', PSEA_CSS.'/slider-style.css',array(), '1.0.0', 'all');
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'psea_js', PSEA_JS.'/slider-style.js',array( 'jquery' ), '1.0.0', true);

}

add_image_size( 'psea-img', 1900,800, true );




 //select category
 if( !function_exists( 'psea_blog_post_category' ) ) :
    function psea_blog_post_category() {

      $terms = get_terms( array(
        'taxonomy'       => 'category',
        'hide_empty'     => false,
        'posts_per_page' => - 1,
      ) );

      $category_list = [];
      foreach ( $terms as $post ) {
        $category_list[ $post->term_id ] = [ $post->name ];
      }

      return $category_list;

    }
endif;


//select tag

if( !function_exists( 'psea_blog_post_tag' ) ) :
  function psea_blog_post_tag() {

    $terms = get_terms( array(
      'taxonomy'       => 'post_tag',
      'hide_empty'     => false,
      'posts_per_page' => - 1,
    ) );

    $tag_list = [];
    foreach ( $terms as $post ) {
      $tag_list[ $post->term_id ] = [ $post->name ];
    }

    return $tag_list;

  }
endif;

$pseainstallation_date = get_option( 'pseaactive_date' );
$pseatoday_date = date( 'Y-m-d h:i:s' );

$pseainstall_date= new DateTime( $pseainstallation_date );
$pseacurrent_date = new DateTime( $pseatoday_date );
$pseadifference = $pseainstall_date->diff($pseacurrent_date);
$pseadiff_days= $pseadifference->days;



if (isset($pseadiff_days) && $pseadiff_days>=3) {
   add_action( 'admin_notices', 'psea_blog_notice' );
}

//admin notice

function psea_blog_notice() {
    $user_id = get_current_user_id();
    if ( !get_user_meta( $user_id, 'psea_blog_notice_dismissed' ) ) 
        echo '<div class="notice-warning notice"><a style="text-decoration:none;float:right;padding-top:5px;" href="?psea_blog-dismissed">Dismiss</a><p>Dear Elementor Post Slider User, Thank you for using this plugin. We expect a  rating from you.</p> Please <a href="https://wordpress.org/support/plugin/post-slider-for-elementor/reviews/#new-post">Rate Now! ★★★★★ </a>
         <p>Any Question ? Or Need any support related WordPress ? Fell Free To Contact Us at <b>solverwp21@gmail.com</b></p>
      </div>';
}

function psea_blog_notice_dismissed() {
    $user_id = get_current_user_id();
    if ( isset( $_GET['psea_blog-dismissed'] ) )
        add_user_meta( $user_id, 'psea_blog_notice_dismissed', 'true', true );
}
add_action( 'admin_init', 'psea_blog_notice_dismissed' );

