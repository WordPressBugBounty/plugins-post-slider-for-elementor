<?php
namespace Elementor;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;

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

    protected function _register_controls() {

  
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
                'type'        => Controls_Manager::TEXT,
                'description' => esc_html__( 'How many post display in slider', 'psea' ),
                'default'     => esc_html__('3','psea')
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
                'scheme'    => Scheme_Typography::TYPOGRAPHY_1,
                'selector'  => '{{WRAPPER}} .skdslider .slide-desc > h2',
                'label'       => esc_html__( 'Title typography', 'psea' ),
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __( 'Title Color', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default'=>'#CFDB0C',
                'scheme' => [
                    'type' => \Elementor\Scheme_Color::get_type(),
                    'value' => \Elementor\Scheme_Color::COLOR_1,
                ],
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
                'scheme'    => Scheme_Typography::TYPOGRAPHY_1,
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
                'scheme' => [
                    'type' => \Elementor\Scheme_Color::get_type(),
                    'value' => \Elementor\Scheme_Color::COLOR_1,
                ],
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
                'scheme'    => Scheme_Typography::TYPOGRAPHY_1,
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
                'scheme' => [
                    'type' => \Elementor\Scheme_Color::get_type(),
                    'value' => \Elementor\Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .skdslider .slide-desc > p a.more' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();


        
    }

    protected function render() {

        $settings = $this->get_settings();

        ?>

              <div id="demo1">
                 <?php  
                      $args  = array(
                        'post_type'           => 'post',
                        'post_status'         => 'publish',
                        'ignore_sticky_posts' => 1,
                        'posts_per_page'      => $settings['ppr'],
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
                            <p><?php echo wp_trim_words( get_the_content(), 30 ) ?><a class="more" href="<?php the_permalink(); ?>"><?php echo esc_html__( ' more', 'psea' ); ?></a></p>
                        </div>
                     </div>
                      <?php endwhile; wp_reset_postdata(); endif; ?>
            </div>
            <script type="text/javascript">
                (function ($) {
              
                     jQuery(document).ready(function(){
                        jQuery('#demo1').skdslider({
                          slideSpseactor: '.slide',
                          delay:5000,
                          animationSpeed:2000,
                          showNextPrev:true,
                          showPlayButton:true,
                          autoSlide:true,
                          animationType:'fading'
                        });
                    });
                })(jQuery);
            </script>
        <?php    
        
    }
    

}

plugin::instance()->widgets_manager->register_widget_type(new Psea_post_slider_widget());
