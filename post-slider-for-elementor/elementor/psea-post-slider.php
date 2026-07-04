<?php
namespace Elementor;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;

// Note: Scheme_Color / Scheme_Typography (global color & font "schemes") were
// removed from Elementor years ago in favor of Global Colors/Fonts (Kits).
// Referencing those classes on a current Elementor install fatals, so every
// 'scheme' => [...] block below was removed — the individual color/typography
// controls below still work fine on their own.

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 *
 *  elementor slider widget.
 *
 * @since 1.0
 */
class Psea_post_slider_widget extends Widget_Base {

    public function get_name() {
        return 'psea-section';
    }

    public function get_title() {
        return esc_html__( 'Post Slider', 'psea' );
    }



    public function get_categories() {
        return ['psea'];
    }

    /**
     * Declaring the slider assets as widget dependencies is what makes
     * Elementor load them inside its EDITOR preview iframe (where widgets
     * are rendered over AJAX and the theme's normal frontend enqueue alone
     * is not enough). Handles are registered in psea_register_assets().
     *
     * @since 2.1.1
     */
    public function get_script_depends() {
        return [ 'psea_js' ];
    }

    public function get_style_depends() {
        return [ 'psea_css' ];
    }

    // Elementor renamed _register_controls() to register_controls() in 3.1;
    // the old underscore-prefixed method was removed in later versions.
    protected function register_controls() {

  
        $this->start_controls_section(
            'settings_section',
            [
                'label' => esc_html__( 'General Settings', 'psea' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'ppr',
            [
                'label'       => esc_html__( 'How Many Post Display', 'psea' ),
                // A free TEXT field let this end up empty/non-numeric on some
                // saved widgets, which made the WP_Query below return zero
                // posts (showing as a blank box, especially visible in the
                // Elementor editor). NUMBER with min/max/step guarantees a
                // valid value.
                'type'        => Controls_Manager::NUMBER,
                'description' => esc_html__( 'How many post display in slider', 'psea' ),
                'min'         => 1,
                'max'         => 20,
                'step'        => 1,
                'default'     => 3,
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'Post_filter_settings',
            [
                'label' => esc_html__( 'Post Filter', 'psea' ),
            ]
        );

        $this->add_control(
            'select_cat', [
                'label'    => esc_html__( 'Select Category', 'psea' ),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => psea_blog_post_category(),

            ]
        );


        $this->add_control(
            'exclude_cat', [
                'label'    => esc_html__( 'Exclude Category', 'psea' ),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => psea_blog_post_category(),
            ]
        );

        $this->add_control(
            'select_tag', [
                'label'    => esc_html__( 'Select tag', 'psea' ),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => psea_blog_post_tag(),
            ]
        );

        $this->add_control(
            'orderby', [
                'label'   => esc_html__( 'Order by', 'psea' ),
                'type'    => Controls_Manager::SELECT2,
                'options' => array(
                    'author' => esc_html__( 'Author', 'psea' ),
                    'title'  => esc_html__( 'Title', 'psea' ),
                    'date'   => esc_html__( 'Date', 'psea' ),
                    'rand'   => esc_html__( 'Random', 'psea' ),
                ),
                'default' => 'date'

            ]
        );

        $this->add_control(
            'order', [
                'label'   => esc_html__( 'Order', 'psea' ),
                'type'    => Controls_Manager::SELECT2,
                'options' => array(
                    'desc' => esc_html__( 'DESC', 'psea' ),
                    'asc'  => esc_html__( 'ASC', 'psea' ),
                ),
                'default' => 'desc'

            ]
        );

  
        $this->end_controls_section(); // End Contact content

        $this->start_controls_section(
            'service',
            [
                'label' => esc_html__( 'Service', 'psea' ),
            ]
        );

        $this->add_control(
            'warning_text',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' =>  __('<p style="line-height: 20px;">Do you need any WordPress Service like Speed Up website, Custom theme or plugin development contact us at </p>
                 <strong style="color:Green;font-size:18px">solverwp21@gmail.com </strong><br/><br/><a target="_blank" href="https://1.envato.market/mgODEX">Check Our premium product </a>', 'psea'), array( '<strong>' ),
                'content_classes' => 'warining',
            ]
        );


        $this->end_controls_section(); // End Contact content

        /*
         * start style section
        */
        $this->start_controls_section(
            'style_section',
            [
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => __( 'Style', 'psea' ),
            ]
        );

        // title typography.
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'title_typography',
                'selector'  => '{{WRAPPER}} .skdslider .slide-desc > h2',
                'label'       => esc_html__( 'Title typography', 'psea' ),
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __( 'Title Color', 'psea' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default'=>'#CFDB0C',
                'selectors' => [
                    '{{WRAPPER}} .skdslider .slide-desc > h2' => 'color: {{VALUE}}',
                ],
            ]
        );

          // content
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'content_typography',
                'selector'  => '{{WRAPPER}} .skdslider .slide-desc > p',
                'label'       => esc_html__( 'Content typography', 'psea' ),
            ]
        );

          $this->add_control(
            'content_color',
            [
                'label' => __( 'Content Color', 'psea' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default'=>'#fff',
                'selectors' => [
                    '{{WRAPPER}} .skdslider .slide-desc > p' => 'color: {{VALUE}}',
                ],
            ]
        );

        // read more
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'read_more_typography',
                'selector'  => '{{WRAPPER}} .skdslider .slide-desc > p a.more',
                'label'       => esc_html__( 'Read More typography', 'psea' ),
            ]
        );


          $this->add_control(
            'read_more_color',
            [
                'label' => __( 'Read More Color', 'psea' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default'=>'#fff',
                'selectors' => [
                    '{{WRAPPER}} .skdslider .slide-desc > p a.more' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();


        
    }

    protected function render() {

        $settings = $this->get_settings();

        // Guard against an empty/invalid value even though the control is
        // now type NUMBER, so a widget saved before this update (when the
        // field was free TEXT) can't send WP_Query an unusable value.
        $posts_per_page = ! empty( $settings['ppr'] ) ? absint( $settings['ppr'] ) : 3;

        // Unique per-widget id (the old hardcoded #demo1 broke pages with two
        // sliders, and collided across editor re-renders).
        $slider_id = 'psea-slider-' . $this->get_id();

        ?>

              <div id="<?php echo esc_attr( $slider_id ); ?>">
                 <?php
                      $args  = array(
                        'post_type'           => 'post',
                        'post_status'         => 'publish',
                        'ignore_sticky_posts' => 1,
                        'posts_per_page'      => $posts_per_page,
                    );

                    $args['orderby'] = $settings['orderby'];
                    $args['order']   = $settings['order'];
                    if ( ! empty( $settings['exclude_cat'] ) ) {
                        $args['category__not_in'] = $settings['exclude_cat'];
                    }


                    if ( ! empty( $settings['select_cat'] ) ) {
                        $args['tax_query'][] = array(
                            'taxonomy' => 'category',
                            'field'    => 'id',
                            'terms'    => array_values( $settings['select_cat'] )
                        );
                    }

                    if ( ! empty( $settings['select_tag'] ) ) {
                        $args['tax_query'][] = array(
                            'taxonomy' => 'post_tag',
                            'field'    => 'id',
                            'terms'    => array_values( $settings['select_tag'] )
                        );
                    }

                      $posts_query = new \WP_Query( $args );
                        if ( $posts_query->have_posts() ) :
                         while ( $posts_query->have_posts()) : $posts_query->the_post();
                    ?>
                    <div class="slide">
                        <?php the_post_thumbnail( 'psea-img' ); ?>
                        <!--Slider Description example-->
                         <div class="slide-desc">
                            <h2><?php the_title(); ?></h2>
                            <?php /* get_the_excerpt() instead of get_the_content(): calling get_the_content()
                                     (which runs the "the_content" filter) from inside a widget that Elementor
                                     is itself rendering as part of "the_content" for this same page can hit
                                     Elementor's own re-entrancy guard and silently return an empty string —
                                     most noticeable in the editor preview, which is always mid-render. */ ?>
                            <p><?php echo wp_trim_words( get_the_excerpt(), 30 ) ?><a class="more" href="<?php the_permalink(); ?>"><?php echo esc_html__( ' more', 'psea' ); ?></a></p>
                        </div>
                     </div>
                      <?php
                      endwhile;
                      wp_reset_postdata();
                      else :
                          // Never show a mysterious blank box — this is the
                          // one thing that would previously leave the widget
                          // looking broken (especially in the editor) when the
                          // filters matched no posts.
                          ?>
                          <p class="psea-no-posts"><?php echo esc_html__( 'No posts found for the current filters. Try a different category or tag.', 'psea' ); ?></p>
                          <?php
                      endif;
                      ?>
            </div>
            <script type="text/javascript">
                (function ($) {
                    var initSlider = function () {
                        var $el = $('#<?php echo esc_js( $slider_id ); ?>');
                        // Skip when there are no slides (skdslider() throws
                        // otherwise) or when this instance is already wrapped
                        // in .skdslider (already initialized — matters in the
                        // Elementor editor, which re-runs widget scripts).
                        if ( ! $el.length || ! $el.find('.slide').length || $el.parent().hasClass('skdslider') ) {
                            return;
                        }
                        if ( typeof $.fn.skdslider !== 'function' ) {
                            return; // slider script not loaded (declared via get_script_depends()).
                        }
                        $el.skdslider({
                          slideSelector: '.slide',
                          delay:5000,
                          animationSpeed:2000,
                          showNextPrev:true,
                          showPlayButton:true,
                          autoSlide:true,
                          animationType:'fading'
                        });
                    };

                    // In the Elementor editor the widget markup is injected
                    // over AJAX long after document.ready — but this inline
                    // script re-runs on every editor render, so calling
                    // initSlider() directly covers the editor, while $(ready)
                    // covers the normal frontend load.
                    if ( window.elementorFrontend && window.elementorFrontend.isEditMode && window.elementorFrontend.isEditMode() ) {
                        initSlider();
                    } else {
                        $( initSlider );
                    }
                })(jQuery);
            </script>
        <?php
        
    }
    

}

// Registration now happens in elementor-widgets-init.php via the
// "elementor/widgets/register" hook — register_widget_type() was removed
// from current Elementor versions, so this file only defines the class.
